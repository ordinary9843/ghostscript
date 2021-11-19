# Ghostscript
[![build](https://github.com/ordinary9843/ghostscript/actions/workflows/php.yml/badge.svg)](https://github.com/ordinary9843/ghostscript/actions/workflows/php.yml)

Use ghostscript guess and convert PDF file version for PHP.

## Requirements
This library has the following requirements:

 - PHP 7.1+
 - Ghostscript 9.50+

## Installation
Require the package via Composer:

```bash
composer require ordinary9843/ghostscript
```

## Usage
This is a simple usage example how to guess or convert PDF.

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Ordinary9843\Ghostscript;

$binPath = '/usr/bin/gs';
$tmpPath = '/tmp';
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
```

## Testing
```bash
vendor/bin/phpunit
```

## Licenses
(The [MIT](http://www.opensource.org/licenses/mit-license.php) License)

Copyright &copy; [Jerry Chen](https://ordinary9843.medium.com/)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE