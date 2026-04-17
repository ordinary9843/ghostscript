# Ghostscript

[![build](https://github.com/ordinary9843/ghostscript/actions/workflows/build.yml/badge.svg)](https://github.com/ordinary9843/ghostscript/actions/workflows/build.yml)
[![codecov](https://codecov.io/gh/ordinary9843/ghostscript/branch/master/graph/badge.svg?token=DMXRZFN55V)](https://codecov.io/gh/ordinary9843/ghostscript)

### If there are any features you desire, please open an issue, and I will do our best to meet your requirements!

## Intro

Use Ghostscript to merge / split all PDF files or guess and convert PDF file version, and transform PDF into images. Fix FPDI error by Ghostscript: This document PDF probably uses a compression technique which is not supported by the free parser shipped with FPDI.

## Cores

This library has the following features:

- Full-featured: This library supports PDF version prediction, conversion, merger, and splitting.
- Lower dependency on external libraries: Most Ghostscript libraries have too high a dependency on other libraries.
- Compatible with multiple PHP versions: It can run properly on PHP 7.1 - 8.4.

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
use Ordinary9843\Constants\GhostscriptConstant;
use Ordinary9843\Constants\ImageTypeConstant;

$file = './files/gs_ -test/test.pdf';
$binPath = '/usr/bin/gs';
$tmpPath = sys_get_temp_dir();
$ghostscript = new Ghostscript($binPath, $tmpPath);

/**
 * This function sets the path for the Ghostscript binary, which will be used for PDF processing.
 */
$ghostscript->setBinPath($binPath);

/**
 * This function sets the path for storing temporary files created during the PDF processing in Ghostscript.
 */
$ghostscript->setTmpPath($tmpPath);

/**
 * This function analyzes the input PDF file and returns the guessed PDF version.
 *
 * Output: 1.5
 */
$ghostscript->guess($file);

/**
 * This function converts the version of the input PDF file to the specified PDF version.
 *
 * Output: './files/convert/test.pdf'
 */
$ghostscript->convert($file, GhostscriptConstant::STABLE_VERSION);

/**
 * This function merges multiple PDF files into a single PDF file.
 * The fourth parameter $isAutoConvert (default: true) automatically converts
 * all input PDFs to a stable version before merging.
 *
 * Output: './files/merge/res.pdf'
 */
$ghostscript->merge('./files/merge', 'res.pdf', [
    './files/merge/part_1.pdf',
    './files/merge/part_2.pdf',
    './files/merge/part_3.pdf'
], true);

/**
 * This function splits a PDF file into individual pages, each saved as a separate PDF file.
 *
 * Output: [
 *   './files/split/parts/part_1.pdf',
 *   './files/split/parts/part_2.pdf',
 *   './files/split/parts/part_3.pdf'
 * ]
 */
$ghostscript->split('./files/split/test.pdf', './files/split/parts');

/**
 * This function converts each page of a PDF file into individual image files.
 * Supported types: ImageTypeConstant::JPEG, ImageTypeConstant::PNG
 *
 * Output: [
 *   './files/to-image/images/image_1.jpeg',
 *   './files/to-image/images/image_2.jpeg',
 *   './files/to-image/images/image_3.jpeg'
 * ]
 */
$ghostscript->toImage('./files/to-image/test.pdf', './files/to-image/images', ImageTypeConstant::JPEG);

/**
 * This function calculates and returns the total number of pages in a PDF file.
 *
 * Output: 3
 */
$ghostscript->getTotalPages('./files/get-total-pages/test.pdf');

/**
 * This function sets custom Ghostscript options appended to the shell command.
 */
$ghostscript->setOptions(['-dPDFSETTINGS' => '/ebook']);

/**
 * This function returns the currently configured Ghostscript options.
 *
 * Output: ['-dPDFSETTINGS' => '/ebook']
 */
$ghostscript->getOptions();

/**
 * Clear temporary files generated during the PDF processing.
 * $isForceClear = true removes all tmp files immediately regardless of age.
 * $days specifies the age threshold in days (default: 7).
 */
$ghostscript->clearTmpFiles();
$ghostscript->clearTmpFiles(true);
$ghostscript->clearTmpFiles(false, 30);
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
