<?php
declare(strict_types=1);

require_once __DIR__ . '/controllers/TableController.php';

class App
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('sqlite:' . __DIR__ . '/../db/db.sqlite');
    }
    private function parseRequest(): array
    {
        // echo '<pre>';
        // print_r($_SERVER);

        $ruri = $_SERVER['REQUEST_URI'] ?? '';
        $rarray = explode('/', $ruri);

        $subject = $rarray[2];
        $details = array_slice($rarray, 3);

        return [
            'subject' => $subject,
            'details' => $details
        ];
    }

    public function run()
    {
        $request = $this->parseRequest();

        // echo '<pre>';
        // print_r($request);
        // echo '</pre>';

        switch ($request['subject']) {
            case 'table':
                // new TableController($request['details'], $this->pdo);
                TableController::handle($request['details'], $this->pdo);
                break;
            case 'session':
                echo 'Let\'s start a session!';
                break;
            default:
                http_response_code(404);
                echo '<h2>You shall not pass!</h2><h1>🧙🏻‍♂️</h1>';
        }

        // call_user_method();
        // call_user_method_array();
    }
}