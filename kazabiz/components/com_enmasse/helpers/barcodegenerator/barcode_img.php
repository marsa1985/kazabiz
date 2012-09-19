<?php

require_once("Barcode.php");

$num = isset($_REQUEST['num']) ? $_REQUEST['num'] : '1-3-1';
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'code128';
$imgtype = isset($_REQUEST['imgtype']) ? $_REQUEST['imgtype'] : 'png';

Image_Barcode::draw($num, $type, $imgtype);

?>
