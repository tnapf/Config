<?php

namespace Tnapf\Config\ConfigProvider;

use Tnapf\Config\Exceptions\InvalidConfigException;

interface ConfigProvider
{
    /**
     * @throws InvalidConfigException
     */
    public function get(string $key): mixed;
}
