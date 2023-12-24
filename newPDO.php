<?php

function newPDO() {
    require_once '../env.php';
    return new PDO(
        "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']}",
        $env['DB_USER'],
        $env['DB_PASS']);
}