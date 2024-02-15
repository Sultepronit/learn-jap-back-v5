<?php
declare(strict_types=1);

require_once 'renumberAfterDelete.php';
require_once 'addNextRepeatStatus.php';

function httpCrud(string $table) {
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

    // function options() {
    //     echo 'Here we go!';
    // }

    function patch(PDO $pdo, string $table) {
        $id = $_GET['id'] ?? null;
        $input = receiveInput();
        
        // if(isset($input['learnStatus']) && $input['learnStatus'] == 33) {
        //     $input['learnStatus'] = addNextRepeatStatus($pdo, $table);
        // }
        $input = addNextRepeatStatus($input, $pdo, $table);

        $columns = array_keys($input);
        $values = array_values($input);
        $values[] = (int)$id;

        $set = implode(' = ?, ', $columns) . ' = ?';
        $query = "UPDATE {$table} SET {$set} WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute($values);

        $colString = implode(', ', $columns);
        $query = "SELECT {$colString} FROM {$table} WHERE id = {$id}";
        $updated = $pdo->query($query)->fetch(PDO::FETCH_ASSOC);
        
        if(json_encode($input) == json_encode($updated)) {
            echo '{"success": true}';
        } else {
            echo json_encode($input), json_encode($updated);
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
            }
            http_response_code(500);
            print_r($e);
            exit();
        }
    }

    // $method = $_GET['real-method']
    //     ?? strtolower($_SERVER['REQUEST_METHOD']);
    $method = strtolower($_SERVER['REQUEST_METHOD']);
    // echo $method;
    usePDO($method, [$table]);
}