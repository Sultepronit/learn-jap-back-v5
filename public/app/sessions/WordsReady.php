<?php
declare(strict_types=1);

require_once 'words.php';

class WordsReady
{
    private static $data = [];

    private static $confirmDivisor = 4;
    // private static $recognizeDivisor = 5;

    private static $learnStages = [
        'LEARN' => 'learn',
        'CONFIRM' => 'confirm',
        'REPEAT' => 'repeat',
        'AUTOREPEAT' => 'autorepeat',
        // 'RECOGNIZE' => 'recognize',
    ];

    private static function assignLearnStage(array &$list, string $stageLabel): void
    {
        foreach($list as &$card) {
            $card['learnStage'] = self::$learnStages[$stageLabel];
        }
    }

    private static function prepareLearnList(): array
    {
        $learnList = self::$data['learnList'];
        self::assignLearnStage($learnList, 'LEARN');
        return $learnList;
    }

    private static function getPartOfList(string $listName, int $length, string $stageLabel): array
    {
        shuffle(self::$data[$listName]);
        $result = array_slice(self::$data[$listName], 0, $length);
        self::assignLearnStage($result, $stageLabel);

        return $result;
    }

    private static function prepareRepeatList(int $repeatNumber): array
    {
        shuffle(self::$data['repeatList']);
        $repeatList = [];

        $normal = 0;
        foreach(self::$data['repeatList'] as $card) {
            if(
                $card['fProgress'] === 0 && $card['fAutorepeat']
                || $card['fProgress'] > 0 && $card['bAutorepeat']
            ) {
                $card['learnStage'] = self::$learnStages['AUTOREPEAT'];
            } else {
                $card['learnStage'] = self::$learnStages['REPEAT'];
                $normal++;
            }

            $repeatList[] = $card;
            if($normal >= $repeatNumber) break;
        }

        return $repeatList;
    }

    public static function prepare(PDO $pdo): array
    {
        self::$data = words($pdo);

        $learnList = self::prepareLearnList();
        $learnNumber = count($learnList);

        $confirmNumber = (int) ceil(count(self::$data['confirmList']) / self::$confirmDivisor);
        $confirmList = self::getPartOfList('confirmList', $confirmNumber, 'CONFIRM');

        // $recognizeNumber = (int) ceil(count(self::$data['recognizeList']) / self::$recognizeDivisor);
        // $recognizeList = self::getPartOfList('recognizeList', $recognizeNumber, 'RECOGNIZE');

        $session = [...$learnList, ...$confirmList];

        $repeatNumber = self::$data['constsAndVars']['sessionLength'] - count($session);
        $repeatList = self::prepareRepeatList($repeatNumber);

        $session = [...$session, ...$repeatList];
        shuffle($session);

        $plan = compact('learnNumber', 'confirmNumber', 'repeatNumber');
        
        return compact('plan', 'session');
    }
}
