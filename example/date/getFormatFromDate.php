<?php
use Jgauthi\Component\Utils\Date;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$values = [
    '2015-03',
    '01/05/2015',
    2012,
    '2013-1-3',
];

foreach ($values as $value) {

    $date = Date::new($value);
    $originalFormat = Date::getFormatFromDate($value);
    var_dump("Date: $value ==> Original format: {$originalFormat}");
}