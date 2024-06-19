<?php
namespace Codedwebltd\Backups\Class;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class AddFilesToZip{

    public function __construct()
    {
        
    }

    public function onFilesAddedToZipListener($dir, $zip, $baseDirLength) 
    {
       
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $name => $file) {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, $baseDirLength);

            if ($file->isDir()) {
                // Add empty directory to archive (if necessary)
                return $zip->addEmptyDir($relativePath);
            } else {
                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
                $index = $zip->numFiles - 1;  // Get the index of the last added file
              return  $zip->setCompressionIndex($index, ZipArchive::CM_DEFAULT);  // Apply default compression
            }
        }

        
    
    }
}