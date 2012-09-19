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

//using for check to generate calendar popup
$version = new JVersion;
$joomla = $version->getShortVersion();

$row = $this->oContact;

$option = 'com_enmasse';

JHTML::_('behavior.calendar');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
JHTML::_('behavior.formvalidation');
JFactory::getDocument()->addScript("components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js");
JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict()');

?>
<style>
.width-40{width: 40%}
.width-60{width: 60%}
.width-100{width: 100%}
.fltlft{float: left}
.fltrgt{float: right}
fieldset {
    background-color: #FFFFFF;
    padding: 5px 5px 17px;
    border: 1px solid #CCCCCC;
    margin: 10px;
}
fieldset.pagetitle{
    border: none;
    margin: 0;
    padding: 5px 5px 0;
}
fieldset.pagetitle h3{
    text-decoration: underline;  
}
legend {
    color: #146295;
    font-size: 1.182em;
    font-weight: bold;
}
.error{
	border: 1px red solid;
	background: none;
	border-radius: none !important;
}
</style>
<?php
//load submenu for sale person
$oldTpl = $this->setLayout('sales_person_sub_menu');
echo $this->loadTemplate();
$this->setLayout($oldTpl); 
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate">
<fieldset class="pagetitle">
<h3>
	<?php 
		if($row->id > 0)
			echo JText::_("SALE_PERSON_EDIT_MERCHANT") .$row->name;
		else 
			echo JText::_("SALE_PERSON_ADD_MERCHANT");
	?>
</h3>
</fieldset>    
<div class="width-100 fltrt">
<fieldset class="adminform">
<legend><?php echo JText::_('MERCHANT_DETAIL');?></legend>
<table class="admintable" style="width: 100%">
	<tr>
		<td width="100"   class="key"><label for="name"><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_MERCHANT_NAME'),JTEXT::_('TOOLTIP_MERCHANT_NAME_TITLE'), 
                    '', JTEXT::_('MERCHANT_NAME'));?></label> *</td>
		<td><input class="required" type="text" name="name" id="name"
			size="50" maxlength="250" value="<?php echo htmlentities($row->name, ENT_QUOTES,"UTF-8");?>" />
			<input type="hidden"  name="tempName" value="<?php echo htmlentities($row->name, ENT_QUOTES,"UTF-8");?>" />
			</td>
	</tr>
	<tr>
		<td width="100"   class="key"><?php echo JHTML::tooltip(JTEXT::_('BRANCH_DESC_TOOLTIP'), JTEXT::_('BRANCH_DESC_TOOLTIP_TITLE'),  
                    '', JTEXT::_('MERCHANT_DESCRIPTION'));?></td>
		<td><textarea name="description" id="description" cols="20" rows="3"><?php echo $row->description; ?></textarea>
		</td>
	</tr>	
	<tr>
		<td width="100"   class="key"><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_MERCHANT_URL'),JTEXT::_('TOOLTIP_MERCHANT_URL_TITLE'), 
                    '', JTEXT::_('MERCHANT_WEB_URL'));?></td>
		<td><input class="" type="text" name="web_url" id="web_url"
			size="50" maxlength="250" value="<?php echo $row->web_url;?>" /></td>
	</tr>
	<tr>
		<td width="100"   class="key"><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_MERCHANT_LOGO'),JTEXT::_('TOOLTIP_MERCHANT_LOGO_TITLE'), 
                    '', JTEXT::_('MERCHANT_LOGO_URL'));?></td>
		<td><input class="" type="text" name="logo_url" id="logo_url"
			size="50" maxlength="250" readonly="readonly" value="<?php echo $row->logo_url;?>" />
           
            <a rel="{handler: 'iframe', size: {x: 500, y: 400}}"
			href="<?php echo 'index.php?option=com_enmasse&controller=uploader&task=display&tmpl=component&parentId=logo_url&parent=merchant'; ?>"
			class="modal"><?php echo JText::_('MERCHANT_LOGO_URL_LINK');?></a></td>
	</tr>
	
	<tr>
		<td width="100" class="key"><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_MERCHANT_PUBLISHED_TITLE'),JTEXT::_('MERCHANT_PUBLISHED'), '', JTEXT::_('MERCHANT_PUBLISHED'));?></td>
		<td><?php
		if ($row->published == null)
		{
			echo JHTML::_('select.booleanlist', 'published',
                          'class="inputbox"', 1);
		}
		else
		{
		echo JHTML::_('select.booleanlist', 'published',
                          'class="inputbox"', $row->published);
		}
		?></td>
	</tr>
	<?php if($row->id > 0):?>
		<tr>
			<td width="100"   class="key"><?php echo JText::_('CREATED_AT')?></td>
			<td><?php echo DatetimeWrapper::getDisplayDatetime($row->created_at); ?></td>
		</tr>
		<tr>
			<td width="100"   class="key"><?php echo JText::_('UPDATED_AT')?></td>
			<td><?php echo DatetimeWrapper::getDisplayDatetime($row->updated_at); ?></td>
		</tr>
	<?php endif;?>
