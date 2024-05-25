<?php
declare(strict_types=1);

require_once 'helpers/updateReRepeatStatus.php';
require_once 'sessions/words.php';
require_once 'sessions/kanji.php';
require_once 'sessions/wordsForKanji.php';
require_once 'sessions/kanjiForWords.php';
require_once 'sessions/KanjiCollector.php';
require_once 'sessions/wordsMin.php';
require_once 'sessions/WordsReady.php';

function session(string $sessionName, PDO $pdo)
{    
    switch ($sessionName) {
        case 'kanji':
            return kanji($pdo);
            
        case 'words': 
            return words($pdo);

        case 'words-for-kanji':
            return wordsForKanji($pdo);

        case 'kanji-for-words':
            return kanjiForWords($pdo);

        case 'collect-kanji':
            return KanjiCollector::collect($pdo);

        case 'words-min':
            return wordsMin($pdo);
        
        case 'words-ready':
            return WordsReady::prepare($pdo);
            
        default:
            return ['404' => 'Nothing here!'];
    }
}