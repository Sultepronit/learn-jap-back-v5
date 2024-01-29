<?php
declare(strict_types=1);

function isKanji($char) {
    return $char > 'ー';
}

function linksToJson($card) {
    $card['links'] = json_encode($card['links']);
    $card['otherLinks'] = json_encode($card['otherLinks']);
    return $card;
}

function selectKanji(PDO $pdo) {
    # get the db
    $query = "SELECT id, kanji, links, otherLinks FROM collected_kanji;";
    $stmt = $pdo->query($query);
    $theDb = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // print_r($theDb);

    # prepare the $updatedList
    $updatedList = [];
    foreach($theDb as $row) {
        $updatedList[$row['kanji']] = ['links' => [], 'otherLinks' => []];
    }
    // print_r($updatedList);

    # get words
    $query = "SELECT cardNumber, altWriting, writings, rareWritings
        FROM jap_words
        WHERE learnStatus >= 0";
    $stmt = $pdo->query($query);
    $words = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // print_r($words);

    # fill the $updatedList
    foreach($words as $word) {
        # handle normal writings
        $unique = [];
        if(!$word['altWriting']) {
            foreach(mb_str_split($word['writings'], 1, 'UTF-8') as $char) {
                if(isKanji($char)) {
                    $unique[$char] = true;
                }
            }

            foreach(array_keys($unique) as $kanji) {
                $updatedList[$kanji]['links'][] = $word['cardNumber'];
            }
        } else {
            $word['rareWritings'] .= $word['writings'];
        }

        # handle additional writings
        $other = [];
        foreach(mb_str_split($word['rareWritings'], 1, 'UTF-8') as $char) {
            if(isKanji($char) && !in_array($char, array_keys($unique))) {
                $other[$char] = true;
            }
        }
        // print_r($other);

        foreach(array_keys($other) as $kanji) {
            if(in_array($kanji, array_keys($updatedList))) {
                $updatedList[$kanji]['otherLinks'][] = $word['cardNumber'];
            }
        }
    }
    // print_r($updatedList);

    # create lists to update existing and create new cards
 
    $theDbLength = count($theDb);
    
    $newCards = array_slice($updatedList, $theDbLength);
    print_r($newCards);

    $updatedList = array_slice($updatedList, 0, $theDbLength);

    function updateLinks($newCard, $oldCard, $links) {
        if($newCard[$links] !==  $oldCard[$links]) {
            // echo $links, ': ', $newCard[$links], PHP_EOL;
            $query = "UPDATE collected_kanji
                SET {$links} = $newCard[$links]
                WHERE id = {$oldCard['id']};";
            echo $query, PHP_EOL;
        }
    }

    $i = 0;
    foreach($updatedList as $kanji => $newCard) {
        $newCard = linksToJson($newCard);
        updateLinks($newCard, $theDb[$i], 'links');
        updateLinks($newCard, $theDb[$i], 'otherLinks');
        $i++;
    }
}