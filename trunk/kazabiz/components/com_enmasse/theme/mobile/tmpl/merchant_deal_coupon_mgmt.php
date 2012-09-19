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

$theme =  EnmasseHelper::getThemeFromSetting();//getThemeFromSetting();
JFactory::getDocument()->addScript("components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js");
JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict()');

$orderItemList 	= $this->orderItemList;

$emptyJOpt = JHTML::_('select.option', '', JText::_('MERCHANT_LOGIN_DEAL_OP') );

$dealJOptList = array();
array_push($dealJOptList, $emptyJOpt);
foreach ($this->dealList as $item)
{
	$var = JHTML::_('select.option', $item->id, JText::_($item->name) );
	array_push($dealJOptList, $var);
}
?>
<div class="row row_list">
		<h3><?php echo JText::_('MERCHANT_LOGIN_MSG1') ?></h3>
		<form action="index.php">
			<b><?php echo JText::_('MERCHANT_LOGIN_COUPON_SERIAL') ?>:</b>
			<input type="text" class="text" width="30" name="coupon" id="coupon" />
			<br/><br/>
			<button type="submit" name="submit" class="button_big" onClick="this.form.newStatus.value='Used'"><?php echo JText::_('MERCHANT_LOGIN_USED_BUTTON') ?></button>
			<button type="submit" name="submit" class="button_big" onClick="this.form.newStatus.value='Taken'"><?php echo JText::_('MERCHANT_LOGIN_TAKEN_BUTTON') ?></button>
			
			<input type="hidden" name="newStatus" value="Used" />
			
			<input type="hidden" name="option" id="option" value="com_enmasse"/>
			<input type="hidden" name="controller" id="controller" value="merchant"/>
			<input type="hidden" name="task" id="task" value="update"/>
		</form>
</div>

<div class="row row_list">
		<h3><?php echo JText::_('MERCHANT_LOGIN_MSG2') ?></h3>
		<form action="index.php">
				<b><?php echo JText::_('MERCHANT_LOGIN_DEAL') ?>:</b>
				<?php echo JHTML::_('select.genericList', $dealJOptList, 'filter[deal_id]', null , 'value', 'text', $this->filter['deal_id']);?>
				<button type="submit" class="button" ><?php echo JTEXT::_('MERCHANT_LOGIN_GO_BUTTON');?></button>
				
				<input type="hidden" name="option" value="com_enmasse" />
				<input type="hidden" name="controller" id="controller" value="merchant"/>
		</form>
		<br/>
		<table class="adminlist" width=100%>
			<thead>
				<tr>
					<th width="30%"><?php echo JTEXT::_('MERCHANT_LOGIN_BUYER');?></th>
					<th width="25%"><?php echo JTEXT::_('MERCHANT_LOGIN_PURCHASE_DATE');?></th>
					<th width="25%"><?php echo JTEXT::_('MERCHANT_LOGIN_COUPON_SERIAL');?></th>
					<th width="20%"><?php echo JTEXT::_('STATUS');?></th>
				</tr>
			</thead>
			<?php
			for($i=0; $i < count($orderItemList); $i++)
			{
				$orderItem = $orderItemList[$i];
				$buyerDetail = json_decode($orderItem->order->buyer_detail);
		
				for($j=0; $j < count($orderItem->invtyList); $j++)
				{
					$invty = $orderItem->invtyList[$j];
					?>
			<tr>
				<td><?php echo $buyerDetail->name; ?></td>
				<td align="center"><?php echo DatetimeWrapper::getDisplayDatetime($orderItem->created_at); ?></td>
				<td align="center"><?php echo $invty->name; ?></td>
				<td align="center">
					<?php
						if($invty->status=="Used")
						{ 
							echo "<b>";echo JTEXT::_('COUPON_'.$invty->status); echo "</b>";
						}
						else
							echo JTEXT::_('COUPON_'.$invty->status);
					?>
				</td>
			</tr>
			<?php
				}
			}
			?>
		</table>
</div>


