<?php
declare(strict_types=1);

require_once __DIR__ . '/controllers/TableController.php';
require_once __DIR__ . '/controllers/SessionController.php';

class Router
{
    private static function parseRequest(): array
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

    public static function run(PDO $pdo): ?array
    {
        $request = self::parseRequest();

        // echo '<pre>';
        // print_r($request);
        // echo '</pre>';

        switch ($request['subject']) {
            case 'table':
                return TableController::handle($request['details'], $pdo);
            case 'session':
                return SessionController::prepare($request['details'][0], $pdo);
            default:
                http_response_code(404);
                echo '<h2>You shall not pass!</h2><h1>🧙🏻‍♂️</h1>';
                return null;
        }
    }
}