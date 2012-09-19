<?php
/**
 *--------------------------------------------------------------------
 *
 * Sub-Class - EAN-13
 *
 * EAN-13 contains
 *	- 2 system digits (1 not displayed but coded with parity)
 *	- 5 manufacturer code digits
 *	- 5 product digits
 *	- 1 checksum digit
 *
 * The checksum is always displayed.
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGBarcode1D.php');

class BCGean13 extends BCGBarcode1D {
	protected $codeParity = array();
	protected $ean13offsetX = 0;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->keys = array('0','1','2','3','4','5','6','7','8','9');

		// Left-Hand Odd Parity starting with a space
		// Left-Hand Even Parity is the inverse (0=0012) starting with a space
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

		// Parity, 0=Odd, 1=Even for manufacturer code. Depending on 1st System Digit
		$this->codeParity = array(
			array(0, 0, 0, 0, 0),	/* 0 */
			array(0, 1, 0, 1, 1),	/* 1 */
			array(0, 1, 1, 0, 1),	/* 2 */
			array(0, 1, 1, 1, 0),	/* 3 */
			array(1, 0, 0, 1, 1),	/* 4 */
			array(1, 1, 0, 0, 1),	/* 5 */
			array(1, 1, 1, 0, 0),	/* 6 */
			array(1, 0, 1, 0, 1),	/* 7 */
			array(1, 0, 1, 1, 0),	/* 8 */
			array(1, 1, 0, 1, 0)	/* 9 */
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
		$previousOffsetX = $this->offsetX;
		$this->offsetX = $this->ean13offsetX + $this->offsetX;

		$this->drawBars($im);
		$this->drawText($im);

		$this->offsetX = $previousOffsetX;
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
		$textlength = 12 * 7 * $this->scale;
		$endlength = 3 * $this->scale;

		return array($p[0] + $startlength + $centerlength + $textlength + $endlength, $p[1]);
	}

	/**
	 * Validates the input.
	 */
	protected function validate() {
		$c = strlen($this->text);
		if($c === 0) {
			throw new BCGParseException('ean13', 'No data has been entered.');
		}
	
		$this->checkCharsAllowed();
		$this->checkCorrectLength();

		parent::validate();
	}

	/**
	 * Check chars allowed.
	 */
	protected function checkCharsAllowed() {
		// Checking if all chars are allowed
		$c = strlen($this->text);
		for ($i = 0; $i < $c; $i++) {
			if (array_search($this->text[$i], $this->keys) === false) {
				throw new BCGParseException('ean13', 'The character \'' . $this->text[$i] . '\' is not allowed.');
			}
		}
	}

	/**
	 * Check correct length.
	 */
	protected function checkCorrectLength() {
		// If we have 13 chars, just flush the last one without throwing anything
		$c = strlen($this->text);
		if ($c === 13) {
			$this->text = substr($this->text, 0, 12);
		} elseif ($c !== 12) {
			throw new BCGParseException('ean13', 'Must contain 12 digits, the 13th digit is automatically added.');
		}
	}

	/**
	 * Sets the label offset.
	 */
	protected function setLabelOffset() {
		$this->ean13offsetX = 0;
		$label = $this->getLabel();
		if (!empty($label)) {
			if ($this->textfont instanceof BCGFont) {
				$f = clone $this->textfont;
				$f->setText(substr($label, 0, 1));
				$val = ($f->getWidth() + 5) / $this->scale;
				if ($val > $this->offsetX) {
					$this->ean13offsetX = $val;
				}
			} elseif ($this->textfont !== 0) {
				$val = (imagefontwidth($this->textfont) + 2) / $this->scale;
				if ($val > $this->offsetX) {
					$this->ean13offsetX = $val;
				}
			}
		}
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
	 * Draws the bars
	 *
	 * @param resource $im
	 */
	protected function drawBars($im) {
		// Checksum
		$this->calculateChecksum();
		$temp_text = $this->text . $this->keys[$this->checksumValue];

		// Starting Code
		$this->drawChar($im, '000', true);

		// Draw Second Code
		$this->drawChar($im, $this->findCode($temp_text[1]), false);

		// Draw Manufacturer Code
		for ($i = 0; $i < 5; $i++) {
			$this->drawChar($im, self::inverse($this->findCode($temp_text[$i + 2]), $this->codeParity[(int)$temp_text[0]][$i]), false);
		}

		// Draw Center Guard Bar
		$this->drawChar($im, '00000', false);

		// Draw Product Code
		for ($i = 7; $i < 13; $i++) {
			$this->drawChar($im, $this->findCode($temp_text[$i]), true);
		}

		// Draw Right Guard Bar
		$this->drawChar($im, '000', true);
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

				// We need to separate the text, one on the left and one on the right and one starting
				$text0 = substr($temp_text, 0, 1);
				$text1 = substr($temp_text, 1, 6);
				$text2 = substr($temp_text, 7, 6);
				$font0 = clone $this->textfont;
				$font1 = clone $this->textfont;
				$font2 = clone $this->textfont;
				$font0->setText($text0);
				$font1->setText($text1);
				$font2->setText($text2);

				$xPosition0 = $this->offsetX * $this->scale - $font0->getWidth() - 4; // -4 is just for beauty
				$yPosition0 = $this->thickness * $this->scale + $font0->getHeight() / 2 + $this->offsetY * $this->scale;

				$xPosition1 = ($this->scale * 44 - $font1->getWidth()) / 2 + $code1 * $this->scale + $this->offsetX * $this->scale;
				$xPosition2 = ($this->scale * 44 - $font1->getWidth()) / 2 + $code2 * $this->scale + $this->offsetX * $this->scale;
				$yPosition = $this->thickness * $this->scale + $this->textfont->getHeight() + self::SIZE_SPACING_FONT + $this->offsetY * $this->scale;

				$text_color = $this->colorFg->allocate($im);
				$font0->draw($im, $text_color, $xPosition0, $yPosition0);
				$font1->draw($im, $text_color, $xPosition1, $yPosition);
				$font2->draw($im, $text_color, $xPosition2, $yPosition);
			} elseif ($this->textfont !== 0) {
				$code1 = 0;
				$code2 = 0;

				$this->drawExtendedBars($im, 9, $code1, $code2);

				$startPosition = 10;

				$xPosition0 = $this->offsetX * $this->scale - imagefontwidth($this->textfont);
				$yPosition0 = $this->thickness * $this->scale - imagefontheight($this->textfont) / 2 + $this->offsetY * $this->scale;

				$xPosition1 = ($this->scale * 44 - imagefontwidth($this->textfont) * 6) / 2 + $code1 * $this->scale + $this->offsetX * $this->scale;
				$xPosition2 = ($this->scale * 44 - imagefontwidth($this->textfont) * 6) / 2 + $code2 * $this->scale + $this->offsetX * $this->scale;
				$yPosition = $this->thickness * $this->scale + 1 * $this->scale + $this->offsetY * $this->scale;

				$text_color = $this->colorFg->allocate($im);
				imagechar($im, $this->textfont, $xPosition0, $yPosition0, $temp_text[0], $text_color);
				imagestring($im, $this->textfont, $xPosition1, $yPosition, substr($temp_text, 1, 6), $text_color);
				imagestring($im, $this->textfont, $xPosition2, $yPosition, substr($temp_text, 7, 6), $text_color);
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
	private function drawExtendedBars($im, $plus, &$code1, &$code2) {
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
		$this->positionX += 44;
		$this->DrawSingleBar($im, BCGBarcode::COLOR_FG);
		$this->positionX += 2;
		$this->DrawSingleBar($im, BCGBarcode::COLOR_FG);

		// Last Bars
		$code2 = $this->positionX;
		$this->positionX += 44;
		$this->DrawSingleBar($im, BCGBarcode::COLOR_FG);
		$this->positionX += 2;
		$this->DrawSingleBar($im, BCGBarcode::COLOR_FG);

		$this->positionX = $rememberX;
		$this->thickness = $rememberH;
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