<?php
declare(strict_types=1);

$pdo = new PDO('sqlite:' . __DIR__ . '/db.sqlite');

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

// camelColumn_to_snake('kanji', 'repeatStatus');
// camelColumn_to_snake('kanji', 'otherLinks');

// camelToSnake('sessionLength');

// echo PHP_EOL;

function snakeToCamel(string $snake_term) {
    return lcfirst(
        str_replace(' ', '',
            ucwords(str_replace('_', ' ', $snake_term))
        )
    );
}

// echo snakeToCamel('session_length');
// echo snakeToCamel('session');

echo PHP_EOL;