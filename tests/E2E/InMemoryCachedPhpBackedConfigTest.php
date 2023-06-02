<?php

namespace Tnapf\Config\Test\E2E;

use PHPUnit\Framework\TestCase;
use Tnapf\Config\Cache\InMemoryDriver;
use Tnapf\Config\ConfigProvider\CachedConfigProvider;
use Tnapf\Config\ConfigProvider\ConfigProvider;
use Tnapf\Config\ConfigProvider\PhpBackedConfigProvider;

class InMemoryCachedPhpBackedConfigTest extends ConfigTestTemplate
{
    protected function createConfigProvider(): ConfigProvider
    {
        return new CachedConfigProvider(
            new PhpBackedConfigProvider(__DIR__ . '/config'),
            new InMemoryDriver()
        );
    }
}
