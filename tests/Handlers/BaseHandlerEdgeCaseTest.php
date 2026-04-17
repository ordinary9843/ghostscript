<?php

declare(strict_types=1);

namespace Tests\Handlers;

use Tests\BaseTestCase;
use Ordinary9843\Handlers\BaseHandler;
use Ordinary9843\Exceptions\InvalidException;

class BaseHandlerEdgeCaseTest extends BaseTestCase
{
    public function testIsPdfReturnsFalseForNonExistentFile(): void
    {
        $handler = new BaseHandler();
        $this->assertFalse($handler->isPdf('/nonexistent/path/file.pdf'));
    }

    public function testIsPdfReturnsFalseForEmptyString(): void
    {
        $handler = new BaseHandler();
        $this->assertFalse($handler->isPdf(''));
    }

    public function testIsPdfReturnsFalseForDirectoryPath(): void
    {
        $handler = new BaseHandler();
        $this->assertFalse($handler->isPdf(sys_get_temp_dir()));
    }

    public function testGetTmpFileContainsTmpPath(): void
    {
        $handler = new BaseHandler();
        $tmpFile = $handler->getTmpFile();
        $this->assertStringStartsWith($handler->getTmpPath(), $tmpFile);
    }

    public function testGetTmpFileWithCustomFilenameContainsPrefix(): void
    {
        $handler = new BaseHandler();
        $tmpFile = $handler->getTmpFile('custom');
        $this->assertStringContainsString('ghostscript_tmp_file_custom', $tmpFile);
        $this->assertStringEndsWith('.pdf', $tmpFile);
    }

    public function testForceClearDeletesTmpPrefixedFiles(): void
    {
        $handler = new BaseHandler();
        $tmpFile = tempnam($handler->getTmpPath(), BaseHandler::TMP_FILE_PREFIX);
        @rename($tmpFile, $tmpFile . '.pdf');
        $tmpFile .= '.pdf';
        $handler->clearTmpFiles(true);
        $this->assertFileDoesNotExist($tmpFile);
    }

    public function testOptionsToCommandWithKeyValuePairs(): void
    {
        $handler = new BaseHandler();
        $handler->setOptions(['-dCompatibilityLevel' => '1.4']);
        $result = $handler->optionsToCommand('gs');
        $this->assertEquals('gs -dCompatibilityLevel=1.4', $result);
    }

    public function testOptionsToCommandWithNumericKeys(): void
    {
        $handler = new BaseHandler();
        $handler->setOptions(['-dSAFER', '-dBATCH']);
        $result = $handler->optionsToCommand('gs');
        $this->assertEquals('gs -dSAFER -dBATCH', $result);
    }

    public function testOptionsToCommandWithEmptyOptionsReturnsOriginal(): void
    {
        $handler = new BaseHandler();
        $this->assertEquals('gs -sDEVICE=pdfwrite', $handler->optionsToCommand('gs -sDEVICE=pdfwrite'));
    }

    public function testValidateBinPathThrowsForNonExistentPath(): void
    {
        $this->expectException(InvalidException::class);
        $this->expectExceptionCode(InvalidException::CODE_FILEPATH);
        $handler = new BaseHandler();
        $handler->setBinPath('/nonexistent/gs');
        $handler->validateBinPath();
    }
}
