<?php
declare(strict_types=1);

function kanji(PDO $pdo) {
    # session consts and vars
    $query = 'SELECT * FROM collected_kanji_consts_vars;';
    $stmt = $pdo->query($query);
    $constsAndVars = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    # repeat list
    $query = "SELECT * FROM collected_kanji
        WHERE repeatStatus < {$constsAndVars['reRepeatStatus']}
        AND repeatStatus > 0;";
    $stmt = $pdo->query($query);
    $repeatList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    updateReRepeatStatus($pdo, 'collected_kanji', $constsAndVars, count($repeatList));

    # learn list
    $query = "SELECT * FROM collected_kanji
    WHERE repeatStatus < 1;";
    $stmt = $pdo->query($query);
    $learnList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'sessionLength' => $constsAndVars['sessionLength'],
        'repeatList' => $repeatList,
        'learnList' => $learnList,
    ];
}