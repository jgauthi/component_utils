<?php
use Jgauthi\Component\Utils\Date;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$dates = ['2019-02-32', '2019-02-19', '2048-02-03', '31-01-2018', '2041-13-15'];

foreach ($dates as $date) {
    $result = 'OK';
    try {
        $dateCheck = Date::DateTime($date);
    } catch (Exception $e) {
        $result = "false (<em>{$e->getMessage()}</em>)";
    }

    try {
        $dateFuture = Date::DateTime($date, null, true);
        $isFuture = 'OK';
    } catch (Exception $e) {
        $isFuture = "false (<em>{$e->getMessage()}</em>)";
    }

    ?><p>
        <?=$date?>: <?php var_dump($dateCheck); ?>
        Test Date: <strong><?=$dateCheck?></strong>,
        is Future: <strong><?=$isFuture?></strong>,
        Result: <?=$result?>
    </p><?php
}
