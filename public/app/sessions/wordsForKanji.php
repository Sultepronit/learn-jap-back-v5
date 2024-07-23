<?php
declare(strict_types=1);

function wordsForKanji(PDO $pdo) {
    $query = "SELECT alt_writing, writings, rare_writings, readings, rare_readings, translation
    FROM words
    WHERE repeat_status >= 0";
    $stmt = $pdo->query($query);
    $words = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return tableToCamelCase($words);
}