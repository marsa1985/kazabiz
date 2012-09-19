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
$app->setUserState('staticTitle', JText::_('Orders'));
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div class="row row_space">
				<?php $count = 0;?>
				<?php foreach($this->orderList as $orderRow):?>
						<strong> <?php echo JText::_('ORDER_DATE');?></strong>: <?php echo JHTML::_('date', $orderRow->created_at, JText::_('DATE_FORMAT_LC1'));?><br />
						<strong> <?php echo JText::_('ORDER_ID');?></strong>: <?php echo $orderRow->display_id; ?><br />
						<strong> <?php echo JText::_('ORDER_DEAL');?></strong>: <?php echo $orderRow->orderItem->description; ?><br />
						<strong> <?php echo JText::_('ORDER_QTY');?></strong>: <?php echo $orderRow->orderItem->qty; ?><br />
						<strong> <?php echo JText::_('ORDER_TOTAL');?></strong>: <?php echo EnmasseHelper::displayCurrency($orderRow->total_buyer_paid);?><br />
						<strong> <?php echo JText::_('ORDER_DELIVERY');?></strong>: <?php $deliveryObj = json_decode($orderRow->delivery_detail);
				    	echo $deliveryObj->name ." (".$deliveryObj->email.")";?><br />
						<strong> <?php echo JText::_('ORDER_STATUS');?></strong>: <?php echo JTEXT::_('ORDER_'.strtoupper($orderRow->status));?><br />
						
						<div>
							
							 <?php 
						    	if($orderRow->orderItem->status=="Delivered")
						    	{
						    		echo JText::_('ORDER_COMMENT');
						    		$token=EnmasseHelper::generateOrderItemToken($orderRow->orderItem->id, $orderRow->orderItem->created_at);
						    ?>
								<a href='index.php?option=com_enmasse&controller=coupon&task=listing&orderItemId=<?php echo $orderRow->orderItem->id ?>&token=<?php echo $token?>'>
									<?php echo JText::_('ORDER_LIST_COUPON');?>
								</a>
							<?php 
						    	}
						    	elseif($orderRow->orderItem->status=="Refunded")
						    	{
						    		echo JText::_('ORDER_COMMENT');
									$pointPaid = EnmasseHelper::getPointPaidByOrderId($orderRow->orderItem->order_id);
									$refundedAmount = EnmasseHelper::getRefundedAmountByOrderId($orderRow->orderItem->order_id);
									if($pointPaid>0)
									{
										if($refundedAmount!=0)
										{
											echo JText::_('REFUNDED');
										}
										else
										{
										$buyerId = EnmasseHelper::getUserIdByOrderId($orderRow->orderItem->order_id);
							?>
								<a href='<?php echo JURI::base();?>index.php?option=com_enmasse&controller=point&task=refundForm&orderid=<?php echo $orderRow->orderItem->id; ?>&buyerid=<?php echo $buyerId; ?>'>
									<?php echo JText::_('REFUND_POINT');?>
								</a>				
										
							<?php
										}
									}	    		
						    	}
							?>
						<hr />
						<br />
					</div>
				<?php $count++?>
				<?php endforeach;?>
</div>