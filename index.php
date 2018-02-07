<?php

require_once __DIR__ . '/vendor/autoload.php';

require_once 'lib/phpQuery.php';

$a = new \core\Parser();
$content = $a->getWebPage();
//var_dump($content);
$elements = $a->getPageElements($content);
var_dump($elements);
