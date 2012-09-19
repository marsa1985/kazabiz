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
?>
<div class="maincol_full_cont">
	<h4><?php echo JTEXT::_('CASH_ORDER_SUCCESS');?></h4>
	<br />
	<br />
	<div id="ShoppingCart" style="width:100%">
			<div class="top">
			<div class="apollo_title" style="text-align:left;">
				<?php 
				echo JTEXT::_('ORDER_ID').' : '.$this->orderId.'<br>';?>
			</div>
			<div class="apollo_title" style="text-align:left;">
				<?php
					echo JTEXT::_('DEAL_NAME').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;';
					$cart = $this->cart;
						 $count = 0;
						 foreach($cart->getAll() as $cartItem): 
							 $item = $cartItem->getItem();
							 echo $item->name;
						 endforeach;
					
					?>
				</div>
			</div>
			<div class="bottom">
				<div class="text">
				<?php 
					
					echo JTEXT::_('SHOP_CARD_TOTAL_ITEM').' &nbsp;:&nbsp;&nbsp;'.$this->cart->getTotalItem();
					
				?>
				</div>
				<div class="text">
				<?php 
					echo JTEXT::_('SHOP_CARD_TOTAL_PRICE').' :&nbsp;&nbsp;'.EnmasseHelper::displayCurrency($cartItem->getCount() * $item->price * $item->prepay_percent/100 - $this->cart->getPoint());
					
				?>
				</div>
			</div>
	</div>
	<div class="paygty-desc-table">
		<table id="instruction_table">
		  <thead>
		     <tr id="instruction_table_header"> 
		     	<td colspan="2" align="center"><?php echo JTEXT::_('CASH_PAY_INFO'); ?> </td>
		     </tr>
		  </thead>
		  	<tr>		  	 	
		  	    <td><?php echo $this->attributeConfig->instruction;?></td>
		  	</tr>
		</table>
	</div>
</div>
<?php 
 $this->cart->deleteAll();
 JFactory::getSession()->set('cart', null);
?>