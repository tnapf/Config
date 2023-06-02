<?php

namespace Tnapf\Config\Test\E2E;

use PHPUnit\Framework\TestCase;
use Tnapf\Config\ConfigProvider\ConfigProvider;
use Tnapf\Config\ConfigProvider\PhpBackedConfigProvider;

class PhpBackedConfigTest extends ConfigTestTemplate
{
    protected function createConfigProvider(): ConfigProvider
    {
        return new PhpBackedConfigProvider(__DIR__ . '/config');
    }
}