<?php
declare(strict_types=1);

function snakeToCamel(string $snake_term) {
    return lcfirst(
        str_replace(' ', '',
            ucwords(str_replace('_', ' ', $snake_term))
        )
    );
}

function camelToSnake(string $camelTerm) {
    return strtolower(
        preg_replace('/([a-z])([A-Z])/', '$1_$2', $camelTerm)
    );
}

function changeRowTitles(array $row, string $toWhat) {
    $codeToCallback = [
        'camel' => 'snakeToCamel',
        'snake' => 'camelToSnake'
    ];

    $callback = $codeToCallback[$toWhat];

    $result = [];
    foreach($row as $key => $value) {
        $newKey = call_user_func($callback, $key);
        $result[$newKey] = $value;
    }

    return $result;
}

function tableToCamelCase(array $table) {
    return array_map(function($row) {
        return changeRowTitles($row, 'camel');
    }, $table);
}