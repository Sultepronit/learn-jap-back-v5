<?php
declare(strict_types=1);

require_once 'helpers/renumberAfterDelete.php';
require_once 'helpers/addNextRepeatStatus.php';

class TableController
{
    private string $table = '';
    private int $id = 0;
    private PDO $pdo;

    public function __construct(array $request, PDO $pdo)
    {
        $this->table = $request[0];
        $this->id = isset($request[1]) ? (int) $request[1] : 0;
        $this->pdo = $pdo;
    }
    
    private static function receiveInput() {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    private function get()
    {
        $data = $this->pdo
            ->query("SELECT * FROM {$this->table}")
            ->fetchAll(PDO::FETCH_ASSOC);
        
        // return $data;
        return tableToCamelCase($data);
    }

    private function patch() {
        $input = self::receiveInput();
        
        $input = addNextRepeatStatus($input, $this->pdo, $this->table);

        $input = changeRowTitles($input, 'snake');

        // $columns = changeRowTitles(array_keys($input), 'snake');
        $columns = array_keys($input);
        $values = [...array_values($input), $this->id];

        # set data to db
        $set = implode(' = ?, ', $columns) . ' = ?';
        $query = "UPDATE {$this->table} SET {$set} WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($values);

        # fetch it back
        $colString = implode(', ', $columns);
        $query = "SELECT {$colString} FROM {$this->table} WHERE id = {$this->id}";
        $updated = $this->pdo->query($query)->fetch(PDO::FETCH_ASSOC);
        
        # check results
        return json_encode($input) == json_encode($updated) ?
            ['success' => true] : ['input' => $input, 'result' => $updated];
    }

    # words only
    private function post() {
        $cn = self::receiveInput()['cardNumber'];
        $this->pdo->exec("INSERT INTO {$this->table} (card_number) VALUES ({$cn})");

        $id = (int) $this->pdo->lastInsertId();
        $newCard = $this->pdo
            ->query("SELECT * FROM {$this->table} WHERE id = {$id}")
            ->fetch(PDO::FETCH_ASSOC);

        return changeRowTitles($newCard, 'camel');
    }

    # words only
    private function delete() {
        try {
            $this->pdo->beginTransaction();

            $this->pdo->exec("DELETE FROM {$this->table} WHERE id = {$this->id}");
            renumberAfterDelete($this->pdo, $this->table);

            $this->pdo->commit();

            return ['success' => true];
        } catch (\Throwable $e) {
            if($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            http_response_code(500);
            print_r($e);
            // exit();
        }
    }

    public function handle() {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        
        $response = $this->$method();

        return $response;
    }
}