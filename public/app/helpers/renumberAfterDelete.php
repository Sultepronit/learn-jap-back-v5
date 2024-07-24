<?php
declare(strict_types=1);

function renumberAfterDelete(PDO $pdo, string $table) {
    $data = $pdo
        ->query("SELECT id, card_number FROM {$table};")
        ->fetchAll(PDO::FETCH_ASSOC);

    $index = 0;
    foreach($data as $row) {
        $index++;
        if($row['card_number'] != $index) {
            $query = "UPDATE {$table}
                SET card_number = {$index}
                WHERE id = {$row['id']}";
            $pdo->exec($query);
        }
    }
}