# Config

Simple package to handle PHP based configuration files.

Config keys can be used to cause other php files to be executed. Be careful and do NOT trust user input anywhere near a config key.

## Install

```sh
composer require tnapf/config
```

## Usage

```php

/**
 * -- src
 * ---- config
 * ------ database.php
 * ---- index.php
 */

# src/config/database.php

return [
    'host' => 'localhost',
    'name' => 'my_database',
    'port' => 1337,
];

# src/index.php

$config = new Exan\Config\Config(__DIR__ . '/config');

$config->get('database.host', 'my-default-value'); // 'localhost'
```

## Requirements

- PHP 8.1+
