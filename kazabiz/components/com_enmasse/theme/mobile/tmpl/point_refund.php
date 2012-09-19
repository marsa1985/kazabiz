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
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."EnmasseHelper.class.php");

JFactory::getDocument()->addScript("components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js");
JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict()');

$isPointSystemEnabled = EnmasseHelper::isPointSystemEnabled();
if($isPointSystemEnabled==true)
{
	$orderId = $this->orderId;
	$pointPaid = $this->pointPaid;
	$buyerId = $this->buyerId;
	$totalPrice = $this->totalPrice;
	$dealName = $this->dealName;
	$maxPoint = (int)($totalPrice);
	$refundedAmount = EnmasseHelper::getRefundedAmountByOrderId($orderId);
	if($refundedAmount==0)
	{
	?>
		<script language="javascript" type="text/javascript">
			function validateForm()
			{
				var form = document.refundForm;
				if (form.point.value == "" || isNaN(form.point.value) || form.point.value <= 0 || form.point.value > <?php echo $maxPoint; ?>)
				{
					alert("Invalid point");
					return false;
				}		
				return true;
			}		
		</script>
		<h1><?php echo JText::_('REFUND_FOR') . " ";?> "<?php echo $dealName; ?>":</h1>
		<form action="index.php" name="refundForm" method="post" onsubmit="return validateForm()">
		<p><?php echo JText::_('YOU_PAID') . " \"" . $dealName . "\" " . JText::_('WITH') . " " . EnmasseHelper::displayCurrency($totalPrice-$pointPaid) . " " . JText::_('AND') . " " . $pointPaid . " " . JText::_('POINTS') . "."?>"
		</p>
		<?php 
			if($maxPoint==$pointPaid)
			{
		?>
				<p><?php  echo JText::_('REFUND_ALL_POINT_MESSAGE'); ?><br/>
				<input type="hidden" name="point" value="<?php echo $pointPaid; ?>"/><input type="submit" name="submit" value="<?php echo JText::_('REFUND'); ?>"/></p>			
		<?php
			}
			else
			{
		?>
			<p><?php  echo JText::_('ENTER_POINT_REFUND_MESSAGE') . " " . $maxPoint; ?><br/>
			<input type="text" name="point" value="<?php echo $pointPaid; ?>"/><input type="submit" name="submit" value="<?php echo JText::_('REFUND'); ?>"/></p>		
		<?php 
			}
		?>
		<p>
		<input type="hidden" name="orderid" value="<?php echo $orderId; ?>" />
		<input type="hidden" name="buyerid" value="<?php echo $buyerId; ?>" />
		<input type="hidden" name="option" value="com_enmasse" />
		<input type="hidden" name="controller" value="point" />
		<input type="hidden" name="task" value="doRefund" />	
		
		</p>
		</form>
	<?php 
	}
	else
	{
		echo JText::_('ALREADY_REQUEST') . " " . $refundedAmount . JText::_('POINT_FOR_DEAL') . " " . $dealName . "\"";
	}	
}
else
{
	echo JText::_('NO_POINT_SYSTEM');
}
