<?php

$phpunitVersion = shell_exec('vendor/bin/phpunit --version');
preg_match('/(\d+\.\d+\.\d+)/', $phpunitVersion, $matches);
if (!$matches) {
    echo 'Unable to retrieve PHPUnit version.' . PHP_EOL;
    exit;
}

$phpunitVersion = current($matches);
if (version_compare($phpunitVersion, '9.0', '<')) {
    @copy('phpunit-7.xml', 'phpunit.xml');
} else {
    if ((PHP_MAJOR_VERSION === 7 && in_array(PHP_MINOR_VERSION, [3, 4])) || (PHP_MAJOR_VERSION === 8 && PHP_MINOR_VERSION === 0)) {
        @copy('phpunit-9-coverage.xml', 'phpunit.xml');
    } else {
        @copy('phpunit-9-source.xml', 'phpunit.xml');
    }
}
