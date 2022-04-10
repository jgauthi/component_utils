<?php
use Jgauthi\Component\Utils\Date;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$timestamp = [
    Date::fromParts(1994, 2, 26, 4, 15, 32),
    Date::fromParts(day: 2, month: 12, year: 1994, hour: 11, minute: 11, second: 11),
    Date::new('3 may 1999 15:02'),
    Date::get_timestamp_from_date('31/05/1999 14h25'),
    Date::get_timestamp_from_date(date('Y-m-d H:i:s')),
    Date::get_timestamp_from_date('2012-04-02'),
    Date::get_timestamp_from_date('8 Nov 2015 12:32 PM')
];
$date = [];

foreach ($timestamp as $datetime) {
    if ($datetime instanceof DateTimeInterface) {
        $ts = $datetime->getTimestamp();
        $date[$ts] = $datetime;
        continue;
    }

    $ts = $datetime;
    $date[$ts] = date('d/m/Y H\hi:s', $ts);
}

var_dump($date);
