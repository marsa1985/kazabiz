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

$oItems = $this->cart->getAll();
$oCartItem = array_pop($oItems);//we just support the cart with one item, so we only need to get the first item in the itemslist of the cart
$item = $oCartItem->getItem();
$item_price = $item->price;
?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div id="ShoppingCart">
	<div class="top">
		<div class="dealname">
			<div class="apollo_title" style="text-align:left;"> <?php echo JText::_('SHOP_CARD_DEAL_NAME');?></div>
			<div class="apollo_info"  style="text-align:left;">
				<?php echo $item->name;?>
			</div>
		</div>
		<div class="price">
			<div class="apollo_title"><?php echo JText::_('SHOP_CARD_PRICE');?></div>
			<div class="apollo_info"><?php echo  EnmasseHelper::displayCurrency($item->price);?></div>
		</div>
		<div class="qty">
			<div class="apollo_title"><?php echo JText::_('SHOP_CARD_QTY');?></div>
			<div class="apollo_info">
				<form action="index.php" id="changeItem" method="post" name="changeItem"
						class="form-validate" onSubmit="return myValidate(this);">
					<input type="hidden" name="itemId" value="<?php echo $item->id; ?>" />
					<input type="hidden" name="option" value="com_enmasse" />
					<input type="hidden" name="controller" value="shopping" />
					<input type="hidden" name="task" value="changeItem" />
					<input type="hidden" name="buy4friend" value="<?php echo JRequest::getVar('buy4friend', 0, 'method', 'int')?>" />
					<input type="input" size="1px" id="value" name="value" value="<?php echo $oCartItem->getCount();?>" class="required validate-numeric" />
				</form>
			</div>
		</div>
		<div class="total">
			<div class="apollo_title"><?php echo JText::_('SHOP_CARD_TOTAL');?></div>
			<div class="apollo_info">
				<?php echo  EnmasseHelper::displayCurrency($this->cart->getAmountToPay());?>
			</div>
		</div>
		<div class="updateqtiny">
			<div class="apollo_title"><?php echo JText::_('SHOP_CARD_UPDATE_QTY');?></div>
			<div class="apollo_info">
				<input type="button" class="button" value="<?php echo JText::_('UPDATE_BUTTON');?>" onclick="javascript:document.changeItem.submit();"></input>
			</div>
		</div>
<!-- Begin Point Sytem Integration -->  
	<?php if(EnmasseHelper::isPointSystemEnabled() && $item->pay_by_point && $item->prepay_percent == 100.0):?>
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
					<input type="input" size="1px" id="value" name="value" value="<?php echo $this->cart->getPoint();?>" class="required validate-numeric" />
					<input type="button" class="button" value="<?php echo JText::_('UPDATE_BUTTON');?>" onclick="javascript:document.changePoint.submit();"></input>
				</form>
			</div>
		</div>
	<?php endif;?>
<!-- End Point Sytem Integration -->         
	</div>
	<div class="bottom">
		<div class="checkout-infor-right">
			<div class="text"><?php echo JText::_('SHOP_CARD_TOTAL_ITEM');?>: <?php echo $this->cart->getTotalItem();?></div>
			<div class="text"><?php echo JText::_('SHOP_CARD_TOTAL_PRICE');?>: <?php echo EnmasseHelper::displayCurrency($this->cart->getAmountToPay());?></div>
		</div>
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
</div>
	