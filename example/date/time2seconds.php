<?php
use Jgauthi\Component\Utils\Date;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$time = Date::time2seconds('01:05:12');
$time2 = Date::time2seconds( (Date::new())->setTime(0, 18, 53) );

var_dump($time, $time2);
