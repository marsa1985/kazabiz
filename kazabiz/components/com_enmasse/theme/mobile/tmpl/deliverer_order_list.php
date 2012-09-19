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

$app = JFactory::getApplication();
$app->setUserState('staticTitle', JText::_('Delivery'));
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div class="row row_space">
	  		<?php foreach($this->orderList as $orderRow):?>
					<?php $link = JRoute::_('index.php?option=com_enmasse&controller=deliverer&task=edit&id=' .$orderRow->id)?>
					<strong><?php echo JText::_('ORDER_ID');?></strong> : <a href="<?php echo $link?>"><?php echo $orderRow->display_id?></a><br />
					<strong><?php echo JText::_('ORDER_QTY');?></strong> : <?php echo $orderRow->orderItem->qty?><br />
					<strong><?php echo JText::_('ORDER_TOTAL');?></strong> : <?php echo EnmasseHelper::displayCurrency($orderRow->total_buyer_paid); ?><br />
					<strong><?php echo JText::_('ORDER_PAID_AMOUNT');?></strong> : <?php echo EnmasseHelper::displayCurrency($orderRow->paid_amount); ?> <br />
					<strong><?php echo JText::_('ORDER_REMAIN_AMOUNT');?></strong> : <?php echo EnmasseHelper::displayCurrency($orderRow->total_buyer_paid - $orderRow->paid_amount);?><br />
					<strong><?php echo JText::_('ORDER_DATE');?></strong> : <?php echo JHTML::_('date', $orderRow->created_at, JText::_('DATE_FORMAT_LC1'));?><br />
					<strong><?php echo JText::_('ORDER_DELIVERY');?></strong> : <?php echo EnmasseHelper::displayJson($orderRow->delivery_detail);?>
					<strong><?php echo JText::_('ORDER_STATUS');?></strong> : <?php echo JTEXT::_('ORDER_'.strtoupper($orderRow->status));?><br />
					<strong><?php echo JText::_('ORDER_COMMENT');?></strong> : 
							<?php $token=EnmasseHelper::generateOrderItemToken($orderRow->orderItem->id, $orderRow->orderItem->created_at);?>
							<a href='index.php?option=com_enmasse&controller=coupon&task=listing&orderItemId=<?php echo $orderRow->orderItem->id ?>&token=<?php echo $token?>'>
								<?php echo JText::_('ORDER_LIST_COUPON');?>
							</a><br /><hr /><br />
				<?php endforeach;?>

</div>