</table>
</fieldset>

<fieldset class="adminform"><legend><?php echo JText::_('MERCHANT_USER_DETAIL')?></legend>
<table class="admintable" style="width: 100%">
	<tr>
		<td width="100"   class="key">
			<label for="user_name"><?php echo JHTML::tooltip(JTEXT::_('MERCHANT_USERNAME_TOOLTIP'), JTEXT::_('MERCHANT_USERNAME_TOOLTIP_TITLE'), 
                    '', JTEXT::_('MERCHANT_USER_NAME'));?></label> *
        </td>
		<td><input style="margin: 0 5px 0 0;" class="required" type="text" name="user_name"
			id="user_name" size="50" maxlength="250"
			value="<?php echo $row->user_name;?>" onblur="checkValidUser()" />
			<input type="hidden" name="temp_user_name" value="<?php echo $row->user_name;?>" />
			<span id='invalid_msg' style="display: none;color:red;">(<?php echo JText::_('MERCHANT_INVALID_USER_NAME');?>)</span>
			<span id='duplicated_msg' style="display: none;color:red;">(<?php echo JText::_('DUP_MERCHANT_USERNAME_MSG');?>)</span>
		</td>
	</tr>
</table>
</fieldset>

<fieldset class="adminform"><legend><?php echo JText::_('MERCHANT_GOOGLE_MAP')?></legend>
<table class="admintable" style="width: 100%">
	<tr>
		<td width="100"   class="key"><?php echo JHTML::tooltip(JTEXT::_('MAP_WIDTH_TOOLTIP'), JTEXT::_('MAP_WIDTH_TOOLTIP_TITLE'), 
                    '', JTEXT::_('MERCHANT_MAP_WIDTH'));?></td>
		<td><input class="" type="text" name="google_map_width"
			id="google_map_width"  maxlength="250"
			value="<?php
				if($row->google_map_width == null)
					echo 200;
				else 
					echo $row->google_map_width;
			?>" />
		</td>
	</tr>
	<tr>
		<td width="100"   class="key"><?php echo JHTML::tooltip(JTEXT::_('MAP_HEIGHT_TOOLTIP'), JTEXT::_('MAP_HEIGHT_TOOLTIP_TITLE'), 
                    '', JTEXT::_('MERCHANT_MAP_HEIGHT'));?></td>
		<td><input class="" type="text" name="google_map_height"
			id="google_map_height"  maxlength="250"
			value="<?php
				if($row->google_map_height == null)
					echo 200;
				else 
					echo $row->google_map_height;
			?>" />
		</td>
	</tr>
</table>
</fieldset>

