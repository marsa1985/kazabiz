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

$setting = $this->setting;
$row = $this->setting;

$oDefault = new JObject();
$oDefault->id = "";
$oDefault->name = JText::_(" ");

if(count($this->taxList) != 0)
{
	$emptyJOpt = JHTML::_('select.option', '', JText::_('') );
	$taxJOptList = array();
	array_unshift($this->taxList, $oDefault);
}
//add an empty select option for usergroup select
array_unshift($this->userGroupList, $oDefault);

$countryJOptList = $this->countryJOptList;

$option = 'com_enmasse';

?>
<?php 
JHTML::_( 'behavior.modal' );
JHTML::_('behavior.tooltip');?>

<script language="javascript" type="text/javascript">
     
<?php        
$version = new JVersion;
$joomla = $version->getShortVersion();
if(substr($joomla,0,3) >= '1.6'){
?>
        Joomla.submitbutton = function(pressbutton)
<?php
}else{
?>
        submitbutton = function(pressbutton)
<?php
}
?>
        {
            var form = document.adminForm;
            if (pressbutton == 'cancel')
            {
                submitform( pressbutton );
                return;
            }
            // do field validation
            if (form.company_name.value == "")
            {
                alert( "<?php echo JText::_( 'FILL_IN_COMPANY_NAME', true ); ?>" );
            }
            else if (form.address1.value == "")
            {
                alert( "<?php echo JText::_( 'FILL_IN_COMPANY_ADDRESS', true ); ?>" );
            }
            else if (form.city.value == "")
            {
                alert( "<?php echo JText::_( 'FILL_IN_COMPANY_CITY', true ); ?>" );
            }
            else if (form.state.value == "")
            {
                alert( "<?php echo JText::_( 'FILL_IN_COMPANY_STATE', true ); ?>" );
            }
            else if (form.country.value == "")
            {
                alert( "<?php echo JText::_( 'FILL_IN_COMPANY_COUNTRY', true ); ?>" );
            }
            else if (form.postal_code.value == "")
            {
                alert( "<?php echo JText::_( 'FILL_IN_COMPANY_POSTAL_CODE', true ); ?>" );
            }
            else if (isNaN(form.contact_number.value))
            {
                alert( "<?php echo JText::_( 'PHONE_CONTACT_SHOULD_BE_NUM', true ); ?>" );
            }
            else if (isNaN(form.contact_fax.value))
            {
                alert( "<?php echo JText::_( 'FAX_CONTACT_SHOULD_BE_NUM', true ); ?>" );
            }
            else if (!isEmail(form.customer_support_email.value))
            {
                alert( "<?php echo JText::_( 'PLEASE_ENTER_VALID_EMAIL', true ); ?>" );
            }
            else if (form.currency_prefix.value == "" && form.currency_postfix.value == "")
            {
                alert( "<?php echo JText::_( 'FILL_IN_DEFAULT_CURRENCY_PREFIX_POSTFIX', true ); ?>" );
            }
            else if (form.currency_separator.value.length && !form.currency_separator.value.match(/^[ ]$|^[\.]$|^[\,]$/) )
            {
                alert("<?php echo JText::_('INVALID_CURRENCY_THOUSANDS_SEPARATOR')?>");
            }
            else if (form.currency_decimal.value.length && !form.currency_decimal.value.match(/^[0-9]$/) )
            {
                alert("<?php echo JText::_('INVALID_CURRENCY_DECIMAL')?>");
            }
            else if (form.currency_decimal_separator.value.length && !form.currency_decimal_separator.value.match(/^[\.]$|^[\,]$/) )
            {
                alert("<?php echo JText::_('INVALID_DECIMAL_POINT_SEPARATOR')?>");
            }
            else if (form.currency_decimal_separator.value == form.currency_separator.value )
            {
                alert("<?php echo JText::_('THOUSANDS_SEPARATOR_IS_THE_SAME_WITH_DECIMAL_POINT_SEPARATOR')?>");
            }
            else
            {
                submitform( pressbutton );
            }
        }
        
        function isEmail(strEmail){
          validRegExp = /^[^@]+@[^@]+.[a-z]{2,}$/i;
           if (strEmail.search(validRegExp) == -1)
           {
              return false;
           }
           return true;
        }
        //-->
        </script>
 <body >
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="width-100 fltrt">
<fieldset class="adminform"><legend><?php echo JText::_('COMPANY_DETAIL');?></legend>
<table class="admintable">
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_COMPANY_NAME'),JTEXT::_('TOOLTIP_SETTING_COMPANY_NAME_TITLE'), 
                    '', JTEXT::_('C_NAME'));?> *</span></td>
		<td><input class="text_area" type="text" name="company_name"
			id="company_name" size="30" maxlength="50"
			value="<?php echo $row->company_name;?>" /></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_ADDRESS1'),JTEXT::_('TOOLTIP_SETTING_ADDRESS_TITLE1'), 
                    '', JTEXT::_('ADDRESS_1'));?> *</span></td>
		<td><input class="text_area" type="text" name="address1" id="address1"
			size="50" maxlength="255" value="<?php echo $row->address1;?>" /></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_ADDRESS2'),JTEXT::_('TOOLTIP_SETTING_ADDRESS_TITLE2'),  
                    '', JTEXT::_('ADDRESS_2'));?></span></td>
		<td><input class="text_area" type="text" name="address2" id="address2"
			size="50" maxlength="255" value="<?php echo $row->address2;?>" /></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_CITY'),JTEXT::_('TOOLTIP_SETTING_CITY_TITLE'), 
                    '', JTEXT::_('CITY'));?> *</span></td>
		<td><input class="text_area" type="text" name="city" id="city"
			size="50" maxlength="50" value="<?php echo $row->city;?>" /></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_STATE'),JTEXT::_('TOOLTIP_SETTING_STATE_TITLE'),  
                    '', JTEXT::_('STATE'));?> *</span></td>
		<td><input class="text_area" type="text" name="state" id="state"
			size="50" maxlength="50" value="<?php echo $row->state;?>" /></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_COUNTRY'),JTEXT::_('TOOLTIP_SETTING_COUNTRY_TITLE'), 
                    '', JTEXT::_('COUNTRY'));?> *</span></td>
		<td><?php echo $countryJOptList?></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_PCODE'),JTEXT::_('TOOLTIP_SETTING_PCODE_TITLE'), 
                    '', JTEXT::_('POSTAL_CODE'));?> *</span></td>
		<td><input class="text_area" type="text" name="postal_code"
			id="postal_code" size="20" maxlength="250"
			value="<?php echo $row->postal_code;?>" /></td>
	</tr>
