<?php
declare(strict_types=1);

require_once 'renumberAfterDelete.php';

function httpCrud($table) {
    function receiveInput() {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    function get(PDO $pdo, string $table) {
        $data = $pdo
            ->query("SELECT * FROM {$table};")
            ->fetchAll(PDO::FETCH_ASSOC);
            
        echo json_encode($data);
    }

    function post(PDO $pdo, string $table) {
        $cn = receiveInput()['cardNumber'];
        $pdo->exec("INSERT INTO {$table} (cardNumber) VALUES ({$cn})");

        $id = (int) $pdo->lastInsertId();
        $newCard = $pdo
            ->query("SELECT * FROM {$table} WHERE id = {$id}")
            ->fetch(PDO::FETCH_ASSOC);

        echo json_encode($newCard);
    }

    function patch(PDO $pdo, string $table) {
        $id = $_GET['id'] ?? null;
        $input = receiveInput();

        $columns = array_keys($input);
        $values = array_values($input);
        $values[] = $id;

        $set = implode(' = ? ', $columns) . ' = ?';
        $query = "UPDATE {$table} SET {$set} WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute($values);

        $colStting = implode(', ', $columns);
        $query = "SELECT {$colStting} FROM {$table} WHERE id = {$id}";
        $updated = $pdo->query($query)->fetch(PDO::FETCH_ASSOC);
        
        if(json_encode($input) == json_encode($updated)) {
            echo '{"success": true}';
        }
    }

    function delete(PDO $pdo, string $table) {
        $id = $_GET['id'] ?? null;
        try {
            $pdo->beginTransaction();

            $pdo->exec("DELETE FROM {$table} WHERE id = {$id}");
            renumberAfterDelete($pdo, $table);

            $pdo->commit();

            echo '{"success": true}';
        } catch (\Throwable $e) {
            if($pdo->inTransaction()) {
                $pdo->rollBack();
                print_r($e);
            }
        }
    }

    try {
        $pdo = newPDO();
        $action = $_GET['real-method']
            ?? strtolower($_SERVER['REQUEST_METHOD']);
        call_user_func_array($action, [$pdo, $table]);  
    } catch (\Throwable $th) {
        http_response_code(404);

        echo '<pre>';
        print_r($th);
        echo '</pre>';

        exit();
    } finally {
        $pdo = null;
    }
}