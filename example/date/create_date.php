<?php
use Jgauthi\Component\Utils\Date;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$timestamp = [
    mktime(20, 45, 0, 4, 20, 2010),
    (Date::new('3 may 1999 15:02'))->getTimestamp(),
    Date::get_timestamp_from_date('31/05/1999 14h25'),
    Date::get_timestamp_from_date(date('Y-m-d H:i:s')),
    Date::get_timestamp_from_date('2012-04-02'),
    Date::get_timestamp_from_date('8 Nov 2015 12:32 PM')
];
$date = [];

foreach ($timestamp as $ts) {
    $date[$ts] = date('d/m/Y H\hi:s', $ts);
}

var_dump($date);
