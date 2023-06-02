<?php

namespace Tnapf\Config\Test\Cache;

use PHPUnit\Framework\TestCase;
use Tnapf\Config\Cache\InMemoryDriver;
use Tnapf\Config\Exceptions\InvalidCacheKeyException;

class InMemoryDriverTest extends TestCase
{
    public function testItCachesValues(): void
    {
        $driver = new InMemoryDriver();
        $driver->set('host', 'localhost');

        $this->assertTrue($driver->has('host'));
        $this->assertSame('localhost', $driver->get('host'));
    }

    public function testItDeletesCache(): void
    {
        $driver = new InMemoryDriver();
        $driver->set('key', 'value');

        $driver->delete('key');

        $this->assertFalse($driver->has('key'));
        $this->assertNull($driver->get('key'));
    }

    public function testItClearsCache(): void
    {
        $driver = new InMemoryDriver();
        $driver->setMultiple(['host' => 'localhost', 'user' => 'root']);

        $driver->clear();

        $this->assertFalse($driver->has('host'));
        $this->assertNull($driver->get('host'));

        $this->assertFalse($driver->has('user'));
        $this->assertNull($driver->get('user'));
    }

    public function testItRetrievesDefaultValueOnMissingCache(): void
    {
        $driver = new InMemoryDriver();

        $this->assertFalse($driver->has('non-existent-key'));

        $result = $driver->get('non-existent-key', 'defaultValue');
        $this->assertSame('defaultValue', $result);
    }

    public function testItFillsDefaultValueForMultipleKeys(): void
    {
        $driver = new InMemoryDriver();

        $driver->set('host', 'localhost');
        $expected = ['host' => 'localhost', 'user' => 'empty', 'driver' => 'empty'];

        $this->assertSame($expected, $driver->getMultiple(['host', 'user', 'driver'], 'empty'));
    }

    public function testItDeletesMultipleKeys(): void
    {
        $driver = new InMemoryDriver();

        $driver->setMultiple(['host' => 'localhost', 'user' => 'root', 'driver' => 'pdo']);

        $driver->deleteMultiple(['host', 'user']);

        $this->assertFalse($driver->has('host'));
        $this->assertNull($driver->get('host'));

        $this->assertFalse($driver->has('user'));
        $this->assertNull($driver->get('user'));

        $this->assertTrue($driver->has('driver'));
        $this->assertSame('pdo', $driver->get('driver'));
    }

    public function testItThrowsWhenCacheKeyIsInvalid(): void
    {
        $this->expectException(InvalidCacheKeyException::class);

        $driver = new InMemoryDriver();
        $driver->get('{invalid}');
    }
}
