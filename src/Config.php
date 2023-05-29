<?php

namespace Tnapf\Config;

use Tnapf\Config\Exceptions\InvalidConfigException;

use function is_array;
use function is_file;
use function sprintf;

use const DIRECTORY_SEPARATOR;

class Config
{
    public function __construct(private readonly string $directory)
    {
    }

    /**
     * @throws InvalidConfigException if a config file does not return an array
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $keyPath = array_filter(explode('.', $key), static fn(string $part): bool => $part !== '');

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
        $file = $this->directory . DIRECTORY_SEPARATOR . $key[0] . '.php';
        if (!is_file($file)) {
            return null;
        }

        $data = include $file;
        if (!is_array($data)) {
            throw new InvalidConfigException(sprintf('Config at %s should return array', $file));
        }

        array_shift($key);

        foreach ($key as $part) {
            if (!isset($data[$part])) {
                return null;
            }
            $data = $data[$part];
        }

        return $data;
    }
}
