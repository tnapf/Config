<?php

namespace Tnapf\Config\Test\ConfigProvider;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\SimpleCache\CacheInterface;
use Tnapf\Config\ConfigProvider\CachedConfigProvider;
use Tnapf\Config\ConfigProvider\ConfigProvider;

class CachedConfigProviderTest extends MockeryTestCase
{
    public function testItRetrievesDataFromCache(): void
    {
        /** @var ConfigProvider&MockInterface $providerMock */
        $providerMock = Mockery::mock(ConfigProvider::class);
        /** @var CacheInterface&MockInterface $driverMock */
        $driverMock = Mockery::mock(CacheInterface::class);

        $provider = new CachedConfigProvider($providerMock, $driverMock);

        $driverMock
            ->expects('has')
            ->twice()
            ->with('key')
            ->andReturns(false, true);

        $providerMock
            ->expects('get')
            ->once()
            ->with('key')
            ->andReturns('value');

        $driverMock
            ->expects('set')
            ->once()
            ->with('key', 'value', null);

        $driverMock
            ->expects('get')
            ->twice()
            ->with('key')
            ->andReturns('value');

        $value = $provider->get('key');
        $other = $provider->get('key');

        $this->assertSame($value, $other);
    }
}
