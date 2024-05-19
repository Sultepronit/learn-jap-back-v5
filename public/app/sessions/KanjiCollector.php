<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/Set.php';

class KanjiCollector
{
    private static array $oldList = [];
    private static array $words = [];
    private static array $jooyoo = [];

    private static array $kanjiMap = [];
    private static array $newList = [];

    private static int $oldListLength = 0;

    private static array $changes = [
        'updated' => [],
        'created' => []
    ];

    private static function getTheLists($pdo): void
    {
        $query = "SELECT id, kanji, links, otherLinks FROM collected_kanji;";
        self::$oldList = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

        $query = "SELECT cardNumber, altWriting, writings, rareWritings
            FROM jap_words
            WHERE learnStatus >= 0";
        self::$words = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

        $query = "SELECT kanji, readings FROM jooyoo";
        self::$jooyoo = $pdo->query($query)->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    private static function prepareKanjiMap(): void
    {
        foreach(self::$oldList as $row) {
            self::$kanjiMap[$row['kanji']] = ['links' => [], 'otherLinks' => []];
        }
    }

    private static function writingsToLinks(array $word, string $writings, string $linksType)
    {
        $unique = new Set();
        foreach(mb_str_split($writings, 1, 'UTF-8') as $char) {
            if($char > 'ー') {
                $unique->add($char);
            }
        }
    
        foreach($unique as $kanji) {
            self::$kanjiMap[$kanji][$linksType][] = $word['cardNumber'];
        }
    }

    private static function fillKanjiMap(): void
    {
        foreach(self::$words as $word) {
            $rareWritings = $word['rareWritings'];

            if(!$word['altWriting']) {
                self::writingsToLinks($word, $word['writings'], 'links');
            } else {
                $rareWritings .= $word['writings'];
            }
            
            if($rareWritings) {
                self::writingsToLinks($word, $rareWritings, 'otherLinks');
            }
        }
    }

    private static function processCard($card): ?array
    {
        if(!isset($card['links'])) {
            return null;
        }

        $filtered = array_diff($card['otherLinks'], $card['links']);
        $card['otherLinks'] = array_values($filtered);
    
        $result['links'] = json_encode($card['links']);
        $result['otherLinks'] = json_encode($card['otherLinks'] ?? []);

        return $result;
    }

    private static function fillNewList(): void
    {
        foreach(self::$kanjiMap as $kanji => $card) {
            $processedCard = self::processCard($card);

            if($processedCard) {
                self::$newList[] = [
                    'kanji' => $kanji,
                    ...$processedCard
                ];
            }
        }
    }

    private static function processKanjiMap(): void
    {
        $filtered = [];

        foreach(self::$kanjiMap as $kanji => $card) {
            $processedCard = self::processCard($card);

            if($processedCard) {
                $filtered[$kanji] = $processedCard;
            }
        }

        self::$kanjiMap = $filtered;
    }

    // function updateLinks($pdo, $newCard, $oldCard, $links) {
    //     if($newCard[$links] !== $oldCard[$links]) {
    //         Changes::addUpdated($oldCard['kanji']);
            
    //         $query = "UPDATE collected_kanji
    //             SET {$links} = '$newCard[$links]'
    //             WHERE id = {$oldCard['id']}";
    //         $pdo->exec($query);
    //     }
    // }

    private static function saveChanges(): void
    {
        $linkTypes = ['links', 'otherLinks'];
        foreach(self::$oldList as $oldCard) {
            foreach($linkTypes as $linkType) {
                $kanji = $oldCard['kanji'];
                if($oldCard[$linkType] !== self::$kanjiMap[$kanji][$linkType]) {
                    self::$changes['updated'][] = $kanji;

                    echo $oldCard[$linkType], ' -> ', self::$kanjiMap[$kanji][$linkType];
                    // echo 'something!';
                }
            }
        }
        // for($i = 0; $i < self::$oldListLength; $i++) {
        //     if(self::$oldList[$i]['kanji'] !== self::$newList[$i]['kanji']) {
        //         echo 'Something crazy have happend!';
        //     }

        //     foreach($linkTypes as $linkType) {
        //         if(self::$oldList[$i][$linkType] !== self::$newList[$i][$linkType]) {
        //             self::$changes['updated'][] = self::$oldList[$i]['kanji'];

        //             // echo 'something!';
        //             echo self::$oldList[$i]['kanji'], ' ', $linkType, ': ';
        //             echo self::$oldList[$i][$linkType], ' -> ', self::$newList[$i][$linkType], '<br>';

        //         }
        //     }
        // }
    }
    
    public static function collect(PDO $pdo)
    {
        // echo 'here we go!';
        self::getTheLists($pdo);
        self::$oldListLength = count(self::$oldList);
        self::prepareKanjiMap();
        self::fillKanjiMap();
        self::processKanjiMap();
        // self::fillNewList();
        self::saveChanges();

        echo '<pre>';
        // print_r(self::$words);
        // print_r(self::$kanjiMap);
        // print_r(self::$newList);
        print_r(self::$changes);
        echo 'Happy End!';
    }
}