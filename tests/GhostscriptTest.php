<?php

require __DIR__ . '/../vendor/autoload.php';

use Ordinary9843\Ghostscript;

try {
    $isWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

    if ($isWindows === true) {

        // Windows example path
        $binPath = 'C:\gs\gs9.50\bin\gswin64c.exe';
        $tmpPath = 'C:\Windows\TEMP';
    } else {

        // Linux example path
        $binPath = '/usr/local/bin/gs';
        $tmpPath = '/tmp';
    }

    $ghostscript = new Ghostscript($binPath, $tmpPath);
    $file = '../files/test.pdf';

    // Guess PDF version
    $version = $ghostscript->guess($file);
    echo 'Version is: ' . $version . '<br />';

    // Convert PDF version
    $newVersion = 1.4;
    $file = $ghostscript->convert($file, $newVersion);
    echo 'New file path: ' . $file . '<br />';

    // Can also be delete temporary file
    $ghostscript->deleteTmpFile();

    echo 'Success!';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
} finally {
    die;
}