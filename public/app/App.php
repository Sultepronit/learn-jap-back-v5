<?php
declare(strict_types=1);

class App
{
    public function run()
    {
        echo '<pre>';
        // print_r($_SERVER);

        $ruri = $_SERVER['REQUEST_URI'] ?? '';
        echo $ruri . PHP_EOL;

        $rarray = explode('/', $ruri);

        $subject = $rarray[2];
        echo $subject . PHP_EOL;

        $req = array_slice($rarray, 3);
        print_r($req);

        // call_user_method();
        // call_user_method_array();
    }
}