<fieldset class="adminform"><legend>Branches</legend>
<table class="admintable" style="width: 100%" id="branches">
<?php
$branches = json_decode($row->branches, true);
$count = 0;
if($branches!='')
{
	foreach($branches as $branch)
	{
		$count++;	
?>
		<tr><th colspan=2>#<?php echo $count . " - " .$branch['name']; ?></th></tr>
		<tr>
			<td width="100"   class="key">Check to remove</td>
			<td><input type="checkbox" name="remove-<?php echo $count; ?>" id="remove-<?php echo $count; ?>"/></td>
		</tr>
		<tr>
			<td width="100"   class="key">
				<label for="name-<?php echo $count; ?>"><?php echo JHTML::tooltip(JTEXT::_('BRANCH_NAME_TOOLTIP'), JTEXT::_('BRANCH_NAME_TOOLTIP_TITLE'), 
	                    '', JTEXT::_('MERCHANT_NAME'));?></label> *
	        </td>
			<td><input class="required" type="text" name="name-<?php echo $count; ?>" id="name-<?php echo $count; ?>" size="50" maxlength="250" value="<?php echo $branch['name']; ?>" />
			</td>
		</tr>
		<tr>
			<td width="100"   class="key">
				<label for="description-<?php echo $count; ?>"><?php echo JHTML::tooltip(JTEXT::_('BRANCH_DESC_TOOLTIP'), JTEXT::_('BRANCH_DESC_TOOLTIP_TITLE'),  
	                    '', JTEXT::_('MERCHANT_DESCRIPTION'));?></label>
	        </td>
			<td><textarea name="description-<?php echo $count; ?>" id="description-<?php echo $count; ?>" cols="20" rows="3"><?php echo $branch['description']; ?></textarea>
			</td>
		</tr>
		<tr>
			<td width="100"   class="key">
				<label for="address-<?php echo $count;?>"><?php echo JHTML::tooltip(JTEXT::_('BRANCH_ADD_TOOLTIP'), JTEXT::_('BRANCH_ADD_TOOLTIP_TITLE'), 
		                    '', JTEXT::_('MERCHANT_ADDRESS'));?></label> *
	        </td>
			<td><textarea class="required" name="address-<?php echo $count; ?>" id="address-<?php echo $count; ?>" cols="20" rows="3"><?php echo $branch['address']; ?></textarea>
			</td>
		</tr>	
		<tr>
			<td width="100"   class="key">
				<label for="telephone-<?php echo $count;?>"><?php echo JHTML::tooltip(JTEXT::_('BRANCH_TEL_TOOLTIP'), JTEXT::_('BRANCH_TEL_TOOLTIP_TITLE'), 
	                    '', JTEXT::_('MERCHANT_TELEPHONE'));?></label> *
	        </td>
			<td><input class="required validate-phone" type="text" name="telephone-<?php echo $count; ?>" id="telephone-<?php echo $count; ?>"  maxlength="250" value="<?php echo $branch['telephone'] ?>" />
			</td>
		</tr>
		<tr>
			<td width="100"   class="key">
				<label for="fax-<?php echo $count; ?>"><?php echo JHTML::tooltip(JTEXT::_('BRANCH_FAX_TOOLTIP'), JTEXT::_('BRANCH_FAX_TOOLTIP_TITLE'),
	                    '', JTEXT::_('MERCHANT_FAX'));?></label>
	        </td>
			<td><input class="validate-phone" type="text" name="fax-<?php echo $count; ?>" id="fax-<?php echo $count; ?>"  maxlength="250" value="<?php echo $branch['fax'] ?>" />
			</td>
		</tr>
		<tr>
			<td width="100"   class="key">
				<label for="google_map_lat-<?php echo $count;?>"><?php echo JHTML::tooltip(JTEXT::_('BRANCH_LAT_TOOLTIP'), JTEXT::_('BRANCH_LAT_TOOLTIP_TITLE'),
		                    '', JTEXT::_('MERCHANT_GOOGLE_LATITUDE'));?></label> *
	        </td>
			<td><input class=" required validate-numeric" type="text" name="google_map_lat-<?php echo $count; ?>" id="google_map_lat-<?php echo $count; ?>"  maxlength="250" value="<?php echo $branch['google_map_lat'];?>" /></td>
		</tr>
		<tr>
			<td width="100"   class="key">
				<label for="google_map_long-<?php echo $count;?>"><?php echo JHTML::tooltip(JTEXT::_('BRANCH_LONG_TOOLTIP'), JTEXT::_('BRANCH_LONG_TOOLTIP_TITLE'), 
		                    '', JTEXT::_('MERCHANT_GOOGLE_LONGTITUDE'));?></label> *
	        </td>
			<td>
				<input type="hidden" name="branchname-<?php echo $count; ?>" value="branch<?php echo $count; ?>"/>
				<input class=" required validate-numeric" type="text" name="google_map_long-<?php echo $count; ?>" id="google_map_long-<?php echo $count; ?>"  maxlength="250" value="<?php echo $branch['google_map_long'];?>" />
			</td>
		</tr>
		<tr>
			<td width="100"   class="key">
				<label for="google_map_zoom-<?php echo $count; ?>"><?php echo JHTML::tooltip(JTEXT::_('BRANCH_ZOOM_TOOLTIP'), JTEXT::_('BRANCH_ZOOM_TOOLTIP_TITLE'), 
	                    '', JTEXT::_('MERCHANT_GOOGLE_ZOOM'));?></label>
	            </td>
		<?php 
			if ($branch['google_map_zoom']=='' || !is_numeric($branch['google_map_zoom']))
			{
				$branch['google_map_zoom'] = 14;
			}
		?>	                    
			<td><input type="hidden" name="branchname-<?php echo $count; ?>" value="branch<?php echo $count; ?>"/><input class="validate-numeric" type="text" name="google_map_zoom-<?php echo $count; ?>" id="google_map_zoom-<?php echo $count; ?>"  maxlength="250" value="<?php echo $branch['google_map_zoom'];?>" /></td>
		</tr>		
		<tr>
			<td colspan="2"><hr/></td>
		</tr>
<?php	
	}
}
?>
</table>
<p><a href="#" onclick="addRow(); return false;">Add new branch</a><br/></p>
</fieldset>
<input type="hidden" name="num_of_branches" value="<?php echo $count; ?>" />
<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="controller" value="salesPerson" />
<input type="hidden" name="task" value="merchantSave" />
</div>
<div class="deal-save-button">
	<input type="button" class="button" value="<?php echo JText::_("SALES_PERSON_SAVE_BTN_LABEL")?>" onclick="validateForm()"/>
	<input type="button" class="button" value="<?php echo JText::_("SALES_PERSON_CANCEL_BTN_LABEL")?>" onclick="javascript:{window.location.href='<?php echo JRoute::_("index.php?option=com_enmasse&controller=salesPerson&task=merchantList")?>'}"/>
