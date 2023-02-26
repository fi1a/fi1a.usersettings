<?php

declare(strict_types=1);

use Dotenv\Dotenv;

if (PHP_SAPI !== 'cli') {
    exit('only for cli use');
}

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$_SERVER['DOCUMENT_ROOT'] = realpath(getenv('BITRIX_DIR'));
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

require_once __DIR__ . '/bxdefine.php';
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
