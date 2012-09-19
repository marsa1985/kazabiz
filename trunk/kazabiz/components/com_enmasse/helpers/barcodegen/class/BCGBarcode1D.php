<?php
/**
 *--------------------------------------------------------------------
 *
 * Holds all type of barcodes for 1D generation
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGBarcode.php');
include_once('BCGFont.php');

abstract class BCGBarcode1D extends BCGBarcode {
	const SIZE_SPACING_FONT = 5;

	const AUTO_LABEL = '##!!AUTO_LABEL!!##';

	protected $thickness;
	protected $keys, $code;
	protected $positionX;
	protected $textfont;
	protected $text, $label;
	protected $checksumValue;
	protected $displayChecksum;

	/**
	 * Constructor.
	 */
	protected function __construct() {
		parent::__construct();

		$this->setThickness(30);
		$this->text = '';
		$this->checksumValue = false;
		$this->setLabel(self::AUTO_LABEL);
		$this->setFont(5);
	}

	/**
	 * Sets the thickness.
	 *
	 * @param int $thickness
	 */
	public function setThickness($thickness) {
		$this->thickness = intval($thickness);
		if ($this->thickness <= 0) {
			throw new ArgumentException('The thickness must be larger than 0.', 'thickness');
		}
	}

	/**
	 * Gets the thickness.
	 *
	 * @return int
	 */
	public function getThickness() {
		return $this->thickness;
	}

	/**
	 * Parses the text before displaying it.
	 *
	 * @param mixed $text
	 */
	public function parse($text) {
		$this->text = $text;
		$this->checksumValue = false;		// Reset checksumValue
		$this->validate();
	}

	/**
	 * Sets the label.
	 * You can use BCGBarcode1D::AUTO_LABEL to have the label automatically written based on the parsed text.
	 *
	 * @param string $label
	 */
	public function setLabel($label) {
		$this->label = $label;
	}

	/**
	 * Gets the label.
	 * If the label was set to BCGBarcode1D::AUTO_LABEL, the label will display the value from the text parsed.
	 *
	 * @return string
	 */
	public function getLabel() {
		$label = $this->label;
		if ($this->label === self::AUTO_LABEL) {
			$label = $this->text;
			if ($this->displayChecksum === true && ($checksum = $this->processChecksum()) !== false) {
				$label .= $checksum;
			}
		}

		return $label;
	}

	/**
	 * Sets the font.
	 *
	 * @param mixed $font BCGFont or int
	 */
	public function setFont($font) {
		if ($font instanceof BCGFont) {
			$this->textfont = clone $font;
			$this->textfont->setText($this->text);
		} else {
			$this->textfont = min(5, max(0, intval($font)));
		}
	}
	
	/**
	 * Gets the font.
	 *
	 * @return BCGFont or int
	 */
	public function getFont() {
		return $this->textfont;
	}

	/**
	 * Gets the maximal size for the barcode. Returns an array, [0]=width, [1]=height .
	 *
	 * @return int[]
	 */
	public function getMaxSize() {
		$p = parent::getMaxSize();

		$label = $this->getLabel();
		$textHeight = 0;
		if (!empty($label)) {
			if ($this->textfont instanceof BCGFont) {
				$textfont = clone $this->textfont;
				$textfont->setText($label);
				$textHeight = $textfont->getHeight() + self::SIZE_SPACING_FONT + 1; // +1 for anti-alias
			} elseif ($this->textfont !== 0) {
				$textHeight = imagefontheight($this->textfont) + self::SIZE_SPACING_FONT;
			}
		}

		return array($p[0], $p[1] + $this->thickness * $this->scale + $textHeight);
	}

	/**
	 * Gets the checksum of a Barcode.
	 * If no checksum is available, return FALSE.
	 *
	 * @return string
	 */
	public function getChecksum() {
		return $this->processChecksum();
	}

	/**
	 * Sets if the checksum is displayed with the label or not.
	 * The checksum must be activated in some case to make this variable effective.
	 *
	 * @param boolean $displayChecksum
	 */
	public function setDisplayChecksum($displayChecksum) {
		$this->displayChecksum = (bool)$displayChecksum;
	}

	/**
	 * Validates the input
	 */
	protected function validate() {
		// No validation in the abstract class.
	}

	/**
	 * Returns the index in $keys (useful for checksum).
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	protected function findIndex($var) {
		return array_search($var, $this->keys);
	}

	/**
	 * Returns the code of the char (useful for drawing bars).
	 *
	 * @param mixed $var
	 * @return string
	 */
	protected function findCode($var) {
		return $this->code[$this->findIndex($var)];
	}

	/**
	 * Draws all chars thanks to $code. if $start is true, the line begins by a space.
	 * if $start is false, the line begins by a bar.
	 *
	 * @param resource $im
	 * @param string $code
	 * @param boolean $startBar
	 */
	protected function drawChar($im, $code, $startBar = true) {
		$colors = array(self::COLOR_FG, self::COLOR_BG);
		$currentColor = $startBar ? 0 : 1;
		$c = strlen($code);
		for ($i = 0; $i < $c; $i++) {
			for ($j = 0; $j < intval($code[$i]) + 1; $j++) {
				$this->drawSingleBar($im, $colors[$currentColor]);
				$this->nextX();
			}

			$currentColor = ($currentColor + 1) % 2;
		}
	}

	/**
	 * Draws a Bar of $color depending of the resolution.
	 *
	 * @param resource $img
	 * @param int $color
	 */
	protected function drawSingleBar($im, $color) {
		$this->drawFilledRectangle($im, $this->positionX, 0, $this->positionX, $this->thickness - 1, $color);
	}

	/**
	 * Moving the pointer right to write a bar.
	 */
	protected function nextX() {
		$this->positionX++;
	}

	/**
	 * Draws the label under the barcode.
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
				$yPosition = $this->thickness * $this->scale + $textfont->getHeight() - $textfont->getUnderBaseline() + self::SIZE_SPACING_FONT + $this->offsetY * $this->scale;
				$textfont->draw($im, $this->colorFg->allocate($im), $xPosition, $yPosition);
			} elseif ($this->textfont !== 0) {
				$xPosition = ($w / 2) - (strlen($label) / 2) * imagefontwidth($this->textfont) + $this->offsetX * $this->scale;
				$yPosition = $this->thickness * $this->scale + self::SIZE_SPACING_FONT + $this->offsetY * $this->scale;
				imagestring($im, $this->textfont, $xPosition, $yPosition, $label, $this->colorFg->allocate($im));
			}
		}
	}

	/**
	 * Method that saves FALSE into the checksumValue. This means no checksum
	 * but this method should be overriden when needed.
	 */
	protected function calculateChecksum() {
		$this->checksumValue = false;
	}

	/**
	 * Returns FALSE because there is no checksum. This method should be
	 * overriden to return correctly the checksum in string with checksumValue.
	 *
	 * @return string
	 */
	protected function processChecksum() {
		return false;
	}
}
?>