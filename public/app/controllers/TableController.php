<?php
declare(strict_types=1);

class TableController
{
    private function receiveInput() {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    private static function get(string $table, PDO $pdo)
    {
        $data = $pdo
            ->query("SELECT * FROM {$table};")
            ->fetchAll(PDO::FETCH_ASSOC);
        
        return $data;
    }

    public static function handle(array $details, PDO $pdo)
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        
        $response = self::$method($details[0], $pdo);

        return $response;
    }
}