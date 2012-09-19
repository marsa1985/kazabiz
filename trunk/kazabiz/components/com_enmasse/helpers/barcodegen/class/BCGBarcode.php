<?php
/**
 *--------------------------------------------------------------------
 *
 * Base class for Barcode 1D and 2D
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGColor.php');
include_once('BCGArgumentException.php');
include_once('BCGParseException.php');
include_once('BCGDrawException.php');

abstract class BCGBarcode {
	const COLOR_BG = 0;
	const COLOR_FG = 1;

	protected $colorFg, $colorBg;		// Color Foreground, Barckground
	protected $scale;					// Scale of the graphic, default: 1
	protected $offsetX, $offsetY;		// Position where to start the drawing

	/**
	 * Constructor.
	 */
	protected function __construct() {
		$this->setOffsetX(0);
		$this->setOffsetY(0);
		$this->setForegroundColor(0x000000);
		$this->setBackgroundColor(0xffffff);
		$this->setScale(1);
	}

	/**
	 * Parses the text before displaying it.
	 *
	 * @param mixed $text
	 */
	public abstract function parse($text);

	/**
	 * Gets the foreground color of the barcode.
	 *
	 * @return BCGColor
	 */
	public function getForegroundColor() {
		return $this->colorFg;
	}

	/**
	 * Sets the foreground color of the barcode. It could be a BCGColor
	 * value or simply a language code (white, black, yellow...) or hex value.
	 *
	 * @param mixed $code
	 */
	public function setForegroundColor($code) {
		if ($code instanceof BCGColor) {
			$this->colorFg = $code;
		} else {
			$this->colorFg = new BCGColor($code);
		}
	}

	/**
	 * Gets the background color of the barcode.
	 *
	 * @return BCGColor
	 */
	public function getBackgroundColor() {
		return $this->colorBg;
	}
	
	/**
	 * Sets the background color of the barcode. It could be a BCGColor
	 * value or simply a language code (white, black, yellow...) or hex value.
	 *
	 * @param mixed $code
	 */
	public function setBackgroundColor($code) {
		if ($code instanceof BCGColor) {
			$this->colorBg = $code;
		} else {
			$this->colorBg = new BCGColor($code);
		}
	}

	/**
	 * Sets the color.
	 *
	 * @param mixed $fg
	 * @param mixed $bg
	 */
	public function setColor($fg, $bg) {
		$this->setForegroundColor($fg);
		$this->setBackgroundColor($bg);
	}

	/**
	 * Gets the scale of the barcode.
	 *
	 * @return int
	 */
	public function getScale() {
		return $this->scale;
	}

	/**
	 * Sets the scale of the barcode in pixel.
	 * If the scale is lower than 1, an exception is raised.
	 *
	 * @param int $scale
	 */
	public function setScale($scale) {
		$scale = intval($scale);
		if ($scale <= 0) {
			throw new ArgumentException('The scale must be larger than 0.', 'scale');
		}

		$this->scale = $scale;
	}

	/**
	 * Abstract method that draws the barcode on the resource.
	 *
	 * @param resource $im
	 */
	public abstract function draw($im);

	/**
	 * Returns the maximal size of a barcode.
	 * [0]->width
	 * [1]->height
	 *
	 * @return int[]
	 */
	public function getMaxSize() {
		return array($this->offsetX * $this->scale, $this->offsetY * $this->scale);
	}

	/**
	 * Gets the X offset.
	 *
	 * @return int
	 */
	public function getOffsetX() {
		return $this->offsetX;
	}

	/**
	 * Sets the X offset.
	 *
	 * @param int $offsetX
	 */
	public function setOffsetX($offsetX) {
		$offsetX = intval($offsetX);
		if ($offsetX < 0) {
			throw new ArgumentException('The offset X must be 0 or larger.', 'offsetX');
		}

		$this->offsetX = $offsetX;
	}

	/**
	 * Gets the Y offset.
	 *
	 * @return int
	 */
	public function getOffsetY() {
		return $this->offsetY;
	}

	/**
	 * Sets the Y offset.
	 *
	 * @param int $offsetY
	 */
	public function setOffsetY($offsetY) {
		$offsetY = intval($offsetY);
		if ($offsetY < 0) {
			throw new ArgumentException('The offset Y must be 0 or larger.', 'offsetY');
		}

		$this->offsetY = $offsetY;
	}

	/**
	 * Draws 1 pixel on the resource at a specific position with a determined color.
	 *
	 * @param resource $im
	 * @param int $x
	 * @param int $y
	 * @param int $color
	 */
	protected function drawPixel($im, $x, $y, $color = self::COLOR_FG) {
		$xR = ($x + $this->offsetX) * $this->scale;
		$yR = ($y + $this->offsetY) * $this->scale;

		// We always draw a rectangle
		imagefilledrectangle($im,
			$xR,
			$yR,
			$xR + $this->scale - 1,
			$yR + $this->scale - 1,
			$this->getColor($im, $color));
	}

	/**
	 * Draws an empty rectangle on the resource at a specific position with a determined color.
	 *
	 * @param resource $im
	 * @param int $x1
	 * @param int $y1
	 * @param int $x2
	 * @param int $y2
	 * @param int $color
	 */
	protected function drawRectangle($im, $x1, $y1, $x2, $y2, $color = self::COLOR_FG) {
		if ($this->scale === 1) {
			imagerectangle($im,
				($x1 + $this->offsetX) * $this->scale,
				($y1 + $this->offsetY) * $this->scale,
				($x2 + $this->offsetX) * $this->scale,
				($y2 + $this->offsetY) * $this->scale,
				$this->getColor($im, $color));
		} else {
			imagefilledrectangle($im, ($x1 + $this->offsetX) * $this->scale, ($y1 + $this->offsetY) * $this->scale, ($x2 + $this->offsetX) * $this->scale + $this->scale - 1, ($y1 + $this->offsetY) * $this->scale + $this->scale - 1, $this->getColor($im, $color));
			imagefilledrectangle($im, ($x1 + $this->offsetX) * $this->scale, ($y1 + $this->offsetY) * $this->scale, ($x1 + $this->offsetX) * $this->scale + $this->scale - 1, ($y2 + $this->offsetY) * $this->scale + $this->scale - 1, $this->getColor($im, $color));
			imagefilledrectangle($im, ($x2 + $this->offsetX) * $this->scale, ($y1 + $this->offsetY) * $this->scale, ($x2 + $this->offsetX) * $this->scale + $this->scale - 1, ($y2 + $this->offsetY) * $this->scale + $this->scale - 1, $this->getColor($im, $color));
			imagefilledrectangle($im, ($x1 + $this->offsetX) * $this->scale, ($y2 + $this->offsetY) * $this->scale, ($x2 + $this->offsetX) * $this->scale + $this->scale - 1, ($y2 + $this->offsetY) * $this->scale + $this->scale - 1, $this->getColor($im, $color));
		}
	}

	/**
	 * Draws a filled rectangle on the resource at a specific position with a determined color.
	 *
	 * @param resource $im
	 * @param int $x1
	 * @param int $y1
	 * @param int $x2
	 * @param int $y2
	 * @param int $color
	 */
	protected function drawFilledRectangle($im, $x1, $y1, $x2, $y2, $color = self::COLOR_FG) {
		if ($x1 > $x2) { // Swap
			$x1 ^= $x2 ^= $x1 ^= $x2;
		}

		if ($y1 > $y2) { // Swap
			$y1 ^= $y2 ^= $y1 ^= $y2;
		}

		imagefilledrectangle($im,
			($x1 + $this->offsetX) * $this->scale,
			($y1 + $this->offsetY) * $this->scale,
			($x2 + $this->offsetX) * $this->scale + $this->scale - 1,
			($y2 + $this->offsetY) * $this->scale + $this->scale - 1,
			$this->getColor($im, $color));
	}

	/**
	 * Allocates the color based on the integer.
	 *
	 * @param resource $im
	 * @param int $color
	 * @return resource
	 */
	protected function getColor($im, $color) {
		if ($color === self::COLOR_BG) {
			return $this->colorBg->allocate($im);
		} else {
			return $this->colorFg->allocate($im);
		}
	}
}
?>