<?php
declare(strict_types=1);

function kanji(PDO $pdo) {
    # session consts and vars
    $query = 'SELECT * FROM kanji_consts_vars;';
    $stmt = $pdo->query($query);
    $constsAndVars = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    # repeat list
    $query = "SELECT * FROM kanji
        WHERE repeat_status < {$constsAndVars['reRepeatStatus']}
        AND repeat_status > 0;";
    $stmt = $pdo->query($query);
    $repeatList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    updatereRepeatStatus($pdo, 'kanji', $constsAndVars, count($repeatList));

    # learn list
    $query = "SELECT * FROM kanji
    WHERE repeat_status < 1;";
    $stmt = $pdo->query($query);
    $learnList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'sessionLength' => $constsAndVars['sessionLength'],
        'repeatList' => $repeatList,
        'learnList' => $learnList,
    ];
}