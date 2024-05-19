<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/updateReRepeatStatus.php';
require_once __DIR__ . '/../sessions/words.php';
require_once __DIR__ . '/../sessions/kanji.php';
require_once __DIR__ . '/../sessions/wordsForKanji.php';
require_once __DIR__ . '/../sessions/KanjiCollector.php';

function session(string $sessionName, PDO $pdo)
{
    // if($sessionName === '')
    
    // $names = [
    //     'words-for-kanji' => 'wordsForKanji'
    // ];

    // $functionName = isset($names[$sessionName]) ? $names[$sessionName] : $sessionName;
    
    switch ($sessionName) {
        case 'kanji':
            return kanji($pdo);
        case 'words': 
            return words($pdo);
        case 'words-for-kanji':
            return wordsForKanji($pdo);
        case 'collect-kanji':
            return KanjiCollector::collect($pdo);
        default:
            return ['404' => 'Nothing here!'];
    }
}