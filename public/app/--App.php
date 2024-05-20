<?php
declare(strict_types=1);

require_once __DIR__ . '/Router.php';

class App
{
    public function run()
    {
        try {
            $pdo = new PDO('sqlite:' . __DIR__ . '/../db.sqlite');

            $response = Router::run($pdo); 

            if($response) {
                header('Content-Type: application/json');
                echo json_encode($response);
            }
        } catch (\Throwable $th) {
            http_response_code(500);
            // echo '<pre>';
            print_r($th);
        } finally {
            $pdo = null;
        }
    }
}