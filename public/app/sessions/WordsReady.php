<?php
declare(strict_types=1);

require_once 'words.php';

class WordsReady
{
    private static $data = [];

    private static $confirmDivisor = 4;

    private static $repeatStages = [
        'LEARN' => 'learn',
        'CONFIRM' => 'confirm',
        'REPEAT' => 'repeat',
        'AUTOREPEAT' => 'autorepeat',
    ];

    private static function assignRepeatStage(array &$list, string $stageLabel): void
    {
        foreach($list as &$card) {
            $card['repeatStage'] = self::$repeatStages[$stageLabel];
        }
    }

    private static function prepareLearnList(): array
    {
        $learnList = self::$data['learnList'];
        self::assignRepeatStage($learnList, 'LEARN');
        return $learnList;
    }

    private static function getPartOfList(string $listName, int $length, string $stageLabel): array
    {
        shuffle(self::$data[$listName]);
        $result = array_slice(self::$data[$listName], 0, $length);
        self::assignRepeatStage($result, $stageLabel);

        return $result;
    }

    private static function prepareRepeatList(int $repeatNumber): array
    {
        shuffle(self::$data['repeatList']);
        $repeatList = [];

        $normal = 0;
        foreach(self::$data['repeatList'] as $card) {
            if(
                $card['f_progress'] === 0 && $card['f_autorepeat']
                || $card['f_progress'] > 0 && $card['b_autorepeat']
            ) {
                $card['repeatStage'] = self::$repeatStages['AUTOREPEAT'];
            } else {
                $card['repeatStage'] = self::$repeatStages['REPEAT'];
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

        $session = [...$learnList, ...$confirmList];

        $repeatNumber = self::$data['constsAndVars']['sessionLength'] - count($session);
        $repeatList = self::prepareRepeatList($repeatNumber);

        $session = [...$session, ...$repeatList];
        $session = tableToCamelCase($session);
        shuffle($session);

        $plan = compact('learnNumber', 'confirmNumber', 'repeatNumber');
        
        return compact('plan', 'session');
    }
}