</table>
</fieldset>
<fieldset class="adminform"><legend><?php echo JText::_('TAX_DETAILS')?></legend>
<table class="admintable">
<?php 
if(count($this->taxList)!= 0)
{
?>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT('TOOLTIP_SETTING_TAX'),JTEXT::_('TOOLTIP_SETTING_TAX_TITLE'), 
                    '', JTEXT::_('TAX'));?></span></td>
		<td><?php
		echo JHTML::_('select.genericList',$taxList, 'tax', null , 'text','text', $row->tax );
		?></td>
	</tr>
<?php 
}
?>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_TAX'),JTEXT::_('TOOLTIP_SETTING_TAX_NUM_TITLE1'), 
                    '', JTEXT::_('TAX_1'));?></span></td>
		<td><input class="text_area" type="text" name="tax_number1"
			id="tax_number1" size="30" maxlength="250"
			value="<?php echo $row->tax_number1;?>" /></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_TAX'),JTEXT::_('TOOLTIP_SETTING_TAX_NUM_TITLE2'), 
                    '', JTEXT::_('TAX_2'));?></span></td>
		<td><input class="text_area" type="text" name="tax_number2"
			id="tax_number2" size="30" maxlength="250"
			value="<?php echo $row->tax_number2;?>" /></td>
	</tr>
