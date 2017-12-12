<?php
 error_reporting(E_ALL);
ini_set('display_errors', 1);
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'liliamri_wp1',
    'username' => 'root',
    'password' => '123',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);