<?php
declare(strict_types=1);

// require_once 'updateReRepeatStatus.php';
// sss

function kanjiSession(PDO $pdo) {
    # session consts and vars
    $query = 'SELECT * FROM collected_kanji_consts_vars;';
    $stmt = $pdo->query($query);
    $constsAndVars = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    // $constsAndVars = [
    //     'sessionLength' => 60,
    //     'reRepeatStatus' => 15000
    // ];

    # repeat list
    $query = "SELECT * FROM collected_kanji
        WHERE repeatStatus < {$constsAndVars['reRepeatStatus']}
        AND progress > -1;";
    $stmt = $pdo->query($query);
    $repeatList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // updateReRepeatStatus($pdo, $constsAndVars, count($repeatList));

    # repeat-problem list
    $query = "SELECT * FROM collected_kanji
    WHERE repeatStatus < {$constsAndVars['reRepeatStatus']}
    AND progress < 0;";
    $stmt = $pdo->query($query);
    $problemList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    # words
    $query = "SELECT altWriting, writings, rareWritings, readings, rareReadings, translation
    FROM jap_words
    WHERE learnStatus >= 0";
    $stmt = $pdo->query($query);
    $words = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'sessionLength' => $constsAndVars['sessionLength'],
        'repeatList' => $repeatList,
        'problemList' => $problemList,
        'words' => $words
    ]);
}