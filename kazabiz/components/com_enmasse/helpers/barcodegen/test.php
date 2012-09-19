<?php
// Including all required classes
require('class/BCGFont.php');
require('class/BCGColor.php');
require('class/BCGDrawing.php');

// Including the barcode technology
include('class/BCGcode128.barcode.php');
$num = $_GET['num'];
// Loading Font
$font = new BCGFont('./class/font/Arial.ttf', 13);

// The arguments are R, G, B for color.
$color_black = new BCGColor(0, 0, 0);
$color_white = new BCGColor(255, 255, 255);

$drawException = null;
try {
	$code = new BCGcode128();
	//$code->setScale(1.5); // Resolution
	$code->setThickness(40); // Thickness
	$code->setForegroundColor($color_black); // Color of bars
	$code->setBackgroundColor($color_white); // Color of spaces
	$code->setFont($font); // Font (or 0)
	$code->parse($num); // Text
} catch(Exception $exception) {
	$drawException = $exception;
}

/* Here is the list of the arguments
1 - Filename (empty : display on screen)
2 - Background color */
$drawing = new BCGDrawing('', $color_white);
if($drawException) {
	$drawing->drawException($drawException);
} else {
	$drawing->setBarcode($code);
	$drawing->draw();
}

// Header that says it is an image (remove it if you save the barcode to a file)
header('Content-Type: image/png');

// Draw (or save) the image into PNG format.
$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
?>