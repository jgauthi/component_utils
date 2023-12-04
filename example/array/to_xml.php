<?php
use Jgauthi\Component\Utils\Arrays;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$data = json_decode(file_get_contents(__DIR__.'/../asset/clients.json'), true);
echo Arrays::to_xml($data);
