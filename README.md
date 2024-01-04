# Ghostscript

[![build](https://github.com/ordinary9843/ghostscript/actions/workflows/build.yml/badge.svg)](https://github.com/ordinary9843/ghostscript/actions/workflows/build.yml)
[![codecov](https://codecov.io/gh/ordinary9843/ghostscript/branch/master/graph/badge.svg?token=DMXRZFN55V)](https://codecov.io/gh/ordinary9843/ghostscript)

## Intro

Use Ghostscript to merge / split all PDF files or guess and convert PDF file version. Fix FPDI error by Ghostscript: This document PDF probably uses a compression technique which is not supported by the free parser shipped with FPDI.

## Cores

This library has the following features:

- Full-featured: This library supports PDF version prediction, conversion, merger, and splitting.
- Lower dependency on external libraries: Most Ghostscript libraries have too high a dependency on other libraries.
- Compatible with multiple PHP versions: It can run properly on PHP 7.1 - 8.x.

## Requirements

This library has the following requirements:

- Ghostscript 9.50+

## Installation

Install `Ghostscript`:

```bash
apt-get install ghostscript
```

Install `ordinary9843/ghostscript`:

```bash
composer require ordinary9843/ghostscript
```

## Usage

Example usage:

```php
<?php
require './vendor/autoload.php';

use Ordinary9843\Ghostscript;

$file = './files/gs_ -test/test.pdf';
$binPath = '/usr/bin/gs';
$tmpPath = sys_get_temp_dir();
$ghostscript = new Ghostscript($binPath, $tmpPath);

/**
 * Set the binary path for PDF processing in Ghostscript.
 */
$ghostscript->setBinPath($binPath);

/**
 * Set the temporary file path for PDF processing in Ghostscript.
 */
$ghostscript->setTmpPath($tmpPath);

/**
 * Guess the PDF version.
 *
 * Output: 1.5
 */
$ghostscript->guess($file);

/**
 * Convert the PDF version.
 *
 * Output: './files/merge/test.pdf'
 */
$ghostscript->convert($file, 1.4);

/**
 * Merge all PDF.
 *
 * Output: './files/merge/test.pdf'
 */
$ghostscript->merge('./files/merge/test.pdf', [
    './files/merge/part_1.pdf',
    './files/merge/part_2.pdf',
    './files/merge/part_3.pdf'
]);

/**
 * Split all PDF.
 *
 * Output: [
 *   './files/split/parts/part_1.pdf',
 *   './files/split/parts/part_2.pdf',
 *   './files/split/parts/part_3.pdf'
 * ]
 */
$ghostscript->split('./files/merge/test.pdf', './files/split/parts');

/**
 * Check if there are any messages.
 *
 * Output: false
 */
$ghostscript->hasMessages(MessageConstant::TYPE_ERROR);

/**
 * Get all execution messages.
 *
 * Output: [
 *  '[ERROR] Message'
 * ]
 */
$ghostscript->getMessages(MessageConstant::TYPE_ERROR);

/**
 * Clear temporary files generated during the PDF processing.
 */
$ghostscript->clearTmpFiles();
```

## Testing

Copy the `.env.example` to `.env`:

```bash
cp .env.example .env
```

Run the tests:

```bash
composer test
```

## Licenses

(The [MIT](http://www.opensource.org/licenses/mit-license.php) License)
