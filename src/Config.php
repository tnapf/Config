<?php

namespace Tnapf\Config;

use Tnapf\Config\Exceptions\InvalidConfigException;

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
        $keyPath = array_filter(explode('.', $key), fn (string $subKey) => $subKey !== '');

        if (!count($keyPath)) {
            return $default;
        }

        return $this->getByKeyPath($keyPath) ?? $default;
    }

    /**
     * @throws InvalidConfigException
     */
    private function getByKeyPath(array $key)
    {
        $file = sprintf('%s/%s.php', $this->directory, $key[0]);

        if (!file_exists($file)) {
            return null;
        }

        $data = include $file;

        if (!is_array($data)) {
            throw new InvalidConfigException(sprintf('Config at %s should return array', $file));
        }

        array_shift($key);

        return $this->getFromArray($key, $data);
    }

    private function getFromArray(array $key, array $data): mixed
    {
        if (!count($key)) {
            return $data;
        }

        $newData = $data[array_shift($key)] ?? null;

        if (!count($key)) {
            return $newData;
        }

        return $this->getFromArray(
            $key,
            $newData
        );
    }
}
