<?php

namespace Tnapf\Config\Exceptions;

use Exception;
use Psr\SimpleCache\InvalidArgumentException;

class InvalidCacheKeyException extends Exception implements InvalidArgumentException
{
}
