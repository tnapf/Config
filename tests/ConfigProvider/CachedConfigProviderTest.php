<?php

namespace Tnapf\Config\Test\ConfigProvider;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\SimpleCache\CacheInterface;
use Tnapf\Config\ConfigProvider\CachedConfigProvider;
use Tnapf\Config\ConfigProvider\ConfigProvider;
use Tnapf\Config\Exceptions\InvalidCacheKeyException;
use Tnapf\Config\Exceptions\InvalidConfigException;

class CachedConfigProviderTest extends MockeryTestCase
{
    public function testItRetrievesDataFromCache()
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

    public function testItRethrowsOnCacheDriverFailure()
    {
        /** @var ConfigProvider&MockInterface $providerMock */
        $providerMock = Mockery::mock(ConfigProvider::class);
        /** @var CacheInterface&MockInterface $driverMock */
        $driverMock = Mockery::mock(CacheInterface::class);

        $provider = new CachedConfigProvider($providerMock, $driverMock);

        $driverMock
            ->expects('has')
            ->once()
            ->with('{invalid-key}')
            ->andThrows(InvalidCacheKeyException::class);

        $providerMock
            ->expects('get')
            ->never();

        $driverMock
            ->expects('set')
            ->never();

        $driverMock
            ->expects('get')
            ->never();

        $this->expectException(InvalidConfigException::class);
        $provider->get('{invalid-key}');
    }
}
