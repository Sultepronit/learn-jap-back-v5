<?php
declare(strict_types=1);

require_once 'Set.php';
require_once 'Changes.php';

function writingsToLinks($word, &$updatedList, $writings, $links) {
    $unique = new Set();
    foreach(mb_str_split($word[$writings], 1, 'UTF-8') as $char) {
        if($char > 'ー') {
            $unique->add($char);
        }
    }

    foreach($unique as $kanji) {
        $updatedList[$kanji][$links][] = $word['cardNumber'];
    }
}

function linksToJson($card) {
    $filtered = array_diff($card['otherLinks'], $card['links']);
    $card['otherLinks'] = array_values($filtered); // for old php!

    $card['links'] = json_encode($card['links']);
    $card['otherLinks'] = json_encode($card['otherLinks']);
    return $card;
}

function updateLinks($pdo, $newCard, $oldCard, $links) {
    if($newCard[$links] !== $oldCard[$links]) {
        Changes::addUpdated($oldCard['kanji']);
        
        $query = "UPDATE collected_kanji
            SET {$links} = '$newCard[$links]'
            WHERE id = {$oldCard['id']}";
        $pdo->exec($query);
    }
}

function updateChanges($pdo, $updatedList, $theDb) {
    $i = 0;
    foreach($updatedList as $newCard) {
        $newCard = linksToJson($newCard);
        updateLinks($pdo, $newCard, $theDb[$i], 'links');
        updateLinks($pdo, $newCard, $theDb[$i], 'otherLinks');
        $i++;
    }
}

function createNew(PDO $pdo, array $cards, array $jooyoo) {
    foreach($cards as $kanji => $card) {
        if(!array_key_exists('links', $card)) {
            continue;
        }

        Changes::addCreated($kanji);

        $card['kanji'] = $kanji;
        $card['readings'] = $jooyoo[$kanji] ?? '';

        if(!array_key_exists('otherLinks', $card)) {
            $card['otherLinks'] = [];
        }

        $card = linksToJson($card);

        $query = "INSERT INTO collected_kanji
            (kanji, readings, links, otherLinks)
            VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $card['kanji'],
            $card['readings'],
            $card['links'],
            $card['otherLinks']
        ]);
    }
}

function selectKanji(PDO $pdo) {
    # get the db
    $query = "SELECT id, kanji, links, otherLinks FROM collected_kanji;";
    $stmt = $pdo->query($query);
    $theDb = $stmt->fetchAll(PDO::FETCH_ASSOC);

    # prepare the $updatedList
    $updatedList = [];
    foreach($theDb as $row) {
        $updatedList[$row['kanji']] = ['links' => [], 'otherLinks' => []];
    }

    # get words
    $query = "SELECT cardNumber, altWriting, writings, rareWritings
        FROM jap_words
        WHERE learnStatus >= 0";
    $stmt = $pdo->query($query);
    $words = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    # get jooyoo
    $query = "SELECT kanji, readings FROM jooyoo";
    $jooyoo = $pdo->query($query)->fetchAll(PDO::FETCH_KEY_PAIR);
    // print_r($jooyoo);

    # fill the $updatedList
    foreach($words as $word) {
        if(!$word['altWriting']) {
            writingsToLinks($word, $updatedList, 'writings', 'links');
        } else {
            $word['rareWritings'] .= $word['writings'];
        }

        writingsToLinks($word, $updatedList, 'rareWritings', 'otherLinks');
    }

    # check for changes & save to db
    $theDbLength = count($theDb);

    $newCards = array_slice($updatedList, $theDbLength);
    createNew($pdo, $newCards, $jooyoo);

    $updatedList = array_slice($updatedList, 0, $theDbLength);
    updateChanges($pdo, $updatedList, $theDb);

    Changes::printResult();
}