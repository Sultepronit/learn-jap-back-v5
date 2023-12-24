<?php
declare(strict_types=1);

function httpCrud($table) {
    function receiveInput() {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    function get(PDO $pdo, string $table) {
        receiveInput();
        $data = $pdo
            ->query("SELECT * FROM {$table};")
            ->fetchAll(PDO::FETCH_ASSOC);
            
        echo json_encode($data);
    }

    function patch(PDO $pdo, string $table) {
        $id = $_GET['id'] ?? null;
        $input = receiveInput();

        // echo $id, PHP_EOL;
        // print_r($input);

        $columns = array_keys($input);
        $values = array_values($input);
        $values[] = $id;

        $set = implode(' = ? ', $columns) . ' = ?';
        $query = "UPDATE {$table} SET {$set} WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute($values);

        // echo $query . PHP_EOL;

        $colStting = implode(', ', $columns);
        $query = "SELECT {$colStting} FROM {$table} WHERE id = {$id}";
        $updated = $pdo->query($query)->fetch(PDO::FETCH_ASSOC);
        
        // echo $query . PHP_EOL;
        // print_r($updated);
        // echo json_encode($input), json_encode($updated), PHP_EOL;
        // echo json_encode($input) == json_encode($updated), PHP_EOL;
        if(json_encode($input) == json_encode($updated)) {
            echo '{"success": true}';
        }
    }

    function post(PDO $pdo, string $table) {
        $id = $_GET['id'] ?? null;
        echo 'we did it!' . PHP_EOL;
        echo $id, PHP_EOL;
        $input = receiveInput();
        print_r($input);
    }

    // function options() {} # for the OPTIONS method

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