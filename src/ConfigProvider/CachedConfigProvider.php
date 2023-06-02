<?php

namespace Tnapf\Config\ConfigProvider;

use DateInterval;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Tnapf\Config\Exceptions\InvalidConfigException;

class CachedConfigProvider implements ConfigProvider
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly CacheInterface $cache,
        private readonly DateInterval|int|null $ttl = null
    ) {
    }

    public function get(string $key): mixed
    {
        try {
            if (!$this->cache->has($key)) {
                $this->cache->set(
                    $key,
                    $this->configProvider->get($key),
                    $this->ttl
                );
            }

            return $this->cache->get($key);
        } catch (InvalidArgumentException $e) {
            throw new InvalidConfigException(sprintf('Config key %s is invalid', $key), previous: $e);
        }
    }
}
