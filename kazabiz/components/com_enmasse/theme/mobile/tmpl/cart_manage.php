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

JFactory::getDocument()->addScript("components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js");
JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict()');

$oItems = $this->cart->getAll();
$oCartItem = array_pop($oItems);//we just support the cart with one item, so we only need to get the first item in the itemslist of the cart
$item = $oCartItem->getItem();
$item_price = $item->price;
?>
<form action="index.php" id="changeItem" method="post" name="changeItem" class="form-validate" onSubmit="return myValidate(this);">
<div class="left_checkout">
		<?php echo $item->name;?>
		<input type="hidden" name="itemId" value="<?php echo $item->id; ?>" />
		<input type="hidden" name="option" value="com_enmasse" />
		<input type="hidden" name="controller" value="shopping" />
		<input type="hidden" name="task" value="changeItem" />
		<?php echo $buy4friend;//shop_checkout.php ?>
		<p>
		<span class="smalltext"><?php echo JText::_('SHOP_CARD_QTY');?></span>
		<input type="input" size="1px" id="value" name="value" value="<?php echo $oCartItem->getCount();?>" class="required validate-numeric text_small" />
		X <span class="orange"><?php echo  EnmasseHelper::displayCurrency($item->price);?></span>
		</p>
		
		<?php if($item->prepay_percent < 100 ):?>
			<div class="checkout-infor-left">
				<div>
					<h4><?php echo JText::_('SHOP_CARD_PAYMENT_SCHEDULE');?></h4>
					<label class="checkout-label"><?php echo JText::_('SHOP_CARD_PARTIAL_PAYMENT');?> :</label><span class="currency"><?php echo EnmasseHelper::displayCurrency($this->cart->getTotalItem() * $item_price * $item->prepay_percent / 100)?> </span> <br />
					<label class="checkout-label"><?php echo JText::_('SHOP_CARD_FINAL_PAYMENT');?> :</label><span class="currency"><?php echo EnmasseHelper::displayCurrency($this->cart->getTotalItem() * $item_price * (100 - $item->prepay_percent) / 100)?> </span> <br />
					<label class="checkout-label"><?php echo JText::_('SHOP_CARD_TOTAL');?> :</label><span class="currency"><?php echo EnmasseHelper::displayCurrency($this->cart->getTotalItem() * $item_price )?> </span> <br />
				</div>
				
			</div>
		<?php endif;?>
		
</div>

<div class="right_checkout">
<div align="center"><?php echo JText::_('SHOP_CARD_TOTAL');?><span  class="orange"> <?php echo EnmasseHelper::displayCurrency($this->cart->getAmountToPay());?></span></div>
	<input type="button" class="button" value="<?php echo JText::_('UPDATE_BUTTON');?>" onclick="javascript:document.changeItem.submit();"></input>
</div>
<div class="clear"></div>
</form>

<!-- Begin Point Sytem Integration -->  
<?php if(EnmasseHelper::isPointSystemEnabled() && $item->pay_by_point && $item->prepay_percent == 100.0):?>
		<div style="width:95%;float:none;margin-top:10px"></div>
		<div class="pay_by_point">
			<div class="apollo_title"><?php echo JText::_('PAY_WITH_POINT');?></div>
			<div class="apollo_info">
				<form action="index.php" id="changePoint" method="post" name="changePoint" class="form-validate" onSubmit="return myValidate(this);">
			        <?php
					$integrationClass = EnmasseHelper::getPointSystemClassFromSetting();
					$integrateFileName = $integrationClass.'.class.php';
					require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."pointsystem". DS .$integrationClass. DS. $integrateFileName);
					$integrationObject = new $integrationClass();		
			        $user = JFactory::getUser(); 
			        $userid = $user->id ; 
			        $point = $integrationObject->getPoint($userid);
			        ?>
			        <label><?php echo JText::_('YOU_HAVE'); ?> <?php echo number_format($point, 2); ?> <?php echo JText::_('POINTS'); ?></label><br />               
					<label><?php echo JText::_('ENTER_POINT_MESSAGE');?>:</label>
					<input type="hidden" name="option" value="com_enmasse" />
					<input type="hidden" name="controller" value="shopping" />
					<input type="hidden" name="task" value="changePoint" />
					<input type="hidden" name="buy4friend" value="<?php echo JRequest::getVar('buy4friend', 0, 'method', 'int')?>" />
					<input type="input" size="7" maxlength="7" id="value" name="value" value="<?php echo $this->cart->getPoint();?>" class="required validate-numeric" />
					<input type="button" class="button" value="<?php echo JText::_('UPDATE_BUTTON');?>" onclick="javascript:document.changePoint.submit();"></input>
				</form>
			</div>
		</div>
	<?php endif;?>
<!-- End Point Sytem Integration -->    