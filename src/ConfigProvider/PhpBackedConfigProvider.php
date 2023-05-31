<?php

namespace Tnapf\Config\ConfigProvider;

use Tnapf\Config\Exceptions\InvalidConfigException;

class PhpBackedConfigProvider implements ConfigProvider
{
    public function __construct(private readonly string $directory)
    {
    }

    public function get(string $key): mixed
    {
        $file = $this->directory . DIRECTORY_SEPARATOR . $key . '.php';
        if (!is_file($file)) {
            return null;
        }

        $data = include $file;
        if (!is_array($data)) {
            throw new InvalidConfigException(sprintf('Config at %s should return array', $file));
        }

        return $data;
    }
}
