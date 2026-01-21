<?php
declare(strict_types=1);

function returnForgottenWord(PDO $pdo) {
    $query = 'SELECT * FROM words WHERE repeat_status = -2;';
    $stmt = $pdo->query($query);
    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // print_r(json_encode($list));

    $len = count($list);
    // print_r($list[$len - 1]);
    $index = random_int(0, $len + 10);
    // echo $index . PHP_EOL;
    if ($index >= $len) return;

    $id = $list[$index]['id'];
    // echo $id . PHP_EOL;
    $query = "UPDATE words SET repeat_status = 0 WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    // echo json_encode($list[$index]);
    // exit;
}