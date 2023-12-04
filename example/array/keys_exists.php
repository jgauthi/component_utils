<?php
use Jgauthi\Component\Utils\Arrays;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$data = [
    ['id' => 1, 'title' => 'Lorem ipsu', 'quantity' => 3],
    ['id' => 2, 'title' => 'Dolor Color'],
    ['id' => 3, 'title' => 'John Doe', 'quantity' => 2, 'price' => 10.5],
];
$required = ['id', 'title', 'quantity'];

var_dump('Keys required: '. implode(', ', $required));

foreach ($data as $key => $value) {
    $data[$key]['Fields required'] = Arrays::keys_exists($required, $value) ? 'OK' : 'Echec';
    var_dump($data[$key]);
}
