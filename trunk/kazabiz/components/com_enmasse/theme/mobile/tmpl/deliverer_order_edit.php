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

$dlvStatus = array();
$item = array('value' => 'undelivered', 'text' => 'ORDER_UNDELIVERED');
array_push($dlvStatus, $item);

$item = array('value' => 'delivered', 'text' => 'ORDER_DELIVERED');
array_push($dlvStatus, $item);

$app = JFactory::getApplication();
$app->setUserState('staticTitle', JText::_('Delivery'));
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div class="row row_space">
	<h4><?php echo JText::_("ORDER_EDITING") . "&nbsp;" .EnmasseHelper::displayOrderDisplayId($this->oOrder->id)?></h4>
</div>
<div class="row row_space">
	<div class="delivery_detail_edit">
		<form action="index.php" method="post" name="adminForm">
			<table>
				<tr>
					<td valign="top"><?php echo JText::_('ORDER_ID');?></td>
					<td>
						<?php echo EnmasseHelper::displayOrderDisplayId($this->oOrder->id);?>
						<input type="hidden" value="<?php echo $this->oOrder->id?>" name="id"/>
					</td>
				</tr>
				<tr>
					<td valign="top"><?php echo JText::_('ORDER_DEAL_NAME');?></td>
					<td><?php echo $this->oOrderItemList[0]->description?></td>
				</tr>
				<tr>
					<td valign="top"><?php echo JText::_('ORDER_QTY');?></td>
					<td><?php echo $this->oOrderItemList[0]->qty?></td>
				</tr>
				<tr>
					<td valign="top"><?php echo JText::_('ORDER_TOTAL');?></td>
					<td><?php echo $this->oOrder->total_buyer_paid?></td>
				</tr>
				<tr>
					<td valign="top"><?php echo JText::_('ORDER_PAID_AMOUNT');?></td>
					<td><?php echo EnmasseHelper::displayCurrency($this->oOrder->paid_amount)?></td>
				</tr>
				<tr>
					<td valign="top"><?php echo JText::_('ORDER_REMAIN_AMOUNT');?></td>
					<td><?php echo EnmasseHelper::displayCurrency($this->oOrder->total_buyer_paid - $this->oOrder->paid_amount)?></td>
				</tr>
				<tr>
					<td valign="top"><?php echo JText::_('ORDER_DELIVERY');?></td>
					<td><?php echo EnmasseHelper::displayJson($this->oOrder->delivery_detail)?></td>
				</tr>
				<tr>
					<td valign="top"><?php echo JText::_('ORDER_DELIVERY_STATUS');?></td>
					<td><?php echo JHtml::_('select.genericList', $dlvStatus, 'delivery_status', null, 'value', 'text', $this->oOrder->delivery_status, false, true)?></td>
				</tr>
				<tr>
					<td valign="top"><?php echo JText::_('ORDER_COMMENT');?></td>
					<td>
						<textarea style="width:100%" rows="3" cols="20" name="description"><?php echo $this->oOrder->description?></textarea>
					</td>
				</tr>
			</table>
			<input class="button" type="submit" value="<?php echo JText::_("UPDATE");?>" />
			<input type="hidden" name="option" value="com_enmasse" />
			<input type="hidden" name="controller" value="deliverer" />
			<input type="hidden" name="task" value="updateOrder" />
		</form>
	</div>
</div>
