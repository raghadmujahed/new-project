<?php
use Illuminate\Support\Facades\DB;

Route::get('/db-debug', function () {

    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");

    foreach ($tables as $table) {
        echo "<h3>TABLE: {$table->name}</h3>";

        $columns = DB::select("PRAGMA table_info('{$table->name}')");

        foreach ($columns as $col) {
            echo $col->name . " | " . $col->type . "<br>";
        }

        echo "<hr>";
    }
});