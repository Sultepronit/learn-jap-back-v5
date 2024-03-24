<?php
declare(strict_types=1);

function renumberAfterDelete(PDO $pdo, string $table) {
    $data = $pdo
        ->query("SELECT id, cardNumber FROM {$table};")
        ->fetchAll(PDO::FETCH_ASSOC);

    $index = 0;
    foreach($data as $row) {
        $index++;
        if($row['cardNumber'] != $index) {
            $query = "UPDATE {$table}
                SET cardNumber = {$index}
                WHERE id = {$row['id']}";
            $pdo->exec($query);
        }
    }
}