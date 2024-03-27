<?php

namespace Tests\Traits;

use Tests\BaseTestCase;
use Ordinary9843\Traits\ConfigTrait;

class ConfigTraitTest extends BaseTestCase
{
    use ConfigTrait;

    /**
     * @return void
     */
    public function testSetBinPathShouldEqualGetBinPath(): void
    {
        $binPath = $this->getEnv('GS_BIN_PATH');
        $this->setBinPath($binPath);
        $this->assertEquals($binPath, $this->getBinPath());
    }

    /**
     * @return void
     */
    public function testSetTmpPathShouldEqualGetTmpPath(): void
    {
        $tmpPath = sys_get_temp_dir();
        $this->setTmpPath($tmpPath);
        $this->assertEquals($tmpPath, $this->getTmpPath());
    }
}
