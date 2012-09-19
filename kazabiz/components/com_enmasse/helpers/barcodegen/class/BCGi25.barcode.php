<?php
/**
 *--------------------------------------------------------------------
 *
 * Sub-Class - Interleaved 2 of 5
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGBarcode1D.php');

class BCGi25 extends BCGBarcode1D {
	private $checksum;
	private $ratio;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->keys = array('0','1','2','3','4','5','6','7','8','9');
		$this->code = array(
			'00110',	/* 0 */
			'10001',	/* 1 */
			'01001',	/* 2 */
			'11000',	/* 3 */
			'00101',	/* 4 */
			'10100',	/* 5 */
			'01100',	/* 6 */
			'00011',	/* 7 */
			'10010',	/* 8 */
			'01010'		/* 9 */
		);

		$this->setChecksum(false);
		$this->setRatio(1);
	}

	/**
	 * Sets the checksum.
	 *
	 * @param bool $checksum
	 */
	public function setChecksum($checksum) {
		$this->checksum = (bool)$checksum;
	}
	
	/**
	 * Sets the ratio of the black bar compared to the white bars.
	 *
	 * @param int $ratio
	 */
	public function setRatio($ratio) {
		$this->ratio = $ratio;
	}

	/**
	 * Draws the barcode.
	 *
	 * @param resource $im
	 */
	public function draw($im) {
		$temp_text = $this->text;

		// Checksum
		if ($this->checksum === true) {
			$this->calculateChecksum();
			$temp_text .= $this->keys[$this->checksumValue];
		}

		// Starting Code
		$this->drawChar($im, '0000', true);

		// Chars
		$c = strlen($temp_text);
		for ($i = 0; $i < $c; $i += 2) {
			$temp_bar = '';
			$c2 = strlen($this->findCode($temp_text[$i]));
			for ($j = 0; $j < $c2; $j++) {
				$temp_bar .= substr($this->findCode($temp_text[$i]), $j, 1);
				$temp_bar .= substr($this->findCode($temp_text[$i + 1]), $j, 1);
			}

			$this->drawChar($im, $temp_bar, true);
		}

		// Ending Code
		$this->drawChar($im, '100', true);
		$this->drawText($im);
	}

	/**
	 * Returns the maximal size of a barcode.
	 *
	 * @return int[]
	 */
	public function getMaxSize() {
		$p = parent::getMaxSize();

		$textlength = 7 * strlen($this->text) * $this->scale;
		$startlength = 4 * $this->scale;
		$checksumlength = 0;
		if ($this->checksum === true) {
			$checksumlength = 7 * $this->scale;
		}

		$endlength = 4 * $this->scale;

		return array($p[0] + $startlength + $textlength + $checksumlength + $endlength, $p[1]);
	}


	/**
	 * Validates the input.
	 */
	protected function validate() {
		$c = strlen($this->text);
		if($c === 0) {
			throw new BCGParseException('i25', 'No data has been entered.');
		}
		
		// Checking if all chars are allowed
		for ($i = 0; $i < $c; $i++) {
			if (array_search($this->text[$i], $this->keys) === false) {
				throw new BCGParseException('i25', 'The character \'' . $this->text[$i] . '\' is not allowed.');
			}
		}

		// Must be even
		if ($c % 2 !== 0 && $this->checksum === false) {
			throw new BCGParseException('i25', 'i25 must contain an even amount of digits if checksum is false.');
		} elseif ($c % 2 === 0 && $this->checksum === true) {
			throw new BCGParseException('i25', 'i25 must contain an odd amount of digits if checksum is true.');
		}

		parent::validate();
	}

	/**
	 * Overloaded method to calculate checksum.
	 */
	protected function calculateChecksum() {
		// Calculating Checksum
		// Consider the right-most digit of the message to be in an "even" position,
		// and assign odd/even to each character moving from right to left
		// Even Position = 3, Odd Position = 1
		// Multiply it by the number
		// Add all of that and do 10-(?mod10)
		$even = true;
		$this->checksumValue = 0;
		$c = strlen($this->text);
		for ($i = $c; $i > 0; $i--) {
			if ($even === true) {
				$multiplier = 3;
				$even = false;
			} else {
				$multiplier = 1;
				$even = true;
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
}
?>