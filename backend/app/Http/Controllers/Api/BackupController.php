<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBackupRequest;
use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\ActivityLogger;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Backup::class, 'backup');
    }

    /**
     * عرض قائمة النسخ الاحتياطية
     */
    public function index(Request $request)
    {
        $backups = Backup::with('user')->latest()->paginate($request->per_page ?? 15);
        return response()->json($backups);
    }

    /**
     * إنشاء نسخة احتياطية جديدة
     */
    public function store(CreateBackupRequest $request)
    {
        $tables = DB::select('SHOW TABLES');
        $data = [];

        foreach ($tables as $table) {
            $tableName = reset($table);
            $data[$tableName] = DB::table($tableName)->get();
        }

        $filename = 'backup_' . date('Ymd_His') . '_' . Str::random(8) . '.json';
        $filepath = 'backups/' . $filename;

        Storage::put($filepath, json_encode($data, JSON_PRETTY_PRINT));

        $backup = Backup::create([
            'user_id' => $request->user()->id,
            'type' => $request->type ?? 'full',
            'name' => $filename,
            'file_path' => $filepath,
            'size' => Storage::size($filepath),
            'status' => 'completed',
            'notes' => $request->notes,
        ]);

        ActivityLogger::log(
            'backup',
            'created',
            'Backup created',
            $backup,
            [
                'backup_id' => $backup->id,
                'backup_name' => $filename,
            ],
            $request->user()
        );

        return response()->json($backup, 201);
    }

    /**
     * عرض تفاصيل النسخة
     */
    public function show($id)
    {
        $backup = Backup::findOrFail($id);
        $tables = [];

        if ($backup->file_path && Storage::exists($backup->file_path)) {
            $content = json_decode(Storage::get($backup->file_path), true);

            if (is_array($content)) {
                foreach ($content as $tableName => $rows) {
                    $tables[] = [
                        'name' => $tableName,
                        'count' => is_array($rows) ? count($rows) : 0,
                    ];
                }
            }
        }

        return response()->json([
            'id' => $backup->id,
            'name' => $backup->name,
            'created_at' => $backup->created_at,
            'size' => $backup->size,
            'type' => $backup->type,
            'notes' => $backup->notes,
            'tables' => $tables,
        ]);
    }

    /**
     * بيانات جدول داخل النسخة
     */
    public function getTableData($id, $tableName)
    {
        $backup = Backup::findOrFail($id);

        if (!$backup->file_path || !Storage::exists($backup->file_path)) {
            return response()->json(['message' => 'الملف غير موجود'], 404);
        }

        $content = json_decode(Storage::get($backup->file_path), true);
        $tableData = $content[$tableName] ?? [];

        return response()->json([
            'data' => $tableData,
            'count' => count($tableData),
        ]);
    }

    /**
     * حذف نسخة احتياطية
     */
    public function destroy(Backup $backup)
    {
        Storage::delete($backup->file_path);
        $backup->delete();

        ActivityLogger::log(
            'backup',
            'deleted',
            'Backup deleted',
            null,
            [
                'backup_id' => $backup->id,
                'backup_name' => $backup->name,
            ],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف النسخة الاحتياطية']);
    }

    /**
     * استعادة النسخة
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_id' => 'required|exists:backups,id',
        ]);

        $backup = Backup::findOrFail($request->backup_id);

        $filePath = storage_path('app/' . $backup->file_path);

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'ملف النسخة غير موجود'], 404);
        }

        $data = json_decode(file_get_contents($filePath), true);

        DB::beginTransaction();

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            foreach ($data as $tableName => $rows) {
                DB::table($tableName)->truncate();

                foreach ($rows as $row) {
                    DB::table($tableName)->insert((array)$row);
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            DB::commit();

            ActivityLogger::log(
                'backup',
                'restored',
                'Backup restored',
                $backup,
                [
                    'backup_id' => $backup->id,
                    'backup_name' => $backup->name,
                ],
                auth()->user()
            );

            return response()->json(['message' => 'تم استعادة النسخة بنجاح']);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'فشل الاستعادة',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}