<?php

namespace Tnapf\Config;

use Tnapf\Config\Exceptions\InvalidConfigException;

class Config
{
    private array $cache = [];

    public function __construct(private readonly string $directory, private readonly bool $useCache = false)
    {
    }

    /**
     * @throws InvalidConfigException if a config file does not return an array
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $keyPath = array_filter(explode('.', $key), static fn (string $part): bool => (bool)strlen($part));

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
        $subDirectory = array_shift($key);

        $file = realpath("{$this->directory}/{$subDirectory}.php");
        if (!$file) {
            return null;
        }

        $data = $this->load($file);
        foreach ($key as $part) {
            if (!isset($data[$part])) {
                return null;
            }
            $data = $data[$part];
        }

        return $data;
    }

    private function load(string $filepath): array
    {
        static $loader;
        if (empty($loader)) {
            // Removes $this context from included file.
            $loader = static function (string $filepath): array {
                $data = include $filepath;
                if (!is_array($data)) {
                    throw new InvalidConfigException(sprintf('Config at %s should return array', $filepath));
                }

                return $data;
            };
        }

        if (!$this->useCache) {
            return $loader($filepath);
        }

        if (isset($this->cache[$filepath])) {
            return $this->cache[$filepath];
        }

        return $this->cache[$filepath] = $loader($filepath);
    }
}
