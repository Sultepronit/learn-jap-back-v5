<?php
declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');

require_once 'usePDO.php';
require_once 'httpCrud.php';
require_once 'japSession.php';

// echo '<pre>';
// print_r($_SERVER);
// echo '</pre>';

$router = [
    '/jap' => ['httpCrud', 'jap_words'],
    '/jap_session' => 'japSession'
];

try {
    $path = explode('?', $_SERVER['REQUEST_URI'])[0];
    
    $controller = $router[$path] ?? null;

    if(!$controller) {
        http_response_code(404);
        echo 'Wrong path!';
        exit();
    }

    if($controller[0] === 'httpCrud') {
        call_user_func(...$controller);
    } else {
        usePDO($controller);
    }
} catch (\Throwable $th) {
    http_response_code(500);
    print_r($th);
} 