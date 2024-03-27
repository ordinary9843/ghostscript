<?php

namespace Tests\Handlers;

use ReflectionClass;
use Tests\BaseTestCase;
use Ordinary9843\Handlers\MergeHandler;
use Ordinary9843\Exceptions\HandlerException;

class MergeHandlerTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testArgumentsMappingWhenProvidedInputs()
    {
        $handler = new MergeHandler();
        $reflection = new ReflectionClass($handler);
        $property = $reflection->getProperty('argumentsMapping');
        $property->setAccessible(true);
        $argumentsMapping = $property->getValue($handler);
        $this->assertCount(3, $argumentsMapping);
        $this->assertContains('file', $argumentsMapping);
        $this->assertContains('files', $argumentsMapping);
        $this->assertContains('isAutoConvert', $argumentsMapping);
    }

    /**
     * @return void
     */
    public function testExecuteShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/merge/test.pdf';
        $handler = new MergeHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $mergedFile = $handler->execute($file, [
            dirname(__DIR__, 2) . '/files/merge/part_1.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_2.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_3.pdf'
        ]);
        $this->assertEquals($file, $mergedFile);
        $this->assertFileExists($mergedFile);
    }

    /**
     * @return void
     */
    public function testExecuteWhenFilenameHasChineseShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/merge/test.pdf';
        $handler = new MergeHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $mergedFile = $handler->execute($file, [
            dirname(__DIR__, 2) . '/files/gs_ -test/中文.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_1.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_2.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_3.pdf'
        ]);
        $this->assertEquals($file, $mergedFile);
        $this->assertFileExists($mergedFile);
    }

    /**
     * @return void
     */
    public function testExecuteWhenFileDoesNotExistShouldSkipProcessing(): void
    {
        $file = dirname(__DIR__, 2) . '/files/merge/test.pdf';
        $handler = new MergeHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $mergedFile = $handler->execute($file, [
            dirname(__DIR__, 2) . '/files/merge/part_1.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_2.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_3.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_4.pdf'
        ]);
        $this->assertEquals($file, $mergedFile);
        $this->assertFileExists($mergedFile);
    }

    /**
     * @return void
     */
    public function testExecuteWhenFileTypeNotMatchShouldSkipProcessing(): void
    {
        $file = dirname(__DIR__, 2) . '/files/merge/test.pdf';
        $handler = new MergeHandler();
        $mergedFile = $handler->execute($file, [
            dirname(__DIR__, 2) . '/files/merge/part_1.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_2.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_3.pdf',
            dirname(__DIR__, 2) . '/files/merge/test.txt'
        ]);
        $this->assertEquals($file, $mergedFile);
        $this->assertFileExists($mergedFile);
    }

    /**
     * @return void
     */
    public function testExecuteFailedShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $file = dirname(__DIR__, 2) . '/files/merge/test.pdf';
        $handler = new MergeHandler();
        $handler->setOptions([
            'test' => true
        ]);
        $handler->execute($file, [
            dirname(__DIR__, 2) . '/files/merge/part_1.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_2.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_3.pdf'
        ]);
    }
}
