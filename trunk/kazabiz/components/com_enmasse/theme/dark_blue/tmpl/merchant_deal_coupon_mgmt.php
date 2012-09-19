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

$theme =  EnmasseHelper::getThemeFromSetting();
JFactory::getDocument()->addStyleSheet('components/com_enmasse/theme/' . $theme . '/css/screen.css');

$orderItemList 	= $this->orderItemList;

$emptyJOpt = JHTML::_('select.option', '', JText::_('MERCHANT_LOGIN_DEAL_OP') );

$dealJOptList = array();
array_push($dealJOptList, $emptyJOpt);
foreach ($this->dealList as $item)
{
	$var = JHTML::_('select.option', $item->id, JText::_($item->name) );
	array_push($dealJOptList, $var);
}
JHtml::_('behavior.framework');
?>
<div class="maincol_full_cont">
	<div>
		<h3><?php echo JText::_('MERCHANT_LOGIN_MSG1') ?></h3>
		<form action="index.php">
			<b><?php echo JText::_('MERCHANT_LOGIN_COUPON_SERIAL') ?>:</b>
			<input type="text" width="30" name="coupon" id="coupon" />
			<input type="submit" name="submit" class="button" value="<?php echo JText::_('MERCHANT_LOGIN_USED_BUTTON') ?>" onClick="this.form.newStatus.value='Used'"/>
			<input type="submit" name="submit" class="button" value="<?php echo JText::_('MERCHANT_LOGIN_TAKEN_BUTTON') ?>" onClick="this.form.newStatus.value='Taken'"/>
			
			<input type="hidden" name="newStatus" value="Used" />
			
			<input type="hidden" name="option" id="option" value="com_enmasse"/>
			<input type="hidden" name="controller" id="controller" value="merchant"/>
			<input type="hidden" name="task" id="task" value="update"/>
		</form>
	</div>
	<br/>
	<hr/>
	<div>
		<h3><?php echo JText::_('MERCHANT_LOGIN_MSG2') ?></h3>
		<form action="index.php" name="adminForm">
			<b><?php echo JText::_('MERCHANT_LOGIN_DEAL') ?>:</b>
			<input type="hidden" name="option" value="com_enmasse" />
			<input type="hidden" name="controller" id="controller" value="merchant"/>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHTML::_('select.genericList', $dealJOptList, 'filter[deal_id]', null , 'value', 'text', $this->filter['deal_id']);?>
			<input type="submit" class="button"  value="<?php echo JTEXT::_('MERCHANT_LOGIN_GO_BUTTON');?>" />
			<input type="button" class="button"  value="<?php echo JTEXT::_('MERCHANT_PAY_OUT_BUTTON');?>" 
				onclick="javascript:{if(document.adminForm.elements['filter[deal_id]'].value == '') {alert('No Deal Selected!'); return}else if(confirm('<?php echo JText::_('MERCHANT_PAY_OUT_CONFIRM_MSG')?>')){document.adminForm.task.value = 'payOut';document.adminForm.submit()} }">
				
		
			<br/>
			<table class="adminlist" width=100% >
				<?php
				$count = 0;
				for($i=0; $i < count($orderItemList); $i++)
				{
					$orderItem = $orderItemList[$i];
					$buyerDetail = json_decode($orderItem->order->buyer_detail);
			
					for($j=0; $j < count($orderItem->invtyList); $j++)
					{
						$invty = $orderItem->invtyList[$j];
						$count++;
				?>
				<tr>
					<td> <?php echo JHTML::_('grid.id', $count - 1, $invty->id );?></td>
					<td><?php echo $buyerDetail->name; ?></td>
					<td><?php echo $orderItem->order->description; ?></td>
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
					<td align="center">
						<?php 
							if($invty->status != EnmasseHelper::$INVTY_STATUS_LIST['Used'] && $invty->settlement_status == EnmasseHelper::$MERCHANT_SETTLEMENT_STATUS_LIST['Not_Paid_Out'])
							{
								echo JText::_("MERCHANT_COUPON_CAN_NOT_PAID_OUT");
							}elseif ($invty->status == EnmasseHelper::$INVTY_STATUS_LIST['Used'] && $invty->settlement_status == EnmasseHelper::$MERCHANT_SETTLEMENT_STATUS_LIST['Not_Paid_Out'])
							{
								echo JText::_("MERCHANT_COUPON_CAN_BE_PAID_OUT");
							}
							elseif ($invty->settlement_status == EnmasseHelper::$MERCHANT_SETTLEMENT_STATUS_LIST['Should_Be_Paid_Out'])
							{
								echo JText::_("MERCHANT_COUPON_WATTING_FOR_PAID_OUT");
							}elseif ($invty->settlement_status == EnmasseHelper::$MERCHANT_SETTLEMENT_STATUS_LIST['Paid_Out'])
							{
								echo JText::_("MERCHANT_COUPON_PAID_OUT");
							}
						?>
					</td>
				</tr>
				<?php
					}
				}
				?>
				<thead>
					<tr>
						<th width="5%"><input type="checkbox" name="toggle" value=""
								onclick="checkAll(<?php echo $count ; ?>);" /></th>
						<th width="15%"><?php echo JTEXT::_('MERCHANT_LOGIN_BUYER');?></th>
						<th width="30%"><?php echo JTEXT::_('MERCHANT_LOGIN_COMMENT');?></th>
						<th width="15%"><?php echo JTEXT::_('MERCHANT_LOGIN_PURCHASE_DATE');?></th>
						<th width="10%"><?php echo JTEXT::_('MERCHANT_LOGIN_COUPON_SERIAL');?></th>
						<th width="10%"><?php echo JTEXT::_('STATUS');?></th>
						<th width="15%"><?php echo JTEXT::_('MERCHANT_COUPON_SETTLEMENT_STATUS');?></th>
					</tr>
				</thead>
			</table>
			<input type="button"" class="button" value="<?php echo JText::_('MERCHANT_PAY_OUT_SELECTED_COUPONS_BUTTON')?>"
				onclick="javascript:{if(document.adminForm.boxchecked.value == 0) {alert('<?php echo JText::_('MERCHANT_NO_COUPON_SELECTED_MSG')?>'); return}else if(confirm('<?php echo JText::_('MERCHANT_PAY_OUT_COUPONS_CONFIRM_MSG')?>')){document.adminForm.task.value = 'payOutCoupons';document.adminForm.method='post';document.adminForm.submit()} }" />
			
			
		</form>
	</div>
</div>


