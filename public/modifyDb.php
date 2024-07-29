<?php
declare(strict_types=1);

$pdo = new PDO('sqlite:' . __DIR__ . '/db.sqlite');

// $renameTable = "ALTER TABLE collected_kanji RENAME TO kanji";
// $renameTable = "ALTER TABLE collected_kanji_consts_vars RENAME TO kanji_consts_vars";
// $renameTable = "ALTER TABLE jap_words RENAME TO words";
// $renameTable = "ALTER TABLE jap_words_consts_vars RENAME TO words_consts_vars";
// echo $pdo->exec($renameTable);

// $deleteRow = "DELETE FROM words_consts_vars WHERE `name` = 'problemDivisor'";
// echo $pdo->exec($deleteRow);

function camelToSnake(string $camelTerm) {
    return strtolower(
        preg_replace('/([a-z])([A-Z])/', '$1_$2', $camelTerm)
    );
}

function renameColumn($table, $old, $new) {
    global $pdo;
    $query = "ALTER TABLE {$table} RENAME {$old} TO {$new}";
    echo $pdo->exec($query);
}

function camelColumn_to_snake($table, $camelTerm) {
    renameColumn($table, $camelTerm, camelToSnake($camelTerm));
}

camelColumn_to_snake('kanji', 'repeatStatus');
camelColumn_to_snake('kanji', 'otherLinks');

camelColumn_to_snake('words', 'cardNumber');
renameColumn('words', 'learnStatus', 'repeat_status');
camelColumn_to_snake('words', 'fProgress');
camelColumn_to_snake('words', 'bProgress');
renameColumn('words', 'fStats', 'f_record');
renameColumn('words', 'bStats', 'b_record');
camelColumn_to_snake('words', 'fAutorepeat');
camelColumn_to_snake('words', 'bAutorepeat');
camelColumn_to_snake('words', 'altWriting');
camelColumn_to_snake('words', 'rareWritings');
camelColumn_to_snake('words', 'rareReadings');


echo PHP_EOL;