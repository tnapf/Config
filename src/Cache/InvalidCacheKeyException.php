<?php

namespace Tnapf\Config\Cache;

use Exception;
use Psr\SimpleCache\InvalidArgumentException;

class InvalidCacheKeyException extends Exception implements InvalidArgumentException
{
}
