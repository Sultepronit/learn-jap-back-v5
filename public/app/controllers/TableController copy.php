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

    private function get(PDO $pdo, string $table) {
        $data = $pdo
            ->query("SELECT * FROM {$table};")
            ->fetchAll(PDO::FETCH_ASSOC);
            
        // echo json_encode($data);
        return $data;
    }

    
}