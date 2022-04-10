<?php
use Jgauthi\Component\Utils\{Folder, Strings};

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$dir = realpath(__DIR__.'/../'); // Example/

$arch = Folder::getArchitecture($dir);
?>

<h3>Output Html format:</h3>
<blockquote>
    <?=Folder::displayArchitectureHtml($arch)?>
</blockquote>

<h3>Output Markdown format:</h3>
<blockquote>
    <?=nl2br(Strings::trim(Folder::displayArchitectureMarkdown($arch)))?>
</blockquote>
