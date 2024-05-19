<?php
declare(strict_types=1);

function updateReRepeatStatus(
        PDO $pdo,
        string $table,
        array $constsAndVars,
        int $repeatListLength
    ) {
    $dif = $constsAndVars['numberToRepeat'] - $repeatListLength;
    if($dif < 1) {
        return;
    }
    $newStatus = $constsAndVars['reRepeatStatus'] + ($dif * 2);

    $query = "UPDATE {$table}_consts_vars
        SET value = {$newStatus}
        WHERE name = 'reRepeatStatus'";
    $pdo->exec($query);
}