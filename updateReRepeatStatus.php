<?php
declare(strict_types=1);

function updateReRepeatStatus(PDO $pdo, array $constsAndVars, int $repeatListLength) {
    $dif = $constsAndVars['numberToRepeat'] - $repeatListLength;
    if($dif < 1) {
        return;
    }
    $newStatus = $constsAndVars['reRepeatStatus'] + ($dif * 2);

    $query = "UPDATE jap_words_const_vars
        SET value = {$newStatus}
        WHERE name = 'reRepeatStatus'";
    $pdo->exec($query);
}