<?php
use Jgauthi\Component\Utils\Arrays;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

// Var
$array = ['Hello', 'World', 'PHP'];
$data = json_decode(file_get_contents(__DIR__.'/../asset/clients.json'), true);

$tab = [
    $array,
    $array,
    $array,
    $array,
    $array,
    $array,
];

var_dump(
    $tab,
    Arrays::values_recursive($tab),
    array_values((array) $data),
    Arrays::values_recursive($data)
);
