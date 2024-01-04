<?php

$phpunitVersion = shell_exec('vendor/bin/phpunit --version');
preg_match('/(\d+\.\d+\.\d+)/', $phpunitVersion, $matches);
if (!$matches) {
    echo 'Unable to retrieve PHPUnit version.' . PHP_EOL;
    exit;
}

$phpunitVersion = current($matches);
if (version_compare($phpunitVersion, '9.0', '<')) {
    @copy('phpunit-7.0.0.xml', 'phpunit.xml');
} else {
    @copy('phpunit-9.0.0.xml', 'phpunit.xml');
}
