<?php
declare(strict_types=1);

function kanjiForWords(PDO $pdo) {
    $query = "SELECT kanji, links, other_links
        FROM kanji";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    foreach($data as $card) {
        $result[$card['kanji']] = [
            'links' => $card['links'],
            'otherLinks' => $card['other_links']
        ];
    }

    return $result;
}