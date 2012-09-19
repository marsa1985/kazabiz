<?php
/**
 *--------------------------------------------------------------------
 *
 * Sub-Class - UPC-E
 *
 * You can provide a UPC-A code (without dash), the code will transform
 * it into a UPC-E format if it's possible.
 * UPC-E contains
 *	- 1 system digits (not displayed but coded with parity)
 *	- 6 digits
 *	- 1 checksum digit (not displayed but coded with parity)
 *
 * The text returned is the UPC-E without the checksum.
 * The checksum is always displayed.
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGBarcode1D.php');

class BCGupce extends BCGBarcode1D {
	protected $codeParity = array();
	protected $upce;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->keys = array('0','1','2','3','4','5','6','7','8','9');

		// Odd Parity starting with a space
		// Even Parity is the inverse (0=0012) starting with a space
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

		// Parity, 0=Odd, 1=Even for manufacturer code. Depending on 1st System Digit and Checksum
		$this->codeParity = array(
			array(
				array(1, 1, 1, 0, 0, 0),	/* 0,0 */
				array(1, 1, 0, 1, 0, 0),	/* 0,1 */
				array(1, 1, 0, 0, 1, 0),	/* 0,2 */
				array(1, 1, 0, 0, 0, 1),	/* 0,3 */
				array(1, 0, 1, 1, 0, 0),	/* 0,4 */
				array(1, 0, 0, 1, 1, 0),	/* 0,5 */
				array(1, 0, 0, 0, 1, 1),	/* 0,6 */
				array(1, 0, 1, 0, 1, 0),	/* 0,7 */
				array(1, 0, 1, 0, 0, 1),	/* 0,8 */
				array(1, 0, 0, 1, 0, 1)		/* 0,9 */
			),
			array(
				array(0, 0, 0, 1, 1, 1),	/* 0,0 */
				array(0, 0, 1, 0, 1, 1),	/* 0,1 */
				array(0, 0, 1, 1, 0, 1),	/* 0,2 */
				array(0, 0, 1, 1, 1, 0),	/* 0,3 */
				array(0, 1, 0, 0, 1, 1),	/* 0,4 */
				array(0, 1, 1, 0, 0, 1),	/* 0,5 */
				array(0, 1, 1, 1, 0, 0),	/* 0,6 */
				array(0, 1, 0, 1, 0, 1),	/* 0,7 */
				array(0, 1, 0, 1, 1, 0),	/* 0,8 */
				array(0, 1, 1, 0, 1, 0)		/* 0,9 */
			)
		);
	}

	/**
	 * Parses the text before displaying it.
	 *
	 * @param mixed $text
	 */
	public function parse($text) {
		parent::parse($text);
	
		$this->setLabelOffset();
	}

	/**
	 * Sets the font.
	 *
	 * @param mixed $font BCGFont or int
	 */
	public function setFont($font) {
		parent::setFont($font);

		$this->setLabelOffset();
	}

	/**
	 * Sets the label.
	 * You can use BCGBarcode1D::AUTO_LABEL to have the label automatically written based on the parsed text.
	 *
	 * @param string $label
	 */
	public function setLabel($label) {
		parent::setLabel($label);

		$this->setLabelOffset();
	}

	/**
	 * Sets the X offset.
	 *
	 * @param int $offsetX
	 */
	public function setOffsetX($offsetX) {
		parent::setOffsetX($offsetX);

		$this->setLabelOffset();
	}

	/**
	 * Sets the scale of the barcode in pixel.
	 * If the scale is lower than 1, an exception is raised.
	 *
	 * @param int $scale
	 */
	public function setScale($scale) {
		parent::setScale($scale);

		$this->setLabelOffset();
	}

	/**
	 * Draws the barcode.
	 *
	 * @param resource $im
	 */
	public function draw($im) {
		$this->calculateChecksum();

		// Starting Code
		$this->drawChar($im, '000', true);
		$c = strlen($this->upce);
		for ($i = 0; $i < $c; $i++) {
			$this->drawChar($im, self::inverse($this->findCode($this->upce[$i]), $this->codeParity[$this->text[0]][$this->checksumValue][$i]), false);
		}

		// Draw Center Guard Bar
		$this->drawChar($im, '00000', false);

		// Draw Right Bar
		$this->drawChar($im, '0', true);
		$this->text = $this->text[0] . $this->upce;
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
		$textlength = 6 * 7 * $this->scale;
		$endlength = $this->scale;
		$lastcharlength = $this->getEndPosition() + 2;

		return array($p[0] + $startlength + $centerlength + $textlength + $endlength + $lastcharlength, $p[1]);
	}

	/**
	 * Validates the input.
	 */
	protected function validate() {
		$c = strlen($this->text);
		if($c === 0) {
			throw new BCGParseException('upce', 'No data has been entered.');
		}

		// Checking if all chars are allowed
		for ($i = 0; $i < $c; $i++) {
			if (array_search($this->text[$i], $this->keys) === false) {
				throw new BCGParseException('upce', 'The character \'' . $this->text[$i] . '\' is not allowed.');
			}
		}

		// Must contain 11 chars
		// Must contain 6 chars (if starting with upce directly)
		// First Chars must be 0 or 1
		if ($c !== 11 && $c !== 6) {
			throw new BCGParseException('upce', 'You must provide a UPC-A (11 characters) or a UPC-E (6 characters).');
		} elseif ($this->text[0] !== '0' && $this->text[0] !== '1' && $c !== 6) {
			throw new BCGParseException('upce', 'UPC-A must start with 0 or 1 to be converted to UPC-E.');
		}

		// Convert part
		$this->upce = '';
		if ($c !== 6) {
			// Checking if UPC-A is convertible
			$temp1 = substr($this->text, 3, 3);
			if ($temp1 === '000' || $temp1 === '100' || $temp1 === '200') { // manufacturer code ends with 100,200 or 300
				if (substr($this->text, 6, 2) === '00') { // Product must start with 00
					$this->upce = substr($this->text, 1, 2) . substr($this->text, 8, 3) . substr($this->text, 3, 1);
				}
			} elseif (substr($this->text, 4, 2) === '00') { // manufacturer code ends with 00
				if (substr($this->text, 6, 3) === '000') { // Product must start with 000
					$this->upce = substr($this->text, 1, 3) . substr($this->text, 9, 2) . '3';
				}
			} elseif (substr($this->text, 5, 1) === '0') { // manufacturer code ends with 0
				if (substr($this->text, 6, 4) === '0000') { // Product must start with 0000
					$this->upce = substr($this->text, 1, 4) . substr($this->text, 10, 1) . '4';
				}
			} else { // No zero leading at manufacturer code
				$temp2 = intval(substr($this->text, 10, 1));
				if (substr($this->text, 6, 4) === '0000' && $temp2 >= 5 && $temp2 <= 9) { // Product must start with 0000 and must end by 5,6,7,8 or 9
					$this->upce = substr($this->text, 1, 5) . substr($this->text, 10, 1);
				}
			}
		} else {
			$this->upce = $this->text;
		}

		if ($this->upce === '') {
			throw new BCGParseException('upce', 'Your UPC-A can\'t be converted to UPC-E.');
		}

		if ($c === 6) {
			$upca = '';

			// We convert UPC-E to UPC-A to find the checksum
			if ($this->text[5] === '0' || $this->text[5] === '1' || $this->text[5] === '2') {
				$upca = substr($this->text, 0, 2) . $this->text[5] . '0000' . substr($this->text, 2, 3);
			} elseif ($this->text[5] === '3') {
				$upca = substr($this->text, 0, 3) . '00000' . substr($this->text, 3, 2);
			} elseif ($this->text[5] === '4') {
				$upca = substr($this->text, 0, 4) . '00000' . $this->text[4];
			} else {
				$upca = substr($this->text, 0, 5) . '0000' . $this->text[5];
			}

			$this->text = '0' . $upca;
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

				$this->textfont->setText($temp_text);
				$this->drawExtendedBars($im, $this->textfont->getHeight(), $code1);

				// We need to separate the text, one on the left and one on the right, one starting and one ending
				$text0 = substr($temp_text, 0, 1);
				$text1 = substr($temp_text, 1, 6);
				$text2 = substr($temp_text, 7, 1);
				$font0 = clone $this->textfont;
				$font1 = clone $this->textfont;
				$font2 = clone $this->textfont;
				$font0->setText($text0);
				$font1->setText($text1);
				$font2->setText($text2);

				$xPosition0 = $this->offsetX * $this->scale - $font0->getWidth() - 4; // -4 is just for beauty;
				$xPosition2 = $this->offsetX * $this->scale + $this->positionX * $this->scale + 2;
				$yPosition0 = $this->thickness * $this->scale + $font0->getHeight() / 2 + $this->offsetY * $this->scale;

				$xPosition1 = ($this->scale * 46 - $font1->getWidth()) / 2 + $code1 * $this->scale + $this->offsetX * $this->scale;
				$yPosition = $this->thickness * $this->scale + $this->textfont->getHeight() + BCGBarcode1D::SIZE_SPACING_FONT + $this->offsetY * $this->scale;

				$text_color = $this->colorFg->allocate($im);
				$font0->draw($im, $text_color, $xPosition0, $yPosition0);
				$font1->draw($im, $text_color, $xPosition1, $yPosition);
				$font2->draw($im, $text_color, $xPosition2, $yPosition0);
			} elseif ($this->textfont !== 0) {
				$code1 = 0;

				$this->drawExtendedBars($im, 9, $code1);

				$xPosition0 = $this->offsetX * $this->scale - imagefontwidth($this->textfont);
				$xPosition2 = $this->offsetX * $this->scale + $this->positionX * $this->scale + 2;
				$yPosition0 = $this->thickness * $this->scale - imagefontheight($this->textfont) / 2 + $this->offsetY * $this->scale;

				$xPosition1 = ($this->scale * 46 - imagefontwidth($this->textfont) * 6) / 2 + $code1 * $this->scale + $this->offsetX * $this->scale;
				$yPosition = $this->thickness * $this->scale + $this->offsetY * $this->scale;

				$text_color = $this->colorFg->allocate($im);
				imagechar($im, $this->textfont, $xPosition0, $yPosition0, $temp_text[0], $text_color);
				imagestring($im, $this->textfont, $xPosition1, $yPosition, substr($temp_text, 1, 6), $text_color);
				imagechar($im, $this->textfont, $xPosition2, $yPosition0, $temp_text[7], $text_color);
			}
		}
	}

	/**
	 * Draws the extended bars on the image.
	 *
	 * @param resource $im
	 * @param int $plus
	 * @param int $code1
	 */
	private function drawExtendedBars($im, $plus, &$code1) {
		$rememberX = $this->positionX;
		$rememberH = $this->thickness;

		// We increase the bars
		$this->thickness = $this->thickness + ceil($plus / $this->scale);
		$this->positionX = 0;
		$this->drawSingleBar($im, BCGBarcode::COLOR_FG);
		$this->positionX += 2;
		$this->drawSingleBar($im, BCGBarcode::COLOR_FG);
		$code1 = $this->positionX;

		// Last Bars
		$this->positionX += 46;
		$this->drawSingleBar($im, BCGBarcode::COLOR_FG);
		$this->positionX += 2;
		$this->drawSingleBar($im, BCGBarcode::COLOR_FG);

		$this->positionX = $rememberX;
		$this->thickness = $rememberH;
	}

	/**
	 * Gets the width of the text.
	 *
	 * @return int
	 */
	private function getEndPosition() {
		if ($this->label === BCGBarcode1D::AUTO_LABEL) {
			$this->calculateChecksum();
			if ($this->textfont instanceof BCGFont) {
				$f = clone $this->textfont;
				$f->setText($this->checksumValue);
				return $f->getWidth() + 1; // +1 for antialias
			} elseif ($this->textfont !== 0) {
				return imagefontwidth($this->textfont);
			}
		}

		return 0;
	}

	/**
	 * Sets the label offset.
	 */
	private function setLabelOffset() {
		$label = $this->getLabel();
		if (!empty($label)) {
			if ($this->textfont instanceof BCGFont) {
				$f = clone $this->textfont;
				if (strlen($this->text) === 6) {
					$f->setText('0');
				} else {
					$f->setText(substr($label, 0, 1));
				}

				$val = ($f->getWidth() + 5) / $this->scale;
				if ($val > $this->offsetX) {
					$this->offsetX = $val;
				}
			} elseif ($this->textfont !== 0) {
				$val = (imagefontwidth($this->textfont) + 2) / $this->scale;
				if ($val > $this->offsetX) {
					$this->offsetX = $val;
				}
			}
		}
	}

	/**
	 * Inverses the string when the $inverse parameter is equal to 1.
	 *
	 * @param string $text
	 * @param int $inverse
	 * @return string
	 */
	private static function inverse($text, $inverse = 1) {
		if ($inverse === 1) {
			$text = strrev($text);
		}

		return $text;
	}
}
?>