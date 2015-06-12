<?php
require_once 'src/Document.php';
require_once 'src/Writer.php';
require_once 'src/IndirectObjectManager.php';
require_once 'src/IndirectObject.php';
require_once 'src/IndirectObject/Catalog.php';
require_once 'src/IndirectObject/Pages.php';
require_once 'src/IndirectObject/Page.php';

$om = new IndirectObjectManager();
$writer = new Writer($om);
$doc = new Document($om);
$doc->output($writer);
