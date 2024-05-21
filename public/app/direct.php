<?php
declare(strict_types=1);

require_once 'TableController.php';
require_once 'session.php';

function parseRequest(): array
{
    // echo '<pre>';
    // print_r($_SERVER);

    $ruri = $_SERVER['REQUEST_URI'] ?? '';
    $rarray = explode('/', $ruri);

    $details = array_slice($rarray, 3);

    return [
        'subject' => $rarray[2],
        'details' => $details
    ];
}

function direct(PDO $pdo): ?array
{
    $request = parseRequest();

    switch ($request['subject']) {
        case 'table':
            return (new TableController($request['details'], $pdo))->handle();

        case 'session':
            return session($request['details'][0], $pdo);

        default:
            http_response_code(404);
            echo '<h2>You shall not pass!</h2><h1>🧙🏻‍♂️</h1>';
            return null;
    }
}