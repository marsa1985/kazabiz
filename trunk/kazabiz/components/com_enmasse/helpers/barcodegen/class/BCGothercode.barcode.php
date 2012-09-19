<?php
/**
 *--------------------------------------------------------------------
 *
 * Sub-Class - othercode
 *
 * Other Codes
 * Starting with a bar and altern to space, bar, ...
 * 0 is the smallest
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGBarcode1D.php');

class BCGothercode extends BCGBarcode1D {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Draws the barcode.
	 *
	 * @param resource $im
	 */
	public function draw($im) {
		$this->drawChar($im, $this->text, true);
		$this->drawText($im);
	}

	/**
	 * Gets the label.
	 * If the label was set to BCGBarcode1D::AUTO_LABEL, the label will display the value from the text parsed.
	 *
	 * @return string
	 */
	public function getLabel() {
		$label = $this->label;
		if($this->label === BCGBarcode1D::AUTO_LABEL) {
			$label = '';
		}

		return $label;
	}

	/**
	 * Returns the maximal size of a barcode.
	 *
	 * @return int[]
	 */
	public function getMaxSize() {
		$p = parent::getMaxSize();

		$array = str_split($this->text, 1);
		$textlength = (array_sum($array) + count($array)) * $this->scale;

		return array($p[0] + $textlength, $p[1]);
	}

	/**
	 * Validates the input.
	 */
	protected function validate() {
		$c = strlen($this->text);
		if($c === 0) {
			throw new BCGParseException('othercode', 'No data has been entered.');
		}

		parent::validate();
	}

	/**
	 * Overloaded method for drawing special label.
	 *
	 * @param resource $im
	 */
	protected function drawText($im) {
		if($this->label !== BCGBarcode1D::AUTO_LABEL && $this->label !== '') {
			$pA = $this->getMaxSize();
			$pB = BCGBarcode1D::getMaxSize();
			$w =  $pA[0] - $pB[0];

			if($this->textfont instanceof BCGFont) {
				$textfont = clone $this->textfont;
				$textfont->setText($this->label);

				$xPosition = ($w / 2) - $textfont->getWidth() / 2 + $this->offsetX * $this->scale;
				$yPosition = $this->thickness * $this->scale + $textfont->getHeight() - $textfont->getUnderBaseline() + BCGBarcode1D::SIZE_SPACING_FONT + $this->offsetY * $this->scale;

				$text_color = $this->colorFg->allocate($im);
				$textfont->draw($im, $text_color, $xPosition, $yPosition);
			} elseif($this->textfont !== 0) {
				$xPosition = ($w / 2) - (strlen($this->label) * imagefontwidth($this->textfont)) / 2 + $this->offsetX * $this->scale;
				$yPosition = $this->thickness * $this->scale + $this->offsetY * $this->scale + BCGBarcode1D::SIZE_SPACING_FONT;

				$text_color = $this->colorFg->allocate($im);
				imagestring($im, $this->textfont, $xPosition, $yPosition, $this->label, $text_color);
			}
		}
	}
}
?>