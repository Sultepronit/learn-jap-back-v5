<?php
declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');

if(!key_exists('REQUEST_METHOD', $_SERVER) || $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

require_once './app/App.php';

$app = new App();

$app->run();