</div>
</form>
<script>
function addRow()
{
	count = jQuery("input[name='num_of_branches']").val();
	count = parseInt(count) + 1;
	jQuery("input[name='num_of_branches']").val(count);
	jQuery("#branches").append(
        '<tr>'+
            '<td width="100"   class="key">Check to remove</td>'+
            '<td><input type="checkbox" name="remove-' + count + '" id="remove-' + count + '"/></td>'+
        '</tr>'+
        '<tr>'+
            '<td width="100"   class="key">'+
            '<label for="name-' + count + '">'+
            '<?php echo JTEXT::_('MERCHANT_NAME');?>'+
            '</label> *</td>'+
            '<td><input class="required" type="text" name="name-' + count + '" id="name-' + count + '" size="50" maxlength="250" value="" /></td>'+
        '</tr>'+
        '<tr>'+
            '<td width="100"   class="key">'+
            '<label for="description-' + count + '">'+
            '<?php echo JTEXT::_('MERCHANT_DESCRIPTION');?>'+
			'</label>'+
            '</td>'+
            '<td><textarea name="description-' + count + '" id="description-' + count + '" cols="20" rows="3"></textarea></td>'+
        '</tr>'+
        '<tr>'+
	       '<td width="100"   class="key">'+
		        '<label for="address-' + count + '">'+
		        '<?php echo JTEXT::_('MERCHANT_ADDRESS');?>'+
				'</label> *'+
	       '</td>'+
            '<td><textarea class="required" name="address-' + count + '" id="address-' + count + '" cols="20" rows="3"></textarea></td>'+
        '</tr>'+
        '<tr>'+
	        '<td width="100"   class="key">'+
		        '<label for="telephone-' + count + '">'+
		        '<?php echo JTEXT::_('MERCHANT_TELEPHONE');?>'+
				'</label> *'+
	        '</td>'+
        	'<td><input class="required validate-phone" type="text" name="telephone-' + count + '" id="telephone-' + count + '"  maxlength="250" value="" /></td>'+
        '</tr>'+
        '<tr>'+
	        '<td width="100"   class="key">'+
		        '<label for="fax-' + count + '">'+
		        '<?php echo JTEXT::_('MERCHANT_FAX');?>'+
				'</label>'+
        	'</td>'+
            '<td><input class=" validate-phone" type="text" name="fax-' + count + '" id="fax-' + count + '"  maxlength="250" value="" /></td>'+
        '</tr>'+
        '<tr>'+
	        '<td width="100"   class="key">'+
		        '<label for="google_map_lat-' + count + '">'+
		       '<?php echo JTEXT::_('MERCHANT_GOOGLE_LATITUDE');?>'+
				'</label> *'+
			'</td>'+
            '<td><input class=" required validate-numeric" type="text" name="google_map_lat-' + count + '" id="google_map_lat-' + count + '"  maxlength="250" value="" /></td>'+
        '</tr>'+
        '<tr>'+
        	'<td width="100" class="key">'+
		        '<label for="google_map_long-' + count + '">'+
		        '<?php echo JTEXT::_('MERCHANT_GOOGLE_LONGTITUDE');?>'+
				'</label> *'+
			'</td>'+
            '<td><input class=" required validate-numeric" type="text" name="google_map_long-' + count + '" id="google_map_long-' + count + '"  maxlength="250" value="" /></td>'+
        '</tr>'+
        '<tr>'+
        	'<td width="100" class="key">'+
        		'<label for="goole_map_zoom-' + count + '">'+
        		'<?php echo JTEXT::_('MERCHANT_GOOGLE_ZOOM');?>'+
				'</label>'+
			'</td>'+
            '<td><input type="hidden" name="branchname-' + count + '" value="branch' + count + '"/>'+
                '<input class="validate-numeric" type="text" name="google_map_zoom-' + count + '" id="google_map_zoom-' + count + '"  maxlength="250" value="" />'+
           ' </td>'+
        '</tr>'+
        '<tr>'+
            '<td colspan="2\"><hr/></td>'+
        '</tr>'
);
}

