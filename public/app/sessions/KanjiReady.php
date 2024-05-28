<?php
declare(strict_types=1);

require_once 'kanji.php';

class KanjiReady
{
    private static PDO $pdo;

    private static $data = [];

    private static $autorepeated = 0;

    private static $repeatStages = [
        'LEARN' => 'learn',
        'REPEAT' => 'repeat',
    ];

    private static function prepareLearnList(): array
    {
        $learnList = self::$data['learnList'];

        foreach($learnList as &$card) {
            $card['repeatStage'] = self::$repeatStages['LEARN'];
        }

        return $learnList;
    }

    private static function autorepeat(array $card)
    {
        self::$autorepeated++;

        $newStatus = updateNextRepeatStatus(self::$pdo, 'collected_kanji');

        $query = "UPDATE collected_kanji
        SET repeatStatus = {$newStatus}, autorepeat = 0
        WHERE id = {$card['id']}";

        self::$pdo->exec($query);
    }

    private static function prepareRepeatList(int $repeatNumber): array
    {
        shuffle(self::$data['repeatList']);
        $repeatList = [];

        $normal = 0;
        foreach(self::$data['repeatList'] as $card) {
            if($card['links'] === '[]') {
                continue;
            }

            if($card['autorepeat']) {
                self::autorepeat($card);
                continue;
            }

            $card['repeatStage'] = self::$repeatStages['REPEAT'];
            $normal++;
            $repeatList[] = $card;
            if($normal >= $repeatNumber) break;
        }

        return $repeatList;
        return [];
    }

    public static function prepare(PDO $pdo): array
    {
        self::$pdo = $pdo;
        self::$data = kanji($pdo);

        $learnList = self::prepareLearnList();
        $learnNumber = count($learnList);

        $repeatNumber = self::$data['sessionLength'] -  $learnNumber;
        $repeatList = self::prepareRepeatList($repeatNumber);

        $session = [...$learnList, ...$repeatList];
        shuffle($session);

        $autorepeated = self::$autorepeated;
        $plan = compact('learnNumber', 'repeatNumber', 'autorepeated');
        
        return compact('plan', 'session');
    }
}
