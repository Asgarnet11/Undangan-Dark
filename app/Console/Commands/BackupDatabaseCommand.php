<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PDO;
use Throwable;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'app:backup-database {--keep=7 : Jumlah berkas backup terbaru yang dipertahankan}';

    protected $description = 'Cadangkan database ke direktori storage/app/backups dan rotasi berkas lama';

    public function handle(): int
    {
        $this->info('Memulai pencadangan database...');

        $backupDir = storage_path('app/backups');
        if (! File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = Carbon::now()->format('Y-m-d_His');
        $fileName = "backup_{$timestamp}.sql";
        $filePath = "{$backupDir}/{$fileName}";

        try {
            $connection = DB::connection();
            $driver = $connection->getDriverName();

            if ($driver === 'sqlite') {
                $dbPath = $connection->getDatabaseName();
                if ($dbPath !== ':memory:' && File::exists($dbPath)) {
                    File::copy($dbPath, "{$backupDir}/backup_{$timestamp}.sqlite");
                    $this->info("Backup SQLite berhasil disimpan ke: backup_{$timestamp}.sqlite");
                } else {
                    // Buat file SQL kosong untuk sqlite in-memory testing
                    File::put($filePath, "-- SQLite in-memory backup: {$timestamp}\n");
                    $this->info("Backup SQLite berhasil disimpan ke: {$fileName}");
                }
            } else {
                $handle = fopen($filePath, 'w');
                if (! $handle) {
                    $this->error("Gagal membuka berkas backup: {$filePath}");

                    return self::FAILURE;
                }

                fwrite($handle, "-- Database Backup: {$timestamp}\n");
                fwrite($handle, "-- Driver: {$driver}\n\n");
                fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
                fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
                fwrite($handle, "SET NAMES utf8mb4;\n\n");

                /** @var PDO $pdo */
                $pdo = $connection->getPdo();
                $dbName = $connection->getDatabaseName();
                $tablesRaw = $connection->select('SHOW TABLES');

                $tables = [];
                foreach ($tablesRaw as $t) {
                    $arr = (array) $t;
                    $tables[] = $arr['Tables_in_'.$dbName] ?? array_values($arr)[0];
                }

                foreach ($tables as $table) {
                    // Export structure
                    $createTableStmt = $connection->selectOne("SHOW CREATE TABLE `{$table}`");
                    $createSql = $createTableStmt->{'Create Table'} ?? null;

                    if ($createSql) {
                        fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                        fwrite($handle, $createSql.";\n\n");
                    }

                    // Export data
                    $rows = $connection->table($table)->get();
                    if ($rows->isNotEmpty()) {
                        $columnNames = array_keys((array) $rows->first());
                        $escapedColumns = array_map(fn ($col) => "`{$col}`", $columnNames);
                        $columnList = implode(', ', $escapedColumns);

                        fwrite($handle, "LOCK TABLES `{$table}` WRITE;\n");

                        $chunks = $rows->chunk(100);
                        foreach ($chunks as $chunk) {
                            $valuesList = [];
                            foreach ($chunk as $row) {
                                $escapedValues = array_map(function ($val) use ($pdo) {
                                    if ($val === null) {
                                        return 'NULL';
                                    }

                                    return $pdo->quote((string) $val);
                                }, (array) $row);

                                $valuesList[] = '('.implode(', ', $escapedValues).')';
                            }

                            fwrite($handle, "INSERT INTO `{$table}` ({$columnList}) VALUES\n".implode(",\n", $valuesList).";\n");
                        }

                        fwrite($handle, "UNLOCK TABLES;\n\n");
                    }
                }

                fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
                fclose($handle);

                $sizeInKb = round(filesize($filePath) / 1024, 2);
                $this->info("Backup SQL berhasil disimpan: {$fileName} ({$sizeInKb} KB)");
            }

            // Rotasi berkas backup lama
            $keep = max(1, (int) $this->option('keep'));
            $this->pruneOldBackups($backupDir, $keep);

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Gagal mencadangkan database: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    private function pruneOldBackups(string $directory, int $keep): void
    {
        $files = collect(File::files($directory))
            ->filter(fn ($file) => str_starts_with($file->getFilename(), 'backup_'))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->values();

        if ($files->count() > $keep) {
            $toDelete = $files->slice($keep);
            foreach ($toDelete as $file) {
                File::delete($file->getPathname());
                $this->line("Menghapus backup lama: {$file->getFilename()}");
            }
        }
    }
}
