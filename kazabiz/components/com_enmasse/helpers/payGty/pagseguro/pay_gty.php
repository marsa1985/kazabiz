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
?>
<form name="paymentForm" method="post" action="https://pagseguro.uol.com.br/checkout/checkout.jhtml">
<?php 
	$cart = $this->cart;
	$count = 0;
	echo '<input type="hidden" name="ref_transacao" value="'.$this->orderId.'">';	
	foreach($cart->getAll() as $cartItem) { 
		echo '<input type="hidden" name="item_id_'.($count+1).'" value="'.$cartItem->item->id.'">';
		echo '<input type="hidden" name="item_descr_'.($count+1).'" value="'.substr($cartItem->item->name,0,70).'">';
		echo '<input type="hidden" name="item_quant_'.($count+1).'" value="'.$cartItem->getCount().'">';
		echo '<input type="hidden" name="item_valor_'.($count+1).'" value="'.$cart->getAmountToPay().'">';
		echo '<input type="hidden" name="item_frete_'.($count+1).'" value="0">';
		echo '<input type="hidden" name="item_peso_'.($count+1).'" value="0">';
		$count++;
	}
?>	
<input type="hidden" name="tipo" value="CP">
<input type="hidden" name="tipo_frete" value="EN">
<input type="hidden" name="moeda" value="BRL">
<input type="hidden" name="email_cobranca" value="<?php echo $this->attributeConfig->merchant_email; ?>">
</form>
<?php echo JText::_('PAGSEGURO_REDIRECT'); ?>
<script>
    document.paymentForm.submit();
</script>