<?php

namespace Tnapf\Config\ConfigProvider;

interface ConfigProvider
{
    /**
     * @throws InvalidConfigException
     */
    public function get(string $key): mixed;
}
