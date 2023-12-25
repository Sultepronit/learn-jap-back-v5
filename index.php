<?php
declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Origin: http://localhost:5173');
// header('Access-Control-Allow-Methods: PATCH, POST, GET, OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type, Authorization');
// header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

require_once 'newPDO.php';
require_once 'httpCrud.php';
// require_once 'japSession.php';

// echo '<pre>';
// print_r($_SERVER);
// echo '</pre>';

$router = [
    '/jap' => ['httpCrud', 'jap_words'],
];

try {
    $path = explode('?', $_SERVER['REQUEST_URI'])[0];
    // echo $path, '<br>';
    $controller = $router[$path] ?? null;

    if(!$controller) {
        http_response_code(404);
        echo 'Wrong path!';
        exit();
    }

    call_user_func(...$controller);
} catch (\Throwable $th) {
    http_response_code(404);
    echo '<pre>';
    print_r($th);
    echo '</pre>';
} 