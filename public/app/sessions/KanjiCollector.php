<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/Set.php';

class KanjiCollector
{
    private static PDO $pdo;

    private static array $oldList = [];
    private static array $words = [];
    private static array $jooyoo = [];

    private static array $kanjiMap = [];

    private static array $changes = [
        'updated' => null,
        'created' => []
    ];

    private static function getTheLists(): void
    {
        $query = "SELECT id, kanji, links, other_links FROM kanji;";
        self::$oldList = self::$pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

        $query = "SELECT cardNumber, altWriting, writings, rareWritings
            FROM words
            WHERE learnStatus >= 0";
        self::$words = self::$pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

        $query = "SELECT kanji, readings FROM jooyoo";
        self::$jooyoo = self::$pdo->query($query)->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    private static function prepareKanjiMap(): void
    {
        foreach(self::$oldList as $row) {
            self::$kanjiMap[$row['kanji']] = ['links' => [], 'other_links' => []];
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
                self::writingsToLinks($word, $rareWritings, 'other_links');
            }
        }
    }

    private static function processCard($card): ?array
    {
        if(!isset($card['links'])) {
            return null;
        }

        if(!isset($card['other_links'])) {
            $card['other_links'] = [];
        }

        $filtered = array_diff($card['other_links'], $card['links']);
        $card['other_links'] = array_values($filtered);
    
        $result['links'] = json_encode($card['links']);
        $result['other_links'] = json_encode($card['other_links']);

        return $result;
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

    private static function updateLinks(int $id, array $newCard, string $linksType) {
        $query = "UPDATE kanji
            SET {$linksType} = '$newCard[$linksType]'
            WHERE id = {$id}";
        self::$pdo->exec($query);
    }

    private static function saveChanges(): void
    {
        self::$changes['updated'] = new Set();

        $linksTypes = ['links', 'other_links'];
        foreach($linksTypes as $linksType) {
            foreach(self::$oldList as $oldCard) {
                $kanji = $oldCard['kanji'];
                if($oldCard[$linksType] !== self::$kanjiMap[$kanji][$linksType]) {
                    self::$changes['updated']->add($kanji);

                    self::updateLinks($oldCard['id'], self::$kanjiMap[$kanji], $linksType);
                }
            }
        }
    }

    private static function insertCard(array $card): void
    {
        $query = "INSERT INTO kanji
                (kanji, readings, links, other_links)
                VALUES (?, ?, ?, ?)";
            $stmt = self::$pdo->prepare($query);
            $stmt->execute([
                $card['kanji'],
                $card['readings'],
                $card['links'],
                $card['other_links']
            ]);
    }
    
    private static function createNew(): void
    {
        $newCards = array_slice(self::$kanjiMap, count(self::$oldList));

        foreach($newCards as $kanji => $card) {
            self::$changes['created'][] = $kanji;

            $card['kanji'] = $kanji;
            $card['readings'] = self::$jooyoo[$kanji] ?? '';

            self::insertCard($card);
        }
    }

    public static function collect(PDO $pdo)
    {
        self::$pdo = $pdo;

        self::getTheLists();
        self::prepareKanjiMap();
        self::fillKanjiMap();
        self::processKanjiMap();
        self::saveChanges();
        self::createNew();

        return self::$changes;
    }
}