</table>
</fieldset>
<fieldset class="adminform"><legend><?php echo JText::_('STORE_DETAIL');?></legend>
<table class="admintable">
<!--
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip('Upload your Website\'s logo', 'Website\'s logo', 
                    '', JTEXT::_('Store logo'));?></span></td>
		<td><input class="text_area" type="text" name="logo_url" id="logo_url"
			size="30" maxlength="250" value="<?php echo $row->logo_url;?>" /> <?php $link= 'http://'.$_SERVER['SERVER_NAME'];?>
		<a rel="{handler: 'iframe', size: {x: 500, y: 400}}"
			href="<?php echo $link.'/administrator/components/com_enmasse/upload.php?inputId=logo_url'; ?>"
			class="modal">image...</a></td>
	</tr>
-->
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_PHONE'),JTEXT::_('TOOLTIP_SETTING_PHONE_TITLE'), 
                    '', JTEXT::_('STORE_CONTACT_NUMBER'));?></span></td>
		<td><input class="text_area" type="text" name="contact_number"
			id="contact_number" size="30" maxlength="250"
			value="<?php echo $row->contact_number;?>" /></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_FAX'),JTEXT::_('TOOLTIP_SETTING_FAX_TITLE'), 
                    '', JTEXT::_('STORE_CONTACT_FAX'));?></span></td>
		<td><input class="text_area" type="text" name="contact_fax"
			id="contact_fax" size="30" maxlength="250"
			value="<?php echo $row->contact_fax;?>" /></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_EMAIL'),JTEXT::_('TOOLTIP_SETTING_EMAIL_TITLE'), 
                    '', JTEXT::_('CUMTOMER_SUPPORT_EMAIL'));?> *</span></td>
		<td><input class="text_area" type="text" name="customer_support_email"
			id="customer_support_email" size="50" maxlength="250"
			value="<?php echo $row->customer_support_email;?>" /></td>
	</tr>
</table>
</fieldset>
<fieldset class="adminform"><legend><?php echo JText::_('CURRENCY_DETAILS')?></legend>
<table class="admintable">
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_CUR'),JTEXT::_('TOOLTIP_SETTING_CUR_TITLE'),
                    '', JTEXT::_('DEFAULT_CURRENCY'));?></span> </td>
		<td><input class="text_area" type="text" name="default_currency"
			id="default_currency" size="30" maxlength="250"
			value="<?php echo $row->default_currency;?>" /></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_CUR_PRE'),JTEXT::_('TOOLTIP_SETTING_CUR_PRE_TITLE'), 
                    '', JTEXT::_('CURRENCY_PREFIX'));?> </span></td>
		<td><input class="text_area" type="text" name="currency_prefix"
			id="currency_prefix" size="30" maxlength="250"
			value="<?php echo $row->currency_prefix;?>" /></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_CUR_POST'),JTEXT::_('TOOLTIP_SETTING_CUR_POST_TITLE'), 
                    '', JTEXT::_('CURRENCY_POSTFIX'));?> </span></td>
		<td><input class="text_area" type="text" name="currency_postfix"
			id="currency_postfix" size="30" maxlength="250"
			value="<?php echo $row->currency_postfix;?>" /></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JText::_('THOUSANDS_SEPARATOR')?></span></td>
		<td><input class="text_area" type="text" name="currency_separator"
			id="currency_separator" size="30" maxlength="250"
			value="<?php echo $row->currency_separator;?>" /></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JText::_('NUMBER_OF_DECIMAL')?> *</span></td>
		<td><input class="text_area" type="text" name="currency_decimal"
			id="currency_decimal" size="30" maxlength="250"
			value="<?php echo $row->currency_decimal;?>" /></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JText::_('DECIMAL_SEPARATOR')?> </span></td>
		<td><input class="text_area" type="text" name="currency_decimal_separator"
			id="currency_decimal_separator" size="30" maxlength="250"
			value="<?php echo $row->currency_decimal_separator;?>" /></td>
	</tr>
</table>
</fieldset>

