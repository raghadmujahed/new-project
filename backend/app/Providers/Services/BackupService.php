<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Facades\File;

class BackupService
{
    public function createDatabaseBackup()
    {
        $fileName = 'backup_' . date('Y_m_d_H_i_s') . '.sql';
        $path = storage_path('app/backups/' . $fileName);

        if (!File::exists(storage_path('app/backups'))) {
            File::makeDirectory(storage_path('app/backups'), 0755, true);
        }

        $db = config('database.connections.mysql');

        $command = sprintf(
            'mysqldump -u%s -p%s %s > %s',
            $db['username'],
            $db['password'],
            $db['database'],
            $path
        );

        system($command);

        if (file_exists($path)) {
            return Backup::create([
                'user_id' => auth()->id(),
                'type' => 'database',
                'name' => $fileName,
                'file_path' => $path,
                'size' => filesize($path),
                'status' => 'success',
            ]);
        }

        return null;
    }
} 
