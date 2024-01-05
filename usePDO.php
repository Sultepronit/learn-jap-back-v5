<?php
declare(strict_types=1);

function usePDO(callable $action, array $args = []) {
    require_once '../env.php';
    try {
        $pdo = new PDO(
            "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']}",
            $env['DB_USER'],
            $env['DB_PASS']
        );

        call_user_func_array($action, [$pdo, ...$args]);  
    } catch (\Throwable $th) {
        http_response_code(500);
        print_r($th);
        // exit();
    } finally {
        $pdo = null;
    }
}