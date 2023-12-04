<?php
use Jgauthi\Component\Utils\Arrays;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$array1 = ['color' => ['favorite' => 'red'], 5];
$array2 = [10, 'color' => ['favorite' => 'green', 'blue']];

// $array = Arrays::combine($array1, $array2); // deprecated
$array = Arrays::mergeTree($array1, $array2); // use instead

// $array = ['color' => ['favorite' => 'red', 'blue'], 5];
var_dump($array);
