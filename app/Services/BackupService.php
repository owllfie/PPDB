<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class BackupService
{
    public function generateSqlDump(): string
    {
        $tables = DB::select('SHOW TABLES');
        $database = config('database.connections.mysql.database');
        $key = "Tables_in_{$database}";
        $sql = "-- Database Backup: {$database}\n";
        $sql .= "-- Generated: " . now()->toDateTimeString() . "\n";
        $sql .= "-- -----------------------------------------------\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$key;

            $createResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createSql = $createResult[0]->{'Create Table'};
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createSql . ";\n\n";

            $rows = DB::select("SELECT * FROM `{$tableName}`");
            foreach ($rows as $row) {
                $values = collect((array) $row)->map(function ($value) {
                    if (is_null($value)) {
                        return 'NULL';
                    }
                    return "'" . addslashes($value) . "'";
                })->implode(', ');

                $sql .= "INSERT INTO `{$tableName}` VALUES ({$values});\n";
            }

            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $sql;
    }
}
