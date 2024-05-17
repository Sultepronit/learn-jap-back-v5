<?php
declare(strict_types=1);

class TableController
{
    public function __construct(
        public array $details,
        public PDO $pdo
    )
    {
        echo 'Here we go with a table!';
    }

    private function receiveInput() {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    private static function get(string $table, PDO $pdo) {
        $data = $pdo
            ->query("SELECT * FROM {$table};")
            ->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($data);
        // return $data;
    }

    public static function handle(array $details, PDO $pdo)
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        // echo $method;
        self::$method($details[0], $pdo);
    }
}