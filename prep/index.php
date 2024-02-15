<?php
$dbImport = file_get_contents('jap_words (3).sql');
$pdo = new PDO('sqlite:' . __DIR__ . '/../jap.sqlite');
print_r($pdo);

// $pdo->exec('drop table jap_words');
// $pdo->exec($dbImport);

// $query = "UPDATE jap_words_consts_vars
//     SET value = 9261
//     WHERE name = 'nextRepeatStatus'";
// $pdo->exec($query);

// $thedb = $pdo->query('SELECT * FROM collected_kanji')->fetchAll(PDO::FETCH_ASSOC);
// $thedb = $pdo->query('SELECT * FROM collected_kanji_consts_vars')->fetchAll(PDO::FETCH_ASSOC);
$thedb = $pdo->query('SELECT * FROM jap_words_consts_vars')->fetchAll(PDO::FETCH_KEY_PAIR);
echo '<pre>';
print_r($thedb);

echo 'success';