<fieldset class="adminform"><legend><?php echo JTEXT::_('PRODUCT_IMG_DETAIL');?></legend>
<table class="admintable">
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_H'),JTEXT::_('TOOLTIP_SETTING_H_TITLE'), 
                    '', JTEXT::_('IMG_HEIGHT'));?></span></td>
		<td><input class="text_area" type="text" name="image_height"
			id="image_height" size="10" maxlength="250"
			value="<?php echo $row->image_height;?>" /></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_W'),JTEXT::_('TOOLTIP_SETTING_W_TITLE'),
                    '', JTEXT::_('IMG_WIDTH'));?></span></td>
		<td><input class="text_area" type="text" name="image_width"
			id="image_width" size="10" maxlength="250"
			value="<?php echo $row->image_width;?>" /></td>
	</tr>
</table>
</fieldset>
 
<fieldset class="adminform"><legend><?php echo JText::_('TERM_CONDITION')?></legend>
<table class="admintable">

	<tr>
		<td colspan='2'><span><?php echo JText::_('TERM_CONDITION_MESSAGE')?></span></td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_ARTICLE'),JTEXT::_('TOOLTIP_SETTING_ARTICLE_TITLE'), 
                    '', JTEXT::_('ARTICLE'));?></td>
		<td>
		  <input class="text_area" type="hidden" name="article_id" id="article_id" size="10" maxlength="250" value="<?php echo $row->article_id;?>" />
		  <input class="text_area" type="hidden" name="article_title" id="article_title" size="10" maxlength="250" value="<?php if(isset($row->article_id) && $row->article_id !=0)echo EnmasseHelper::getArticleTitleById($row->article_id);?>" />
          <?php
                    
                $version = new JVersion;
                $joomla = $version->getShortVersion();
                if(substr($joomla,0,3) >= '1.6')
                {
                    include_once JPATH_ADMINISTRATOR.DS.'components/com_enmasse/helpers/articles.php';
                    if (class_exists( 'JFormFieldArticles' ))
                    {
                        $articleSelectBox = new JFormFieldArticles();
                        echo $articleSelectBox->getInput();
                    }                  
                }
                else
                { 
                    include_once JPATH_ADMINISTRATOR.DS.'components/com_content/elements/article.php';
                    if (class_exists( 'JElementArticle' ))
                    {
                        $articleSelectBox = new JElementArticle();
                        echo $articleSelectBox->fetchElement('name', 'value',$a=null, 'control_name');
                    }
                }    
            ?>
		</td>
	</tr>
</table>
</fieldset>
<fieldset class="adminform"><legend><?php echo JText::_('MINUTE_TO_RELEASE_INVTY')?></legend>
<table class="admintable">

	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_MINUTE_TO_RELEASE_INVTY'),JTEXT::_('TOOLTIP_MINUTE_TO_RELEASE_INVTY_TITLE'), 
                    '', JTEXT::_('MINUTE_TO_RELEASE'));?></span></td>
		<td><div style="border: solid 1px #bbb;width:50px;line-height: 16px;"><?php echo $row->minute_release_invty;?></div></td>
	</tr>
</table>

</fieldset>

<fieldset class="adminform"><legend><?php echo JText::_('CASH_MINUTE_TO_RELEASE_INVTY')?></legend>
<table class="admintable">

	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_CASH_MINUTE_TO_RELEASE_INVTY'),JTEXT::_('TOOLTIP_CASH_MINUTE_TO_RELEASE_INVTY_TITLE'), 
                    '', JTEXT::_('CASH_MINUTE_TO_RELEASE'));?></span></td>
		<td><input class="text_area" type="text" name="cash_minute_release_invty"
			id="cash_minute_release_invty" size="10"
			value="<?php echo $row->cash_minute_release_invty;?>" /></div></td>
	</tr>
</table>

</fieldset>
    
