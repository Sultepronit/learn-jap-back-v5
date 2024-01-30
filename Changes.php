<?php
declare(strict_types=1);

class Changes
{
    private static array $cards = [
        'updated' => [],
        'created' => []
    ];

    public static function addUpdated(string $kanji): void
    {
        self::$cards['updated'][] = $kanji;
    }

    public static function addCreated(string $kanji): void
    {
        self::$cards['created'][] = $kanji;
    }

    public static function printResult(): void
    {
        echo json_encode(self::$cards);
    }
}