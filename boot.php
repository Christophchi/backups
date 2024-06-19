<?php
// Include Composer's autoloader
require './vendor/autoload.php';
require './src/Class/AddFilesToZip.php';
require './src/Class/DatabaseBackup.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Codedwebltd\Backups\Class\DatabaseBackup;

//import classes
use Dotenv\Dotenv;

//initialize the enviroment variables.
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Define the directory to be backed up using a relative path
$dirToBackup = realpath(__DIR__ . '/../../' . $_ENV["DIR_TO_BACKUP"]);

// Define the backup directory
$backupDir = __DIR__;  // In this example, we'll save the backup in the current directory

$databasebackupClass = new DatabaseBackup();

function backupDatabase($dbHost, $dbName, $dbUser, $dbPassword, $backupDir, $timestamp)
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

// Initialize ZIP archive object
$zip = new ZipArchive();

$timestamp = date('Y_m_d_H_i_s');
$combinedBackupFilename = $_ENV['BACKUP_PREFIX'] . "_{$timestamp}.zip";
$combinedBackupFilePath = "{$backupDir}/{$combinedBackupFilename}";

try {
    if ($zip->open($combinedBackupFilePath, ZipArchive::CREATE) !== TRUE) {
        throw new Exception("Cannot open <$combinedBackupFilePath>");
    }


    function addFilesToZip($dir, $zip, $baseDirLength)
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $name => $file) {

            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, $baseDirLength);

            if ($file->isDir()) {

                $zip->addEmptyDir($relativePath);
            } else {

                $zip->addFile($filePath, $relativePath);
                $index = $zip->numFiles - 1;
                $zip->setCompressionIndex($index, ZipArchive::CM_DEFAULT);
            }
        }
    }

    // Database backup configuration
    $dbHost = $_ENV['DB_HOST']; // Replace with your database host
    $dbName = $_ENV['DB_DATABASE']; // Replace with your database name
    $dbUser = $_ENV['DB_USERNAME']; // Replace with your database username
    $dbPassword = $_ENV['DB_PASSWORD']; // Replace with your database password


    $dbBackupFilePath = backupDatabase($dbHost, $dbName, $dbUser, $dbPassword, $backupDir, $timestamp);


    addFilesToZip($dirToBackup, $zip, strlen($dirToBackup) + 1);


    $zip->addFile($dbBackupFilePath, basename($dbBackupFilePath));


    $zip->close();


    echo "Combined Backup created successfully: $combinedBackupFilePath\n";


    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host = $_ENV['MAIL_HOST']; // Set the SMTP server to send through
    $mail->SMTPAuth = $_ENV['SMTP_AUTH'];
    $mail->Username = $_ENV['MAIL_USERNAME']; // SMTP username
    $mail->Password = $_ENV['MAIL_PASSWORD']; // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $_ENV['MAIL_PORT'];

    // Recipients
    $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
    $mail->addAddress($_ENV['MAIL_TO'], $_ENV['MAIL_TO_NAME']);

    // Attachments
    $mail->addAttachment($combinedBackupFilePath);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Combined Backup File';
    $mail->Body    = 'Here is the combined backup file (directory + database) we backed up for the week for your website..';

    // Send the email
    $mail->send();
    echo 'Combined backup file has been emailed successfully.';
} catch (Exception $e) {
    echo "Backup process failed: " . $e->getMessage();
}
