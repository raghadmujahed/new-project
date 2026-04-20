<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBackupRequest;
use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * إنشاء نسخة احتياطية جديدة (بتنسيق JSON)
     */
    public function store(CreateBackupRequest $request)
    {
        // جلب جميع الجداول من قاعدة البيانات
        $tables = DB::select('SHOW TABLES');
        $data = [];
        
        foreach ($tables as $table) {
            $tableName = reset($table); // اسم الجدول
            $data[$tableName] = DB::table($tableName)->get();
        }
        
        // توليد اسم الملف وحفظه
        $filename = 'backup_' . date('Ymd_His') . '_' . Str::random(8) . '.json';
        $filepath = 'backups/' . $filename;
        Storage::put($filepath, json_encode($data, JSON_PRETTY_PRINT));
        
        // إنشاء سجل في قاعدة البيانات
        $backup = Backup::create([
            'user_id' => $request->user()->id,
            'type' => $request->type ?? 'full',
            'name' => $filename,
            'file_path' => $filepath,
            'size' => Storage::size($filepath),
            'status' => 'completed',
            'notes' => $request->notes,
        ]);

        
        return response()->json($backup, 201);
    }

    /**
     * عرض تفاصيل النسخة الاحتياطية (معلومات عامة + قائمة الجداول مع عدد السجلات)
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
     * عرض بيانات جدول محدد من داخل النسخة الاحتياطية
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
     * حذف نسخة احتياطية (ملف + سجل)
     */
    public function destroy(Backup $backup)
    {
        Storage::delete($backup->file_path);
        $backup->delete();
        return response()->json(['message' => 'تم حذف النسخة الاحتياطية']);
    }

    /**
     * استعادة نسخة احتياطية (استبدال قاعدة البيانات الحالية بالكامل)
     * ملاحظة: هذه العملية خطيرة ويجب تقييدها للمسؤول فقط
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
        
        // قراءة البيانات من ملف JSON
        $data = json_decode(file_get_contents($filePath), true);
        
        // بدء معاملة (transaction) لضمان سلامة البيانات
        DB::beginTransaction();
        
        try {
            foreach ($data as $tableName => $rows) {
                // حذف جميع السجلات الحالية من الجدول
                DB::table($tableName)->truncate();
                
                // إدراج السجلات الجديدة
                foreach ($rows as $row) {
                    // تحويل الكائن إلى مصفوفة (إذا كان من نوع stdClass)
                    $rowArray = (array) $row;
                    DB::table($tableName)->insert($rowArray);
                }
            }
            
            DB::commit();
            return response()->json(['message' => 'تم استعادة النسخة الاحتياطية بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'فشل الاستعادة: ' . $e->getMessage()], 500);
        }
    }
}