function checkValidUser()
{
	var form = document.adminForm;
	var ob = document.getElementById('invalid_msg');
	 jQuery.post("index.php?option=com_enmasse&tmpl=component&controller=salesPerson&task=checkUserName&userName="+form.user_name.value + "&mer_id=" +form.id.value,function(data) {
		    if(data == 'invalid')
  			{
  				form.user_name.addClass('error');
  				document.getElementById('duplicated_msg').style.display = "none";
  			    ob.style.display = "inline";
  			}
		    else if(data == 'duplicated' && form.user_name.value!=form.temp_user_name.value)
		    {
		    	form.user_name.focus();
		    	ob.style.display = "none";
		    	document.getElementById('duplicated_msg').style.display = "inline";
    		}
  			else
  			{
  				ob.style.display = "none";
  				form.user_name.removeClass('error');
  				document.getElementById('duplicated_msg').style.display = "none";
		  	}
 	   
    });
}

function validateForm()
{
	var form = document.adminForm;
	sName = jQuery.trim(form.name.value.replace(/(<.*?>)/ig,""));
    //-------- do field validation
    //1. validate required field
    var msg = "";
    jQuery('.required').each(function(){
        if(jQuery(this).val() == "")
        {
            var tmp = jQuery('label[for="'+jQuery(this).attr('id') +'"]').text();
            msg += tmp + ", ";
            jQuery(this).addClass('error');
        }else
        {
        	jQuery(this).removeClass('error');
        }
    });
    if(msg != "")
    {
        msg = msg.slice(0, -2);
        alert("Fields: " + msg + " are required!");
        return;
    }
    //2.validate numeric field
	msg = "";
    jQuery('.validate-numeric').each(function(){
        if(jQuery(this).hasClass("invalid"))
        {
            var tmp = jQuery('label[for="'+jQuery(this).attr('id') +'"]').text();
            msg += tmp + ", ";
            jQuery(this).addClass('error');
        }else
        {
        	jQuery(this).removeClass('error');
        }
    });
    if(msg != "")
    {
        alert("Fields: " + msg + " must be numeric value!");
        return;
    }
  	//3.validate phone number field
	msg = "";
    jQuery('.validate-phone').each(function(){
        var p = /^[0-9 \.,\-\(\)\+]*$/;
        if(this.value !="" && ! p.test(this.value))
        {
            var tmp = jQuery('label[for="'+jQuery(this).attr('id') +'"]').text();
            msg += tmp + ", ";
            jQuery(this).addClass('error');
        }else
        {
        	jQuery(this).removeClass('error');
        }
    });
    if(msg != "")
    {
        alert("Fields: " + msg + " not is the valid phone number!");
        return;
    }
    //validate merchant name
    if (sName == "")
    {
        alert( "<?php echo JText::_( 'INVALID_NAME', true ); ?>" );
        return;
    }

	if(jQuery('#adminForm .error').length > 0)
	{
		alert( "<?php echo JText::_( 'SALE_PERSON_FORM_INVALID'); ?>" );
		return;
	}
	//validate Merchant name duplicate
	jQuery.post("index.php?option=com_enmasse&tmpl=component&controller=salesPerson&task=checkMerchantName", { merchantName: sName },function(data) {
  	   if(data == 'true' && sName!=form.tempName.value){
    		  alert("<?php echo JText::_('MERCHANT_NAME_DUPLICATED', true); ?>");
        }
	   else
	   {
		   form.submit();
    	}
	   
 	});
	//form.submit();
}
</script>