<fieldset class="adminform"><legend><?php echo JText::_('FRONT_END_LAYOUT')?></legend>
<table class="admintable">
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_THEME'),JTEXT::_('TOOLTIP_SETTING_THEME_TITLE'), 
                    '', JTEXT::_('THEME'));?></span></td>
		<td>
		<?php
			$arr = array();
			foreach (EnmasseHelper::webThemeList() as $key=>$value)
				array_push($arr, JHTML::_('select.option', $value, JText::_($value) ));
			echo JHTML::_('select.genericlist', $arr, 'theme', null, 'value', 'text', $row->theme);
		?>
		</td>
	</tr>
	<tr>
		<td width="120" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_MOBILE_THEME'),JTEXT::_('TOOLTIP_SETTING_MOBILE_THEME_TITLE'), 
                    '', JTEXT::_('MOBILE_THEME'));?></span></td>
		<td>
		<?php
			$arr = array();
			foreach (EnmasseHelper::mobileThemeList() as $key=>$value)
				array_push($arr, JHTML::_('select.option', $value, JText::_($value) ));
			echo JHTML::_('select.genericlist', $arr, 'mobile_theme', null, 'value', 'text', $row->mobile_theme);
		?>
		</td>
	</tr>
</table>
</fieldset>    

<fieldset class="adminform"><legend><?php echo JText::_('HEAD_LOCATION_POP_UP')?></legend>
<script language="javascript" type="text/javascript">
function showDescription(subscriptionList){
	selectedSubscription = subscriptionList.options[subscriptionList.selectedIndex].value;
	description = document.getElementById(selectedSubscription).innerHTML;
	if (description!='')
	{
		document.getElementById("subscriptionDescription").innerHTML = description;
	}
}
</script>
<table class="admintable">

	<tr>
		<td width="140" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_LOCATION_POP_UP'),JTEXT::_('TOOLTIP_SETTING_LOCATION_POP_UP_TITLE'), 
                    '', JTEXT::_('LOCATION_POP_UP'));?></span>
        </td>
		<td>
		<?php
//		if ($row->active_popup_location == null){
//		  echo JHTML::_('select.booleanlist', 'active_popup_location', 'class="inputbox"', 1);
//		}else{
//		  echo JHTML::_('select.booleanlist', 'active_popup_location', 'class="inputbox"', $row->active_popup_location);
//		}
			$arr = array();
			$subscriptionList = '';
			foreach (EnmasseHelper::subscriptionList() as $key=>$value)
			{
				array_push($arr, JHTML::_('select.option', $value, JText::_($value) ));
				$subscriptionName = $value;
				$subscriptionDesc = strtoupper($value."_DESC");
				$selectedSubscription = strtoupper($row->subscription_class."_DESC");
				if($subscriptionDesc==$selectedSubscription)
				{
					$defaultDescription = JText::_($subscriptionDesc);
				}
				$subscriptionList .= "<div id=\"$subscriptionName\" style=\"display: none\">".JText::_($subscriptionDesc)."</div>";
			}
			echo JHTML::_('select.genericlist', $arr, 'subscription_class',  'onChange="showDescription(this);"', 'value', 'text', $row->subscription_class);
		?>
		</td>
		<td>
			<?php echo $subscriptionList; ?>
			<div id="subscriptionDescription" style="color: red;"><?php echo $defaultDescription;?></div>
		</td>
	</tr>

	<!--<tr>
	 	<td width="120" align="right" class="key"><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_ACY'),JTEXT::_('TOOLTIP_SETTING_ACY_TITLE'),
                    '', JTEXT::_('ACY_ID'));?></td>
		<td><input class="text_area" type="text" name="acy_module_id"
			id="acy_module_id" size="10" maxlength="250"
			value="<?php echo $row->acy_module_id;?>" /></td>
	</tr>
--></table>

</fieldset>

<!-- ---------------- Start - AlphaUserPoint ---------------- -->

<fieldset class="adminform"><legend><?php echo JText::_('HEAD_POINT_SYSTEM')?></legend>
<table class="admintable">

	<tr>
		<td width="140" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_POINT_SYSTEM'),JTEXT::_('TOOLTIP_SETTING_POINT_SYSTEM_TITLE'), 
                    '', JTEXT::_('POINT_SYSTEM'));?></span>
        </td>
		<td>
		<?php
			$arr = array();
			array_push($arr, JHTML::_('select.option', "no", JText::_("No") ));
			foreach (EnmasseHelper::pointSystemList() as $key=>$value)
				array_push($arr, JHTML::_('select.option', $value, JText::_($value) ));
			echo JHTML::_('select.genericlist', $arr, 'point_system_class', null, 'value', 'text', $row->point_system_class);
		?>		
		</td>
	</tr>
