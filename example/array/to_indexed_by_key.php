<?php
use Jgauthi\Component\Utils\Arrays;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$data = [
    ['id' => 1, 'title' => 'Lorem ipsu', 'quantity' => 3],
    ['id' => 2, 'title' => 'Dolor Color', 'quantity' => 1],
    ['id' => 3, 'title' => 'John Doe', 'quantity' => 2],
];

$newArray = Arrays::to_indexed_by_key($data, 'id');
var_dump(
    $data,
    $newArray
);