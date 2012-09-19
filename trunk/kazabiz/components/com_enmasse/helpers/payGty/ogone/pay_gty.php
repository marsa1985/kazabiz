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

$notifyUrl = JURI::root() . 'index.php?option=com_enmasse&controller=payment&task=notifyUrl&payClass=ogone';
$nAmount = $this->cart->getAmountToPay() * 100;
$user = JFactory::getUser();

$sPassPhrase = $this->attributeConfig->in_passphrase;
$aParams = array();
$aParams['ACCEPTURL'] = $notifyUrl;
$aParams['AMOUNT'] = $nAmount;
$aParams['CANCELURL'] = $notifyUrl;
$aParams['CURRENCY'] = $this->attributeConfig->currency_code;
$aParams['DECLINEURL'] = $notifyUrl;
$aParams['EXCEPTIONURL'] = $notifyUrl;
$aParams['LANGUAGE'] = $this->attributeConfig->language;
$aParams['ORDERID'] = $this->orderId;
$aParams['PSPID'] = $this->attributeConfig->pspid;
//prod
?>
<form method="post" name="paymentForm" action="https://secure.ogone.com/ncol/test/orderstandard.asp">
<?php
$sHash = '';
foreach($aParams as $key=>$value)
{
	echo '<input type="hidden" name="'.$key.'" value="'.$value.'">';
	//$sHash .= $key . '=' . $value . $sPassPhrase;    
}
$sHash .= 'AMOUNT='.$nAmount.$sPassPhrase.'CURRENCY='.$this->attributeConfig->currency_code.$sPassPhrase.'
LANGUAGE='.$this->attributeConfig->language.$sPassPhrase.'ORDERID='.$this->orderId.$sPassPhrase.'
PSPID='.$this->attributeConfig->pspid.$sPassPhrase;

//echo '<pre>'; print_r($aParams); echo '</pre>'; echo $sHash; die;
?>
<input type="hidden" name="SHASign" value="<?php echo sha1($sHash); ?>" />
<input type="submit" value="" id="submit2" name="submit2" />
</form>
<?php echo JText::_('OGONE_REDIRECT'); ?>
</div>
<script>
    //document.paymentForm.submit();
</script>
<?php 
	//$this->cart->deleteAll();
	//JFactory::getSession()->set('cart', serialize(@$cart));
?>