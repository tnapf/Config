<?php

namespace Tnapf\Config;

use Tnapf\Config\ConfigProvider\ConfigProvider;
use Tnapf\Config\Exceptions\InvalidConfigException;

class Config
{
    public function __construct(private readonly ConfigProvider $configProvider)
    {
    }

    /**
     * @throws InvalidConfigException if a config file does not return an array
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $keyPath = array_filter(explode('.', $key), static fn (string $part): bool => $part !== '');

        if (empty($keyPath)) {
            return $default;
        }

        return $this->getByKeyPath($keyPath) ?? $default;
    }

    /**
     * @throws InvalidConfigException
     */
    private function getByKeyPath(array $key): mixed
    {
        $providerKey = array_shift($key);
        $data = $this->configProvider->get($providerKey);

        if (!empty($key) && !is_array($data) && !is_null($data)) {
            throw new InvalidConfigException(sprintf('Expected array for provider key %s', $providerKey));
        }

        foreach ($key as $part) {
            if (!isset($data[$part])) {
                return null;
            }
            $data = $data[$part];
        }

        return $data;
    }
}
