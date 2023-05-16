<?php
use Jgauthi\Component\Utils\Date;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

const LOCALISATION = 'fr_FR';

$date = Date::new('2023-05-16 11:30:00');
echo Date::export($date, 'l d M Y à H:i:s', LOCALISATION);
// result: mardi 16 mai 2023 à 11:30:00