</table>

</fieldset>

<!-- ---------------- End - AlphaUserPoint ---------------- -->

<!-- ---------------- Start - Buy without registration ---------------- -->

<fieldset class="adminform"><legend><?php echo JText::_('HEAD_GUEST_BUY')?></legend>
<table class="admintable">

	<tr>
		<td width="140" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_GUEST_BUY'),JTEXT::_('TOOLTIP_SETTING_GUEST_BUY_TITLE'), 
                    '', JTEXT::_('GUEST_BUY'));?></span>
        </td>
		<td>
		<?php
		if ($row->active_guest_buying == null){
		  echo JHTML::_('select.booleanlist', 'active_guest_buying', 'class="inputbox"', 1);
		}else{
		  echo JHTML::_('select.booleanlist', 'active_guest_buying', 'class="inputbox"', $row->active_guest_buying);
		}
		?>
		</td>
	</tr>
</table>

</fieldset>

<!-- ---------------- End - Buy without registration ---------------- -->
<!-- Begin - group for sale and merchant -->
<fieldset class="adminform"><legend><?php echo JText::_('SALE_AND_MERCHANT_GROUP')?></legend>
<table class="admintable">
	<tr>
		<td width="140" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_SALE_GROUP'),JTEXT::_('TOOLTIP_SETTING_SALE_GROUP_TITLE'), 
                    '', JTEXT::_('SALE_GROUP'));?></span>
        </td>
		<td>
			<?php echo JHtml::_('select.genericList', $this->userGroupList, 'sale_group', null, 'id', 'name', $row->sale_group)?>
		</td>
	</tr>
	<tr>
		<td width="140" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_MERCHANT_GROUP'),JTEXT::_('TOOLTIP_SETTING_MERCHANT_GROUP_TITLE'), 
                    '', JTEXT::_('MERCHANT_GROUP'));?></span>
        </td>
		<td>
			<?php echo JHtml::_('select.genericList', $this->userGroupList, 'merchant_group', null, 'id', 'name', $row->merchant_group)?>
		</td>
	</tr>
</table>
</fieldset>
<!-- End - group for sale and merchant -->

<!-- ------------ delivery user group ----------- -->
<fieldset class="adminform"><legend><?php echo JText::_('DELIVERY_PERSON_GROUP')?></legend>
<table class="admintable">
	<tr>
		<td width="140" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_DELIVERY_GROUP'),JTEXT::_('TOOLTIP_SETTING_DELIVERY_GROUP_TITLE'), 
                    '', JTEXT::_('DELIVERY_PERSON_GROUP'));?></span>
        </td>
		<td>
			<?php echo JHtml::_('select.genericList', $this->userGroupList, 'delivery_group', null, 'id', 'name', $row->delivery_group)?>
		</td>
	</tr>
	
</table>
</fieldset>
<!-- ------------ delivery user group end ----------- -->
<!-- Begin - bill setting -->
<fieldset class="adminform"><legend><?php echo JText::_('BILL_SETTING')?></legend>
<table class="admintable">
	<tr>
		<td width="140" align="right" class="key"><span><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_SETTING_BILL'),JTEXT::_('TOOLTIP_SETTING_BILL_TITLE'), 
                    '', JTEXT::_('BILL_SENDING_AUTOMATIC'));?></span>
        </td>
		<td>
			<?php echo JHtml::_('select.booleanlist', 'sending_bill_auto','class="inputbox"', $row->sending_bill_auto)?>
		</td>
	</tr>
	
</table>
</fieldset>
<!-- End - bill setting -->
<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="controller" value="setting" />
<input type="hidden" name="task" value="" />
</div>
</form>
</body>