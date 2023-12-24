<?php
declare(strict_types=1);

require_once 'read.php';

function httpCrud0($table) {
    echo 'ready to CRUD!', '<br>';
    // echo '<pre>';
    // print_r($_GET);
    // echo '</pre>';
    // echo $_GET['table'], '<br>';
    // echo $_GET['id'];

    $method = $_SERVER['REQUEST_METHOD'];
    // echo $method, '<br>';

    $HTTP_CRUD = [
        'GET' => 'read',
        'POST' => 'create',
        'PATCH' => 'update',
        'DELETE' => 'delete'
    ];

    $oparation = $HTTP_CRUD[$method];
    // echo $oparation, '<br>';

    // $table = $_GET['table'] ?? null;
    $id = $_GET['id'] ?? null;
    $json = file_get_contents('php://input');
    $input = json_decode($json, true);

    call_user_func($oparation, $table, $id, $input);


    // echo '[', $json, ']<br>';
    // echo '[', $input, ']<br>';
    
}