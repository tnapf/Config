<?php

namespace Tnapf\Config\Test\E2E;

use Tnapf\Config\ConfigProvider\ConfigProvider;
use Tnapf\Config\ConfigProvider\PhpBackedConfigProvider;

class PhpBackedConfigTest extends ConfigTestTemplate
{
    protected function getConfigProvider(): ConfigProvider
    {
        return new PhpBackedConfigProvider(__DIR__ . '/config');
    }
}
