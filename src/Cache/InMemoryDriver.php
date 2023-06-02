<?php

namespace Tnapf\Config\Cache;

use DateInterval;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Tnapf\Config\Exceptions\InvalidCacheKeyException;

class InMemoryDriver implements CacheInterface
{
    private array $storage = [];

    /**
     * @throws InvalidArgumentException
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $this->storage[$key] : $default;
    }

    /**
     * @throws InvalidCacheKeyException
     */
    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $this->validateKey($key);
        $this->storage[$key] = $value;

        return true;
    }

    /**
     * @throws InvalidCacheKeyException
     */
    public function delete(string $key): bool
    {
        $this->validateKey($key);
        unset($this->storage[$key]);

        return true;
    }

    public function clear(): bool
    {
        $this->storage = [];

        return true;
    }

    /**
     * @param string[] $keys
     *
     * @throws InvalidArgumentException
     *
     * @return iterable<string, mixed>
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->get($key, $default);
        }

        return $items;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    /**
     * @throws InvalidCacheKeyException
     */
    public function has(string $key): bool
    {
        $this->validateKey($key);

        return isset($this->storage[$key]) || array_key_exists($key, $this->storage);
    }

    /**
     * @throws InvalidCacheKeyException
     */
    private function validateKey(string $key): void
    {
        if (preg_match('/[{}()\/\\\@:]/', $key)) {
            throw new InvalidCacheKeyException(sprintf('Cache key %s is invalid', $key));
        }
    }
}
