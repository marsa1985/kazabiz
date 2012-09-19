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
 	
$theme =  EnmasseHelper::getThemeFromSetting();
JFactory::getDocument()->addStyleSheet('components/com_enmasse/theme/' . $theme . '/css/screen.css');
$oItem = array_pop($this->cart->getAll());
JHTML::_('behavior.formvalidation');
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div class="deal">
	<div class="main_deal">
		<?php include "cart_manage.php";?>
		<div class="h13"></div>
		<form action='index.php' id="orderDetail" name="orderDetail"  class="form-validate" method="post" onSubmit="return myValidate(this);">
			<div class="infor_person_wrapper">
				<div class="infor_person_header">
					<b><?php echo JText::_('SHOP_CARD_CHECK_OUT_MESSAGE_LINE1');?></b>
					<br />
					<i>
						<?php echo JText::_('SHOP_CARD_CHECK_OUT_MESSAGE_LINE2');?>
					</i> 
				</div>
				<table class="infor_person">
					<tr>
						<th align="left"><?php echo JText::_('SHOP_CARD_CHECK_OUT_PERSON_NAME');?></th>
						<td><input type="text" name="name" id="name" value="<?php echo $this->arData['name']?>" class="required" /></td>
					</tr>
					<tr>
						<th align="left"><?php echo JText::_('SHOP_CARD_CHECK_OUT_PERSON_EMAIL');?></th>
						<td>
							<input type="text" name="email" id="email" 
								value="<?php echo $this->arData['email']?>"
								class="required validate-email" />
						</td>
					</tr>
				</table>
			</div>
			<?php if(JRequest::getVar('buy4friend')):?>
			<div class="infor_person_wrapper">
				<div class="infor_person_header">
					<b><?php echo JText::_('SHOP_CARD_CHECK_OUT_FOR_FRIEND_MESSAGE_LINE1');?></b>
					<br />
					<i><?php echo JText::_('SHOP_CARD_CHECK_OUT_FOR_FRIEND_MESSAGE_LINE2');?></i>
				</div>
				<table class="infor_person">
					<tr>
						<th align="left"><?php echo JText::_('SHOP_CARD_CHECK_OUT_RECEIVER_NAME');?></th>
						<td>
							<input type="text" name="receiver_name" id="receiver_name" value="<?php echo $this->arData['receiver_name']?>" class="required" />
						</td>
					</tr>
					<tr>
						<th align="left"><?php echo JText::_('SHOP_CARD_CHECK_OUT_RECEIVER_EMAIL');?></th>
						<td>
							<input type="text" name="receiver_email" id="receiver_email" 
								value="<?php echo $this->arData['receiver_email']?>"
								class="required validate-email" />
						</td>
					</tr>
					<tr>
						<th align="left"><?php echo JText::_('SHOP_CARD_CHECK_OUT_RECEIVER_MESSAGE');?></th>
						<td>
							<textarea name="receiver_msg" id="receiver_msg" ><?php echo $this->arData['receiver_msg']?></textarea>
						</td>
					</tr>
				</table>
				<input type="hidden" name="buy4friend" value="1"/>
			</div>
			<?php endif;?>
			<?php if($oItem->item->prepay_percent < 100):?>
			<div class="infor_person_wrapper">
				<div class="infor_person_header">
					<b><?php echo JText::_('SHOP_CARD_CHECK_OUT_DERECTLY_DELIVERY_MESSAGE_LINE1');?></b>
					<br />
					<i><?php echo JText::_('SHOP_CARD_CHECK_OUT_DERECTLY_DELIVERY_MESSAGE_LINE2');?></i>
				</div>
				<table class="infor_person">
					<tr>
						<th align="left"><?php echo JText::_('SHOP_CARD_CHECK_OUT_RECEIVER_ADDRESS');?></th>
						<td>
							<input type="text" name="receiver_address" id="receiver_address" value="<?php echo $this->arData['receiver_address']?>" class="required" />
						</td>
					</tr>
					<tr>
						<th align="left"><?php echo JText::_('SHOP_CARD_CHECK_OUT_RECEIVER_PHONE');?></th>
						<td>
							<input type="text" name="receiver_phone" id="receiver_phone" 
								value="<?php echo $this->arData['receiver_phone']?>"
								class="required validate-numeric" />
						</td>
					</tr>
					
				</table>
			</div>
			<?php endif;?>
		<br/><br/>
		<?php if($item_price !=0) 
		{?>
		<div id="Order_Information">
			<div class="top">
		    	<div class="line"><span> <?php echo JText::_('SHOP_CARD_CHECK_OUT_CHOOSE_PAYMENT');?> </span></div>
		        <div class="line">
			        <select name="payGtyId" id="payGtyId" class="required">
						<option value=""><?php echo JText::_('SHOP_CARD_CHECK_OUT_CHOOSE_PAYMENT_OPTION');?></option>
						<?php 
							$cart = $this->cart;
							$item_price = $cart->getTotalPrice();					
							if($item_price == $cart->getPoint())
							{
								foreach($this->payGtyList as $row):
									if($row->class_name == "point")
									{
										echo "<option value=\"".$row->id."\" SELECTED>".$row->name."</option>";
									}
								endforeach;
							}
							else
							{
								$numberOfPayment = count($this->payGtyList); 						
								foreach($this->payGtyList as $row):
									if($row->class_name != "point")
									{
										if($numberOfPayment==1)
										{
											echo "<option value=\"".$row->id."\" SELECTED>".$row->name."</option>";
										}
										else
										{
											echo "<option value=\"".$row->id."\">".$row->name."</option>";
										}
									}
								endforeach;
							}
							?>
					</select>
		        </div>
		        <div class="line">
			        <?php if (isset($this->termArticleId) && $this->termArticleId!=0):?>
				        <input type="checkbox" name="terms" id="terms" class="required" <?php if(isset($this->arData['terms'])) echo "checked=\"checked\""?>>
				    	<label for="terms">
				    		<?php echo JText::_('SHOP_CARD_CHECK_OUT_TERM_CONDITION');?>
				        	<a style="float:none" href="<?php echo JURI::base();?>components/com_enmasse/theme/<?php echo $this->theme; ?>/tmpl/term.php?artId=<?php echo $this->termArticleId ?>"
								onclick="window.open('<?php echo JURI::base();?>components/com_enmasse/theme/<?php echo $this->theme; ?>/tmpl/term.php?artId=<?php echo $this->termArticleId ?>','name','height=600,width=400,toolbar=no,directories=no,status=no,menubar=no,scrollbars=1,resizable=no'); return false;">
				        		<?php echo JText::_('SHOP_CARD_CHECK_OUT_TERM_CONDITION_LINK');?>
				    		</a>
				    	</label>
			        	
			        <?php else:?>
			        	<input type="hidden" name="terms" id="terms" class="required" value="checked">
			        <?php endif;?> 
		        	<input type="hidden" name="check" value="post" /> 
		        	<input type="hidden" name="option" value="com_enmasse" /> 
		        	<input type="hidden" name="controller" value="shopping" /> 
		        	<input type="hidden" name="task" value="submitCheckOut" />
		        </div>
		    </div>
		    <div class="bottom">
				<input type="button" class="button" value="<?php echo JText::_('PROCESS_CHECK_OUT_BUTTON');?>" onclick="document.orderDetail.submit();"></input>
		    </div>
		</div>
		<?php }
		 else
		 {
		?>
		   <input type="hidden" name="check" value="post" /> 
		   <input type="hidden" name="option" value="com_enmasse" /> 
		   <input type="hidden" name="controller" value="shopping" /> 
		   <input type="hidden" name="task" value="submitCheckOut" />
		   <div class="bottom">
				<input type="button" class="button" value="<?php echo JText::_('PROCESS_CHECK_OUT_BUTTON');?>" onclick="document.orderDetail.submit();"></input>
		    </div>
		<?php }?>
		</form>
	</div>
	<div class="deal_bottom"></div>
</div>