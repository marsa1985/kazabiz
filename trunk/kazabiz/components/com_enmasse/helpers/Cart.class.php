<?php
/*------------------------------------------------------------------------
# En Masse - Social Buying Extension 2010
# ------------------------------------------------------------------------
# By Matamko.com
# Copyright (C) 2010 Matamko.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.matamko.com
# Technical Support:  Visit our forum at www.matamko.com
-------------------------------------------------------------------------*/

require_once (dirname(__FILE__)."/CartItem.class.php");

class Cart
{
	protected $maxQty;
	protected $itemList = array();

	protected $totalItem = 0;
	protected $totalPrice = 0;
	protected $point = 0;
	protected $referralId = 0;

	public function __construct($maxQty = -1)
	{
		$this->maxQty = $maxQty;
	}

	protected function setItemList($itemList)
	{
		$this->itemList = $itemList;
	}
	
	public function setReferralId($id)
	{
		$this->referralId = $id;
	}

	public function getReferralId()
	{
		return $this->referralId;
	}		

	public function addItem($newItem)
	{
		$signature = $newItem->id;

		if(isset($this->itemList[$signature]))
		{
			$cartItem = $this->itemList[$signature];
			$cartItem->addItem($newItem);
			$this->itemList[$signature] = $cartItem;
		}
		else
		{
			$this->itemList[$signature] = new CartItem($newItem);
		}

		$this->recalculateCart();
	}

	public function changeItem($signature, $value)
	{
		if(isset($this->itemList[$signature]))
		{
			$cartItem = $this->itemList[$signature];
			$cartItem->changeItem($value);
			if ($cartItem->getCount() == 0)
				unset($this->itemList[$signature]);
			else
				$this->itemList[$signature] = $cartItem;
		}
		$this->recalculateCart();
	}
	
	public function changePoint($value)
	{
		$this->point = $value;		
	}	

	public function deleteItem($signature)
	{
		unset($this->itemList[$signature]);
		$this->recalculateCart();
	}

	public function deleteAll()
	{
		$this->setItemList(array());
		$this->recalculateCart();
	}

	public function getTotalItem()
	{
		return $this->totalItem;
	}

	public function getTotalPrice()
	{
		return $this->totalPrice;
	}
	
	public function getAmountToPay()
	{
		return $this->totalPrice - $this->point;
	}	
	public function getPoint()
	{
		return $this->point;
	}	

	public function hasExceedCartMaxQty($addOnQty)
	{
		// if it is -1, it is unlimited
		if($this->maxQty == -1)
		{
			return false;
		}
		return ($this->getTotalQty() + $addOnQty > $this->maxQty);
	}

	public function recalculateCart()
	{
		$this->totalItem = 0;
		$this->totalPrice = 0;
		$this->point = 0;
		foreach($this->itemList as $signature => $cartItem)
		{
			$this->totalItem += $cartItem->getCount();
			$this->totalPrice += ($cartItem->getCount() * ($cartItem->getItem()->price));
		}
	}

	public function getItem($signature)
	{
		return $this->itemList[$signature];
	}

	public function hasCartItem($signature)
	{
		if(isset($this->itemList[$signature]))
			return true;
		else
			return false;
	}

	public function getAll()
	{
		return $this->itemList;
	}

	public function getNbItemList()
	{
		return count($this->itemList);
	}
	
}