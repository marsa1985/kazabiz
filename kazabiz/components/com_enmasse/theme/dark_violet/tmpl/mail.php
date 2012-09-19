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
<div id="SendMail">
	<h2>Email deal to a friend.</h2>	
	<form action="index.php" name="mailForm" method="post" onsubmit="return validateForm()">
	<table>
		<tr>
			<td><label><?php echo JText::_('MAIL_SEND_TO'); ?>:</label></td>
			<td><input type="text" name="recipient" size="25" value=""/></td>
		</tr>
		<tr>
			<td><label><?php echo JText::_('MAIL_SUBJECT'); ?>:</label></td>
			<td><input type="text" name="subject" size="25" value=""/></td>
		</tr>
		<tr>
			<td><label><?php echo JText::_('MAIL_MESSAGE'); ?>:</label></td>
			<td>
				<textarea name="content" rows="10" cols="40"></textarea>
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
		<input type="submit" name="submit" value="<?php echo JText::_('SEND'); ?>"/>
	</p>
	</form>
	<div class="mailto-close">
		<a href="javascript: void window.close()" title="Close Window"><span><?php echo JText::_('CLOSE'); ?></span></a>
	</div>	
</div>