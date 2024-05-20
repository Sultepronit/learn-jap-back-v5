<?php
declare(strict_types=1);

class TableController
{
    private static array $request = [];
    private static string $table = '';
    private static int $id = 0;
    private static PDO $pdo;
    
    private static function receiveInput() {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    private static function get()
    {
        $table = self::$table;
        $data = self::$pdo
            ->query("SELECT * FROM " . self::$table)
            ->fetchAll(PDO::FETCH_ASSOC);
        
        return $data;
    }

    private static function post() {
        $table = self::$table;
        $cn = self::receiveInput()['cardNumber'];
        self::$pdo->exec("INSERT INTO {$table} (cardNumber) VALUES ({$cn})");

        $id = (int) $pdo->lastInsertId();
        $newCard = $pdo
            ->query("SELECT * FROM {$table} WHERE id = {$id}")
            ->fetch(PDO::FETCH_ASSOC);

        echo json_encode($newCard);
    }

    public static function handle(array $request, PDO $pdo)
    {
        // self::$request = $request;
        self::$table = $request[0];
        self::$id = (int) $request[1] ?? 0;
        self::$pdo = $pdo;

        $method = strtolower($_SERVER['REQUEST_METHOD']);
        
        $response = self::$method();

        return $response;
    }
}