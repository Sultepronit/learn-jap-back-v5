<?php
declare(strict_types=1);

function kanjiForWords(PDO $pdo) {
    $query = "SELECT kanji, links, otherLinks
        FROM collected_kanji";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // $result = array_map(function($card) {
    //     // print_r($card);
    //     return [
    //         $card['kanji'] => [
    //             'links' => $card['links'],
    //             'otherLinks' => $card['otherLinks']
    //         ]
    //     ];
    // }, $data);
    $result = [];
    foreach($data as $card) {
        $result[$card['kanji']] = [
            'links' => $card['links'],
            'otherLinks' => $card['otherLinks']
        ];
    }

    // echo '<pre>';
    // print_r($result);

    return $result;
}