<?php

function updateNextRepeatStatus(PDO $pdo, string $table) {
    $query = "SELECT value FROM {$table}_consts_vars
        WHERE name = 'nextRepeatStatus';";
    $newStatus = $pdo->query($query)->fetchColumn() + 1;

    $query = "UPDATE {$table}_consts_vars
        SET value = {$newStatus}
        WHERE name = 'nextRepeatStatus'";
    $pdo->exec($query);

    return $newStatus;
}

function addNextRepeatStatus(array $input, PDO $pdo, string $table) {
    if(isset($input['repeatStatus']) && $input['repeatStatus'] == 33) {
        $input['repeatStatus'] = updateNextRepeatStatus($pdo, $table);
    }

    return $input;
}