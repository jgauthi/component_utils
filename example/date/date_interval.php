<?php
use Jgauthi\Component\Utils\Date;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$start = DateTime::createFromFormat('Y-m-d', '2009-01-26');
$end = DateTime::createFromFormat('Y-m-d', '2009-02-03');
$dates = Date::date_interval($start, $end);

var_dump("Date interval entre {$start->format('d/m/Y')} et {$end->format('d/m/Y')}", $dates);
