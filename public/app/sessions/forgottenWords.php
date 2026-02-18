<?php
declare(strict_types=1);

function returnForgottenWord(PDO $pdo) {
    $query = 'SELECT id FROM words WHERE repeat_status = -2;';
    $stmt = $pdo->query($query);
    $list = $stmt->fetchAll(PDO::FETCH_COLUMN);
    // print_r(json_encode($list));
    $len = count($list);
    
    for ($i = 0; $i < $len / 20; $i++) {
        $index = random_int(0, $len + 10);
        // echo $index . PHP_EOL;
        if ($index >= $len) continue;

        $id = $list[$index];
        // echo $id . PHP_EOL;
        
        $query = "UPDATE words SET repeat_status = 0 WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
    }
    
    // exit;
}