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
require_once(JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");
 	
$theme =  EnmasseHelper::getThemeFromSetting();//getThemeFromSetting();
JFactory::getDocument()->addScript("components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js");
JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict()');

$cart = $this->cart;
?>
<div class="deal">
	<div class="main_deal">
		<?php include dirname(__FILE__)."/cart_manage.php";?>
		<div>
			<?php if ( count($cart->getAll()) > 0 ):?>
				<form action="index.php" id="emptyCart" name="emptyCart"><input type="hidden" name="option"
					value="com_enmasse" /> <input type="hidden" name="task"
					value="emptyCart" /> <input type="hidden" name="controller"
					value="shopping" /> </form>
					
				<form action="index.php" name="checkout" id="checkout">
				<input type="hidden" name="option"
					value="com_enmasse" /> <input type="hidden" name="controller"
					value="shopping" /> <input type="hidden" name="task"
					value="checkout" /> 
				</form>
				 <div class="clear"></div>
				 <br />
				<button class="button_big" onclick="javascript:document.emptyCart.submit();"><?php echo JText::_('EMPTY_CART_BUTTON');?></button>
			    <button class="button" onclick="javascript:document.checkout.submit();"><?php echo JText::_('CHECK_OUT_BUTTON');?></button>
			<?php endif;?>
		</div>
	</div>
</div>