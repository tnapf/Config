<?php

namespace Tnapf\Config\Test;

use Mockery;
use Mockery\MockInterface;
use Tnapf\Config\Config;
use Tnapf\Config\Exceptions\InvalidConfigException;
use PHPUnit\Framework\TestCase;
use Tnapf\Config\ConfigProvider\ConfigProvider;

class ConfigTest extends TestCase
{
    /**
     * @dataProvider configProvider
     */
    public function testItRetrievesConfigurations(string $key, mixed $expected)
    {
        /** @var ConfigProvider&MockInterface */
        $configProvider = Mockery::mock(ConfigProvider::class);
        $configProvider
            ->expects()
            ->get()
            ->with('test-config')
            ->andReturns([
                'simple' => '::simple::',
                'two-levels' => [
                    'deep' => '::two-levels-deep::',
                ],
                'int' => 1,
            ]);

        $config = new Config($configProvider);

        $this->assertSame(
            $expected,
            $config->get($key),
        );
    }

    public static function configProvider(): array
    {
        return [
            'Simple' => [
                'key' => 'test-config.simple',
                'expected' => '::simple::',
            ],
            'Two levels deep' => [
                'key' => 'test-config.two-levels.deep',
                'expected' => '::two-levels-deep::',
            ],
            'Int' => [
                'key' => 'test-config.int',
                'expected' => 1,
            ],
            'File only' => [
                'key' => 'test-config',
                'expected' => [
                    'simple' => '::simple::',
                    'two-levels' => [
                        'deep' => '::two-levels-deep::',
                    ],
                    'int' => 1,
                ],
            ],
        ];
    }

    public function testItThrowsAnErrorIfAConfigProviderReturnsNonArrayForMultiLevelKeys()
    {
        /** @var ConfigProvider&MockInterface */
        $configProvider = Mockery::mock(ConfigProvider::class);
        $configProvider
            ->expects()
            ->get()
            ->with('invalid-config')
            ->andReturns('This is certainly not an array');

        $config = new Config($configProvider);

        $this->expectException(InvalidConfigException::class);

        $config->get('invalid-config.key');
    }

    /**
     * @dataProvider returnsDefaultProvider
     */
    public function testItReturnsDefault(string $key, mixed $providerReturn)
    {
        /** @var ConfigProvider&MockInterface */
        $configProvider = Mockery::mock(ConfigProvider::class);
        $configProvider
            ->expects()
            ->get()
            ->withAnyArgs()
            ->andReturns($providerReturn);

        $config = new Config($configProvider);

        $this->assertSame(
            '::default::',
            $config->get($key, '::default::'),
        );
    }

    public static function returnsDefaultProvider(): array
    {
        return [
            'Empty key' => [
                'key' => '',
                'providerReturn' => null,
            ],
            'Non-existant file' => [
                'key' => 'doesnt-exist',
                'providerReturn' => null,
            ],
            'Key not found' => [
                'key' => 'test-config.doesnt-exist',
                'providerReturn' => ['something-else' => 'cheese'],
            ],
        ];
    }
}
