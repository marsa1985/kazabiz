<?php
/**
 *--------------------------------------------------------------------
 *
 * Sub-Class - UPC Supplemental Barcode 2 digits
 *
 * Working with UPC-A, UPC-E, EAN-13, EAN-8
 * This includes 5 digits (normaly for suggested retail price)
 * Must be placed next to UPC or EAN Code
 * If 90000 -> No suggested Retail Price
 * If 99991 -> Book Complimentary (normally free)
 * If 90001 to 98999 -> Internal Purpose of Publisher
 * If 99990 -> Used by the National Association of College Stores to mark used books
 * If 0xxxx -> Price Expressed in British Pounds (xx.xx)
 * If 5xxxx -> Price Expressed in U.S. dollars (US$xx.xx)
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGBarcode1D.php');

class BCGupcext5 extends BCGBarcode1D {
	protected $codeParity = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->keys = array('0','1','2','3','4','5','6','7','8','9');
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

		// Parity, 0=Odd, 1=Even. Depending Checksum
		$this->codeParity = array(
			array(1, 1, 0, 0, 0),	/* 0 */
			array(1, 0, 1, 0, 0),	/* 1 */
			array(1, 0, 0, 1, 0),	/* 2 */
			array(1, 0, 0, 0, 1),	/* 3 */
			array(0, 1, 1, 0, 0),	/* 4 */
			array(0, 0, 1, 1, 0),	/* 5 */
			array(0, 0, 0, 1, 1),	/* 6 */
			array(0, 1, 0, 1, 0),	/* 7 */
			array(0, 1, 0, 0, 1),	/* 8 */
			array(0, 0, 1, 0, 1)	/* 9 */
		);
	}

	/**
	 * Parses the text.
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
	 * Sets the Y offset.
	 *
	 * @param int $offsetY
	 */
	public function setOffsetY($offsetY) {
		parent::setOffsetY($offsetY);

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
		// Checksum
		$this->calculateChecksum();

		// Starting Code
		$this->drawChar($im, '001', true);

		// Code
		for ($i = 0; $i < 5; $i++) {
			$this->drawChar($im, self::inverse($this->findCode($this->text[$i]), $this->codeParity[$this->checksumValue][$i]), false);
			if ($i < 4) {
				$this->drawChar($im, '00', false);	// Inter-char
			}
		}

		$this->drawText($im);
	}

	/**
	 * Returns the maximal size of a barcode.
	 *
	 * @return int[]
	 */
	public function getMaxSize() {
		$p = parent::getMaxSize();

		$startlength = 4 * $this->scale;
		$textlength = 5 * 7 * $this->scale;
		$intercharlength = 2 * 4 * $this->scale;

		$label = $this->getLabel();
		$textHeight = 0;
		if (!empty($label)) {
			if ($this->textfont instanceof BCGFont) {
				$textfont = clone $this->textfont;
				$textfont->setText($label);
				$textHeight = $textfont->getHeight() + BCGBarcode1D::SIZE_SPACING_FONT;
			} elseif ($this->textfont !== 0) {
				$textHeight = imagefontheight($this->textfont) + BCGBarcode1D::SIZE_SPACING_FONT;
			}
		}

		return array($p[0] + $startlength + $textlength + $intercharlength, $p[1] - $textHeight);
	}

	/**
	 * Validates the input.
	 */
	protected function validate() {
		$c = strlen($this->text);
		if($c === 0) {
			throw new BCGParseException('upcext5', 'No data has been entered.');
		}

		// Checking if all chars are allowed
		for ($i = 0; $i < $c; $i++) {
			if (array_search($this->text[$i], $this->keys) === false) {
				throw new BCGParseException('upcext5', 'The character \'' . $this->text[$i] . '\' is not allowed.');
			}
		}

		// Must contain 5 digits
		if ($c !== 5) {
			throw new BCGParseException('upcext5', 'Must contain 5 digits.');
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
		// Odd Position = 3, Even Position = 9
		// Multiply it by the number
		// Add all of that and do ?mod10
		$odd = true;
		$this->checksumValue = 0;
		$c = strlen($this->text);
		for ($i = $c; $i > 0; $i--) {
			if ($odd === true) {
				$multiplier = 3;
				$odd = false;
			} else {
				$multiplier = 9;
				$odd = true;
			}

			if (!isset($this->keys[$this->text[$i - 1]])) {
				return;
			}

			$this->checksumValue += $this->keys[$this->text[$i - 1]] * $multiplier;
		}

		$this->checksumValue = $this->checksumValue % 10;
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
		$label = $this->getLabel();

		if (!empty($label)) {
			$pA = $this->getMaxSize();
			$pB = BCGBarcode1D::getMaxSize();
			$w =  $pA[0] - $pB[0];

			if ($this->textfont instanceof BCGFont) {
				$textfont = clone $this->textfont;
				$textfont->setText($label);
				$xPosition = ($w / 2) - ($textfont->getWidth() / 2) + $this->offsetX * $this->scale;
				$yPosition = $this->offsetY * $this->scale - BCGBarcode1D::SIZE_SPACING_FONT + 1; // +1 for anti-aliasing
				$textfont->draw($im, $this->colorFg->allocate($im), $xPosition, $yPosition);
			} elseif ($this->textfont !== 0) {
				$xPosition = ($w / 2) - (strlen($label) / 2) * imagefontwidth($this->textfont) + $this->offsetX * $this->scale;
				$yPosition = $this->offsetY * $this->scale - BCGBarcode1D::SIZE_SPACING_FONT - imagefontheight($this->textfont);
				imagestring($im, $this->textfont, $xPosition, $yPosition, $label, $this->colorFg->allocate($im));
			}
		}
	}

	/**
	 * Sets the label offset.
	 */
	private function setLabelOffset() {
		$label = $this->getLabel();
		if (!empty($label)) {
			if ($this->textfont instanceof BCGFont) {
				$f = clone $this->textfont;
				$f->setText($label);

				$val = ($f->getHeight() - $f->getUnderBaseline()) / $this->scale + BCGBarcode1D::SIZE_SPACING_FONT;
				if ($val > $this->offsetY) {
					$this->offsetY = $val;
				}
			} elseif ($this->textfont !== 0) {
				$val = (imagefontheight($this->textfont) + 2) / $this->scale;
				if ($val > $this->offsetY) {
					$this->offsetY = $val;
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