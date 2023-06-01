<?php

namespace Tnapf\Config\ConfigProvider;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

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
        if (!$this->cache->has($key)) {
            $data = $this->configProvider->get($key);
            $this->cache->set($key, $data, $this->ttl);
        }

        return $this->cache->get($key);
    }
}
