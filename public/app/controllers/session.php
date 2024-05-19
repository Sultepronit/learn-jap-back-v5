<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/updateReRepeatStatus.php';
require_once __DIR__ . '/../sessions/words.php';
require_once __DIR__ . '/../sessions/kanji.php';
require_once __DIR__ . '/../sessions/wordsForKanji.php';

function session(string $sessionName, PDO $pdo)
{
    // if($sessionName === '')
    $names = [
        'words-for-kanji' => 'wordsForKanji'
    ];

    $functionName = isset($names[$sessionName]) ? $names[$sessionName] : $sessionName;

    return $functionName($pdo);
}