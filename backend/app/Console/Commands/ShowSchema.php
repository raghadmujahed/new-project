<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowSchema extends Command
{
    protected $signature = 'app:show-schema';
    protected $description = 'Show all database tables and columns';

    public function handle()
    {
        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $t) {
            $table = array_values((array)$t)[0];

            $this->info("\nTABLE: $table");

            $columns = DB::select("DESCRIBE `$table`");

            foreach ($columns as $c) {
                $this->line($c->Field . " | " . $c->Type);
            }
        }
    }
}