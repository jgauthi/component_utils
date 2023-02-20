<?php
use Jgauthi\Component\Utils\Numeric;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$numbers = [1.90, 5.50124553, "83.62"];
?>
<ul>
    <?php foreach ($numbers as $number): ?>
        <li>
            Original number: <?=$number?>,
            Number format (french format): <?=Numeric::number_format_fr($number)?>€
        </li>
    <?php endforeach ?>
</ul>
