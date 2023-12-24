<?php
declare(strict_types=1);

// function read($table, $id = null, $input = null) {
function read($table) {
    $query = "SELECT * FROM {$table};";
    echo $query, '<br>';
}