<?php
use Jgauthi\Component\Utils\Date;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

const LOCALISATION = 'fr_FR';

$date = Date::new('2023-05-16 11:30:00');  // Any DateTimeInterface
var_dump(Date::translate($date, 'l d M Y à H:i:s', LOCALISATION));
// result: mardi 16 mai 2023 à 11:30:00

$date = Date::new('2023-05-20 13:15:00');
var_dump($date->export('l d M Y à H:i:s', LOCALISATION)); // export current DateTime $var
// result: samedi 20 mai 2023 à 13:15:00