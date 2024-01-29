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

function updateLinks($pdo, $newCard, $oldCard, $links) {
    if($newCard[$links] !==  $oldCard[$links]) {
        // echo $links, ': ', $newCard[$links], PHP_EOL;
        $query = "UPDATE collected_kanji
            SET {$links} = $newCard[$links]
            WHERE id = {$oldCard['id']};";
        echo $query, PHP_EOL;
        // $pdo->exec($query);
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

function setUnique($array, $newVal) {
    $array[] = $newVal;
}

class Set implements IteratorAggregate
{
    public $data = [];

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    public function add(mixed $newEntry): void
    {
        if(!in_array($newEntry, $this->data)) {
            $this->data[] = $newEntry;
        }
    }

    public function has(mixed $item): bool
    {
        return in_array($item, $this->data);
    }
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
        // $unique = [];
        $unique = new Set();
        if(!$word['altWriting']) {
            foreach(mb_str_split($word['writings'], 1, 'UTF-8') as $char) {
                if(isKanji($char)) {
                    // $unique[$char] = true;
                    $unique->add($char);
                }
            }

            foreach($unique as $kanji) {
                $updatedList[$kanji]['links'][] = $word['cardNumber'];
            }
        } else {
            $word['rareWritings'] .= $word['writings'];
        }

        # handle additional writings
        // $other = [];
        $other = new Set();
        foreach(mb_str_split($word['rareWritings'], 1, 'UTF-8') as $char) {
            if(isKanji($char) && !$unique->has($char)) {
                // $other[$char] = true;
                $other->add($char);
            }
        }

        foreach($other as $kanji) {
            if(in_array($kanji, array_keys($updatedList))) {
                $updatedList[$kanji]['otherLinks'][] = $word['cardNumber'];
            }
        }
    }
    // print_r($updatedList);

    # check for changes & save to db
 
    $theDbLength = count($theDb);

    $newCards = array_slice($updatedList, $theDbLength);
    print_r($newCards);

    $updatedList = array_slice($updatedList, 0, $theDbLength);
    updateChanges($pdo, $updatedList, $theDb);
}