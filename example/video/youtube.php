<?php
use Jgauthi\Component\Utils\Youtube;
use Symfony\Component\HttpClient\HttpClient;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$apikey = null; // Please add an Api Key from google console
$videoId = 'l153UtpCKEI'; // Mozart

if (!empty($apikey)) {
    $client = HttpClient::create();
    $videoInfo = Youtube::getInfo($apikey, $client, $videoId, true);

    var_dump($videoInfo);
}

echo Youtube::player($videoId);