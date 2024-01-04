<?php

function addNextRepeatStatus(PDO $pdo, string $table) {
    if($table === 'jap_words') {
        $query = "SELECT value FROM jap_words_const_vars
            WHERE name = 'nextRepeatStatus';";
        $newStatus = $pdo->query($query)->fetchColumn() + 1;
        // print_r($newStatus);

        $query = "UPDATE jap_words_const_vars
            SET value = {$newStatus}
            WHERE name = 'nextRepeatStatus'";
        $pdo->exec($query);

        return $newStatus;
    }
}