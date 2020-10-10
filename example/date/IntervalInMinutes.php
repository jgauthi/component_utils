<?php
use Jgauthi\Component\Utils\Date;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$start = new DateTime('2012-07-08 11:14:15');
$end = new DateTime('2012-07-08 12:45:52');
// $end = $start->add( new \DateInterval() );

$interval1 = $start->diff($end);
$interval1->diff_minute = Date::IntervalInMinutes($interval1);

$interval2 = $end->diff($start);
$interval2->diff_minute = Date::IntervalInMinutes($interval2);

var_dump($start, $end, $interval1, $interval2);