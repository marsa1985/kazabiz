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

?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script language="javascript" type="text/javascript">

	function validateEmail($email)
	{
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if( !emailReg.test( $email ) )
        {
        	return false;
        }
        else
		{
        	return true;
        }
	}

	function validateForm()
	{
		var form = document.mailForm;
		if (form.recipient.value == "" || validateEmail(form.recipient.value)==false)
		{
			alert("Invalid mail");
			return false;
		}
		if (form.subject.value == "")
		{
			alert("Empty subject");
			return false;
		}
		if (form.content.value == "")
		{
			alert("Empty content");
			return false;
		}		
		return true;
	}		
</script>
<?php 
	if($this->success=='1')
	{	
		echo JText::_('MAIL_SEND_SUCCESSFULLY');
	}	
	elseif($this->success=='0')
	{
		echo JText::_('MAIL_SEND_FAILED');
	}	
?>
<style type="text/css">
	#SendMail table td, #SendMail label{
		font-size:100%;
	}
</style>
<div id="SendMail">
	<h4><?php echo JText::_('EMAIL_TO_FRIEND');?></h4>	
	<form action="index.php" name="mailForm" method="post" onsubmit="return validateForm()">
	<table>
		<tr>
			<td><label><?php echo JText::_('MAIL_SEND_TO'); ?>:</label></td>
			<td><input type="text" class="text" name="recipient" value=""/></td>
		</tr>
		<tr>
			<td><label><?php echo JText::_('MAIL_SUBJECT'); ?>:</label></td>
			<td><input type="text" class="text" name="subject" value=""/></td>
		</tr>
		<tr>
			<td><label><?php echo JText::_('MAIL_MESSAGE'); ?>:</label></td>
			<td>
				<textarea name="content" style="width:215px" rows="5"></textarea>
				<br/><?php echo JText::_('MAIL_DESC'); ?>
			</td>
		</tr>	
	</table>		
	<p>
        <input type="hidden" name="itemid" value="<?php echo $this->itemid; ?>" />
		<input type="hidden" name="userid" value="<?php echo $this->userid; ?>" />
		<input type="hidden" name="dealid" value="<?php echo $this->dealid; ?>" />
		<input type="hidden" name="option" value="com_enmasse" />
		<input type="hidden" name="controller" value="mail" />
		<input type="hidden" name="task" value="sendMail" />	
		<input class="button" type="submit" name="submit" value="<?php echo JText::_('SEND'); ?>"/>
		<input class="button" type="button" name="submit" onclick="window.close()" value="<?php echo JText::_('CLOSE'); ?>"/>
	</p>
	</form>
	
</div>