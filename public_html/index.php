<?php

declare(strict_types=1);

use App\Kernel;

ini_set('error_reporting', (string) E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';
$base_path = dirname(__DIR__);

$kernel = new Kernel($base_path, $config);
$kernel->handle();