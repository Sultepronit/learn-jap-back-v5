<?php
declare(strict_types=1);

function wordsForKanji(PDO $pdo) {
    # words
    $query = "SELECT altWriting, writings, rareWritings, readings, rareReadings, translation
    FROM jap_words
    WHERE learnStatus >= 0";
    $stmt = $pdo->query($query);
    $words = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($words);
}