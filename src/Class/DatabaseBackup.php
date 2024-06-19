<?php
namespace Codedwebltd\Backups\Class;

use PDO;
use PDOException;
use Exception;

class DatabaseBackup{
    public function __construct()
    {
        
    }

    public function onDatabaseBackupListener($dbHost, $dbName, $dbUser, $dbPassword, $backupDir, $timestamp)
    {

        $backupFilename = "db_backup_{$dbName}_{$timestamp}.sql";
    $backupFilePath = "{$backupDir}/{$backupFilename}";

    try {
        $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName}", $dbUser, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $sqlDump = '';

        foreach ($tables as $table) {
            $createTableStmt = $pdo->query("SHOW CREATE TABLE {$table}")->fetch(PDO::FETCH_ASSOC);
            $sqlDump .= $createTableStmt['Create Table'] . ";\n\n";

            $rows = $pdo->query("SELECT * FROM {$table}")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $columns = array_map(function ($col) use ($pdo) {
                    return $pdo->quote($col);
                }, array_values($row));
                $sqlDump .= "INSERT INTO {$table} VALUES (" . implode(", ", $columns) . ");\n";
            }
            $sqlDump .= "\n\n";
        }

        file_put_contents($backupFilePath, $sqlDump);
    } catch (PDOException $e) {
        throw new Exception("Database backup failed: " . $e->getMessage());
    }

    return $backupFilePath;
    }
}