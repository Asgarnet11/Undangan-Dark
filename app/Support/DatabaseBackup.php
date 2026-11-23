<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseBackup
{
    /**
     * Download backup of core wedding invitation tables as an SQL dump stream.
     */
    public static function download(): StreamedResponse
    {
        $filename = 'backup-undangan-'.now()->format('Ymd-His').'.sql';

        $tables = [
            'users',
            'invitations',
            'events',
            'gallery_photos',
            'gift_accounts',
            'gift_addresses',
            'co_inviters',
            'guests',
            'rsvps',
            'wishes',
            'gift_confirmations',
        ];

        return response()->streamDownload(function () use ($tables) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "-- Backup Database Undangan Digital\n");
            fwrite($handle, '-- Tanggal: '.now()->toIso8601String()."\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            foreach ($tables as $table) {
                if (! Schema::hasTable($table)) {
                    continue;
                }

                $rows = DB::table($table)->get();
                fwrite($handle, "-- Data tabel: {$table} ({$rows->count()} baris)\n");

                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $columns = array_map(fn ($col) => "`{$col}`", array_keys($rowArray));
                    $values = array_map(function ($val) {
                        if ($val === null) {
                            return 'NULL';
                        }

                        return "'".addslashes((string) $val)."'";
                    }, array_values($rowArray));

                    $sql = "INSERT INTO `{$table}` (".implode(', ', $columns).') VALUES ('.implode(', ', $values).");\n";
                    fwrite($handle, $sql);
                }

                fwrite($handle, "\n");
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }
}
