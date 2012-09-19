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

require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");

$theme =  EnmasseHelper::getThemeFromSetting();

//--------- add stylesheet and javascript
JFactory::getDocument()->addStyleSheet('components/com_enmasse/theme/' . $theme . '/css/screen.css');
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div id="ShoppingCart">
  <div class="top">
    <div class="dealname" >
      <div class="apollo_title" style="text-align:left;"> <?php echo JText::_('SHOP_CARD_DEAL_NAME');?></div>
      <div class="apollo_info" style="text-align:left;"><?php 
	$cart = $this -> cart;
	$count = 0; foreach($cart->getAll() as $cartItem): 
	 $item = $cartItem->getItem();
	 echo $item->name;
	 endforeach;?></div>
    </div>
    <div class="price">
      <div class="apollo_title"> <?php echo JText::_('SHOP_CARD_PRICE');?> </div>
      <div class="apollo_info"><?php 
	$cart = $this -> cart;
	$count = 0; foreach($cart->getAll() as $cartItem): 
	 $item = $cartItem->getItem();
	 $item_price = $item->price;
	 echo  EnmasseHelper::displayCurrency($item->price);
	 endforeach;?></div>
    </div>
    <div class="qty">
      <div class="apollo_title"> <?php echo JText::_('SHOP_CARD_QTY');?> </div>
      <div class="apollo_info"><?php 
	$cart = $this -> cart;
	$count = 0; foreach($cart->getAll() as $cartItem): 
	 $item = $cartItem->getItem();
	 echo   $cartItem->getCount();
	 endforeach;?></div>
    </div>
    <div class="total">
      <div class="apollo_title"> <?php echo JText::_('SHOP_CARD_TOTAL');?> </div>
      <div class="apollo_info">
        <?php 
	$cart = $this -> cart;
	$count = 0; foreach($cart->getAll() as $cartItem): 
	 $item = $cartItem->getItem();
	 echo EnmasseHelper::displayCurrency(($item->price)*$cartItem->getCount());
	 endforeach;?>
	 
      </div>
    </div>
  </div>
  <div class="bottom">
    <div class="text"><?php echo JText::_('SHOP_CARD_TOTAL_ITEM');?>:  <?php echo $cart->getTotalItem();?></div>
    <div class="text"><?php echo JText::_('SHOP_CARD_TOTAL_PRICE');?>: <?php echo EnmasseHelper::displayCurrency($cart->getTotalPrice());?></div>
  </div>
</div>
