<?php
declare(strict_types=1);

require_once 'words.php';

$learnStages = [
    'LEARN' => 'learn',
    'CONFIRM' => 'confirm',
    'REPEAT' => 'repeat',
    'AUTOREPEAT' => 'autorepeat',
    'RECOGNIZE' => 'recognize',
];

function wordsMin(PDO $pdo)
{
    $data = words($pdo);

    $confirmDivisor = 4;
    $recognizeDivisor = 5;

    global $learnStages;

    function assignLearnStage(array &$list, string $stageLabel): void
    {
        global $learnStages;
        foreach($list as &$card) {
            $card['learnStage'] = $learnStages[$stageLabel];
        }
    }

    

    $learnList = $data['learnList'];
    assignLearnStage($learnList, 'LEARN');

    $confirmNumber = (int) count($data['confirmList']) / $confirmDivisor;
    shuffle($data['confirmList']);
    $confirmList = array_slice($data['confirmList'], 0, $confirmNumber);
    assignLearnStage($confirmList, 'CONFIRM');

    $recognizeNumber = (int) ceil(count($data['recognizeList']) / $recognizeDivisor);
    shuffle($data['recognizeList']);
    $recognizeList = array_slice($data['recognizeList'], 0, $recognizeNumber);
    assignLearnStage($recognizeList, 'RECOGNIZE');

    $lesson = [...$learnList, ...$confirmList, ...$recognizeList];

    $repeatNumber = $data['constsAndVars']['sessionLength'] - count($lesson);
    shuffle($data['repeatList']);
    $repeatList = [];
    $normal = 0;
    foreach($data['repeatList'] as $card) {
        if(
            $card['fProgress'] === 0 && $card['fAutorepeat']
            || $card['fProgress'] > 0 && $card['bAutorepeat']
        ) {
            $card['learnStage'] = $learnStages['AUTOREPEAT'];
        } else {
            $card['learnStage'] = $learnStages['REPEAT'];
            $normal++;
        }

        $repeatList[] = $card;
        if($normal >= $repeatNumber) break;
    }

    $lesson = [...$lesson, ...$repeatList];

    shuffle($lesson);

    return [
        'learnStages' => $learnStages,
        'lesson' => $lesson,
        'confirmNumber' => $confirmNumber,
        // 'repeatList'=> $repeatList
    ];
}
