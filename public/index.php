<?php
declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
// header('Access-Control-Allow-Method: GET, POST, OPTIONS, PATCH');
// if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
// header("Access-Control-Allow-Methods: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']}"); 

// header('Access-Control-Allow-Credentials: true');
// header('Access-Control-Max-Age: 86400');

// if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
//         header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

// echo '<pre>';
// print_r($_SERVER);
// exit();

// require_once '../../allowedOrigins.php';
require_once 'usePDO.php';
require_once 'httpCrud.php';
require_once 'updateReRepeatStatus.php';
require_once 'japSession.php';
require_once 'kanjiSession.php';
require_once 'selectKanji.php';

// $origin = $_SERVER['HTTP_ORIGIN'];
// $origin = $_SERVER['HTTP_ORIGIN'] ?? 'test';
// if(!in_array($origin, $allowedOrigins)) {
//     http_response_code(403);
//     echo "<h3>{$origin}</h3>";
//     echo '<h2>You shall not pass!</h2><h1>🧙🏻‍♂️</h1>';
//     exit();
// }

header('Content-Type: application/json');

$router = [
    'jap' => ['crud', 'jap_words'],
    'kanji' => ['crud', 'collected_kanji'],
    'jap_session' => 'japSession',
    'kanji_session' => 'kanjiSession',
    'select_kanji' => 'selectKanji'
];

try {
    $ru = $_SERVER['REQUEST_URI'] ?? '';
    $target = explode('/', $ru)[2] ?? '';
    $path = explode('?', $target)[0];
    
    $controller = $router[$path] ?? null;

    if(!$controller) {
        http_response_code(404);
        echo 'Wrong path!';
        exit();
    }

    if($controller[0] === 'crud') {
        httpCrud($controller[1]);
    } else {
        usePDO($controller);
    }
} catch (\Throwable $th) {
    http_response_code(500);
    print_r($th);
} 