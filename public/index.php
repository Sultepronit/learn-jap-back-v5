<?php
declare(strict_types=1);

require_once './app/run.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');

if(!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

run();

# git test