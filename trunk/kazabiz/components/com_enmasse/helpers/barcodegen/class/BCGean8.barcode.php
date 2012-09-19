<?php
/**
 *--------------------------------------------------------------------
 *
 * Sub-Class - EAN-8
 *
 * EAN-8 contains
 *	- 4 digits
 *	- 3 digits
 *	- 1 checksum
 *
 * The checksum is always displayed.
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGBarcode1D.php');

class BCGean8 extends BCGBarcode1D {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->keys = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

		// Left-Hand Odd Parity starting with a space
		// Right-Hand is the same of Left-Hand starting with a bar
		$this->code = array(
			'2100',	/* 0 */
			'1110',	/* 1 */
			'1011',	/* 2 */
			'0300',	/* 3 */
			'0021',	/* 4 */
			'0120',	/* 5 */
			'0003',	/* 6 */
			'0201',	/* 7 */
			'0102',	/* 8 */
			'2001'	/* 9 */
		);
	}

	/**
	 * Draws the barcode.
	 *
	 * @param resource $im
	 */
	public function draw($im) {
		$c = strlen($this->text);
		
		// Checksum
		$this->calculateChecksum();
		$temp_text = $this->text . $this->keys[$this->checksumValue];
		
		// Starting Code
		$this->drawChar($im, '000', true);
		
		// Draw First 4 Chars (Left-Hand)
		for ($i = 0; $i < 4; $i++) {
			$this->drawChar($im, $this->findCode($temp_text[$i]), false);
		}
		
		// Draw Center Guard Bar
		$this->drawChar($im, '00000', false);
		
		// Draw Last 4 Chars (Right-Hand)
		for ($i = 4; $i < 8; $i++) {
			$this->drawChar($im, $this->findCode($temp_text[$i]), true);
		}

		// Draw Right Guard Bar
		$this->drawChar($im, '000', true);
		$this->drawText($im);
	}

	/**
	 * Returns the maximal size of a barcode.
	 *
	 * @return int[]
	 */
	public function getMaxSize() {
		$p = parent::getMaxSize();

		$startlength = 3 * $this->scale;
		$centerlength = 5 * $this->scale;
		$textlength = 8 * 7 * $this->scale;
		$endlength = 3 * $this->scale;
		return array($p[0] + $startlength + $centerlength + $textlength + $endlength, $p[1]);
	}

	/**
	 * Validates the input.
	 */
	protected function validate() {
		$c = strlen($this->text);
		if($c === 0) {
			throw new BCGParseException('ean8', 'No data has been entered.');
		}

		// Checking if all chars are allowed
		for ($i = 0; $i < $c; $i++) {
			if (array_search($this->text[$i], $this->keys) === false) {
				throw new BCGParseException('ean8', 'The character \'' . $this->text[$i] . '\' is not allowed.');
			}
		}

		// If we have 8 chars just flush the last one
		if ($c === 8) {
			$this->text = substr($this->text, 0, 7);
		} elseif ($c !== 7) {
			throw new BCGParseException('ean8', 'Must contain 7 digits, the 8th digit is automatically added.');
		}

		parent::validate();
	}

	/**
	 * Overloaded method to calculate checksum.
	 */
	protected function calculateChecksum() {
		// Calculating Checksum
		// Consider the right-most digit of the message to be in an "odd" position,
		// and assign odd/even to each character moving from right to left
		// Odd Position = 3, Even Position = 1
		// Multiply it by the number
		// Add all of that and do 10-(?mod10)
		$odd = true;
		$this->checksumValue = 0;
		$c = strlen($this->text);
		for ($i = $c; $i > 0; $i--) {
			if ($odd === true) {
				$multiplier = 3;
				$odd = false;
			} else {
				$multiplier = 1;
				$odd = true;
			}
	
			if (!isset($this->keys[$this->text[$i - 1]])) {
				return;
			}

			$this->checksumValue += $this->keys[$this->text[$i - 1]] * $multiplier;
		}

		$this->checksumValue = (10 - $this->checksumValue % 10) % 10;
	}

	/**
	 * Overloaded method to display the checksum.
	 */
	protected function processChecksum() {
		if ($this->checksumValue === false) { // Calculate the checksum only once
			$this->calculateChecksum();
		}

		if ($this->checksumValue !== false) {
			return $this->keys[$this->checksumValue];
		}

		return false;
	}

	/**
	 * Overloaded method for drawing special label.
	 *
	 * @param resource $im
	 */
	protected function drawText($im) {
		if ($this->label !== BCGBarcode1D::AUTO_LABEL) {
			parent::drawText($im);
		} elseif ($this->label !== '') {
			$temp_text = $this->text . $this->keys[$this->checksumValue];
			if ($this->textfont instanceof BCGFont) {
				$code1 = 0;
				$code2 = 0;

				$this->textfont->setText($temp_text);
				$this->drawExtendedBars($im, $this->textfont->getHeight(), $code1, $code2);

				// We need to separate the text, one on the left and one on the right
				$text1 = substr($temp_text, 0, 4);
				$text2 = substr($temp_text, 4, 4);
				$font1 = clone $this->textfont;
				$font2 = clone $this->textfont;
				$font1->setText($text1);
				$font2->setText($text2);

				// The $this->res offset is to center without thinking of the white space in the guard
				$xPosition1 = ($this->scale * 30 - $font1->getWidth()) / 2 + $code1 * $this->scale + $this->offsetX * $this->scale;
				$xPosition2 = ($this->scale * 30 - $font2->getWidth()) / 2 + $code2 * $this->scale + $this->offsetX * $this->scale;
				$yPosition = $this->thickness * $this->scale + $this->textfont->getHeight() + BCGBarcode1D::SIZE_SPACING_FONT + $this->offsetY * $this->scale;

				$text_color = $this->colorFg->allocate($im);
				$font1->draw($im, $text_color, $xPosition1, $yPosition);
				$font2->draw($im, $text_color, $xPosition2, $yPosition);
			} elseif ($this->textfont !== 0) {
				$code1 = 0;
				$code2 = 0;

				$this->drawExtendedBars($im, 9, $code1, $code2);

				$xPosition1 = ($this->scale * 30 - imagefontwidth($this->textfont) * 4) / 2 + $code1 * $this->scale + $this->offsetX * $this->scale;
				$xPosition2 = ($this->scale * 30 - imagefontwidth($this->textfont) * 4) / 2 + $code2 * $this->scale + $this->offsetX * $this->scale;
				$yPosition = $this->thickness * $this->scale + 1 * $this->scale + $this->offsetY * $this->scale;

				$text_color = $this->colorFg->allocate($im);
				imagestring($im, $this->textfont, $xPosition1, $yPosition, substr($temp_text, 0, 4), $text_color);
				imagestring($im, $this->textfont, $xPosition2, $yPosition, substr($temp_text, 4, 4), $text_color);
			}
		}
	}

	/**
	 * Draws the extended bars on the image.
	 *
	 * @param resource $im
	 * @param int $plus
	 * @param int $code1
	 * @param int $code2
	 */
	private function drawExtendedBars(&$im, $plus, &$code1, &$code2) {
		$rememberX = $this->positionX;
		$rememberH = $this->thickness;

		// We increase the bars
		$this->thickness = $this->thickness + ceil($plus / $this->scale);
		$this->positionX = 0;
		$this->drawSingleBar($im, BCGBarcode::COLOR_FG);
		$this->positionX += 2;
		$this->drawSingleBar($im, BCGBarcode::COLOR_FG);
		$code1 = $this->positionX;

		// Center Guard Bar
		$this->positionX += 30;
		$this->drawSingleBar($im, BCGBarcode::COLOR_FG);
		$this->positionX += 2;
		$this->drawSingleBar($im, BCGBarcode::COLOR_FG);

		// Last Bars
		$code2 = $this->positionX;
		$this->positionX += 30;
		$this->drawSingleBar($im, BCGBarcode::COLOR_FG);
		$this->positionX += 2;
		$this->drawSingleBar($im, BCGBarcode::COLOR_FG);

		$this->positionX = $rememberX;
		$this->thickness = $rememberH;
	}
}
?>