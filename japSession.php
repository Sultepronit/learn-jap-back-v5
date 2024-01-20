<?php
declare(strict_types=1);

// require_once 'updateReRepeatStatus.php';
function updateReRepeatStatus(PDO $pdo, array $constsAndVars, int $repeatListLength) {
    $dif = $constsAndVars['numberToRepeat'] - $repeatListLength;
    if($dif < 1) {
        return;
    }
    $newStatus = $constsAndVars['reRepeatStatus'] + ($dif * 2);

    $query = "UPDATE jap_words_consts_vars
        SET value = {$newStatus}
        WHERE name = 'reRepeatStatus'";
    $pdo->exec($query);
}

function japSession(PDO $pdo) {
    # session consts and vars
    $query = 'SELECT * FROM jap_words_consts_vars;';
    $stmt = $pdo->query($query);
    $constsAndVars = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    # learn list
    $query = 'SELECT * FROM jap_words WHERE learnStatus = 0;';
    $stmt = $pdo->query($query);
    $learnList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    # confirm list
    $query = 'SELECT * FROM jap_words WHERE learnStatus = 1;';
    $stmt = $pdo->query($query);
    $confirmList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    # repeat list
    $query = "SELECT * FROM jap_words
        WHERE learnStatus BETWEEN 2 AND {$constsAndVars['reRepeatStatus']}
        AND fProgress >= 0 AND bProgress >= 0;";
    $stmt = $pdo->query($query);
    $repeatList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    updateReRepeatStatus($pdo, $constsAndVars, count($repeatList));

    # repeat-problem list
    $query = "SELECT * FROM jap_words
        WHERE learnStatus BETWEEN 2 AND {$constsAndVars['reRepeatStatus']}
        AND (fProgress < 0 OR bProgress < 0);";
    $stmt = $pdo->query($query);
    $problemList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    # recognize list
    $query = "SELECT * FROM jap_words
        WHERE learnStatus > {$constsAndVars['reRepeatStatus']}
        AND fStats < 1";
    $stmt = $pdo->query($query);
    $recognizeList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'constsAndVars' => $constsAndVars,
        'learnList' => $learnList,
        'confirmList' => $confirmList,
        'repeatList' => $repeatList,
        'problemList' => $problemList,
        'recognizeList' => $recognizeList
    ]);
}