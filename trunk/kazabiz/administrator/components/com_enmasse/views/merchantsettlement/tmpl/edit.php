
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
// Get joomla version
$version = new JVersion;
$joomla = $version->getShortVersion();

$row = $this->merchant;
$option = 'com_enmasse';

// create sales person select list with empty option
$oEmpty = new JObject();
$oEmpty->id = "";
$oEmpty->name = "";		
$salesPersonList = $this->salesPersonList;
array_unshift($salesPersonList, $oEmpty);

$merStatusList = array();
foreach ($this->merStatusList as $key => $value)
{
	$merStatusList[$key] = "MERCHANT_" .strtoupper($value);
}

?>
<style>
.error {border: 1px red solid};
</style>

<script src="components/com_enmasse/script/jquery.js"></script>
<?php JHTML::_( 'behavior.modal' );

JHTML::_('behavior.tooltip') ;
$version = new JVersion;
$joomla = $version->getShortVersion();
if(substr($joomla,0,3) >= 1.6){
?>
    <script language="javascript" type="text/javascript">
         $.noConflict();
        Joomla.submitbutton = function(pressbutton)
<?php
}else{
?>
	<script language="javascript" type="text/javascript">
	$.noConflict();
	submitbutton = function(pressbutton)
	<?php
	}
	?>
        {

			function isFloat(val)
			{
				if(!val || (typeof val != "string" || val.constructor != String))
				{
        			return false;
        	    }
        	    var isNumber = !isNaN(new Number(val));
        	    if(isNumber)
            	{
        	    	if(val.indexOf('.') != -1)
            	    {
        	        	return true;
        	    	}
            		else
					{
        	        	return false;
        	      	}
        	    }
        	    else
            	{
        	    	return false;
        	    }
			}
       	  
   	           	 
            var form = document.adminForm;
            if (pressbutton == 'cancel')
            {
                submitform( pressbutton );
                return;
            }
            
            sName = jQuery.trim(form.name.value.replace(/(<.*?>)/ig,""));
            // do field validation
            var msg = "";
            
            jQuery('.required').each(function(){
                if(jQuery(this).val() == "")
                {
                    var tmp = jQuery('label[for="'+jQuery(this).attr('id') +'"]').text();
                    msg += tmp.replace(/(<.*>)|(<\/.*>)/ig,"");
                    jQuery(this).addClass('error');
                }else
                {
                	jQuery(this).removeClass('error');
                }
            });
            if(msg != "")
            {
                alert("Fields:\n" + msg + "\n are required!.");
                return;
            }
            if (sName == "")
            {
                alert( "<?php echo JText::_( 'INVALID_NAME', true ); ?>" );
            }                                    
            else
            {
              	 jQuery.post("index.php?option=com_enmasse&tmpl=component&controller=merchant&task=checkDuplicatedName", { merchantName: sName },function(data) {
                 	   if(data == 'true' && sName!=form.tempName.value){
                   		  alert("<?php echo JText::_('MERCHANT_NAME_DUPLICATED', true); ?>");
                        	   }
	           	   else
	           	   {
	           		  submitform( pressbutton );
	               	}
	         	   
	            });
	    }
	
	    
	}
	
	function checkValidUser()
	{
		var form = document.adminForm;
		var ob = document.getElementById('invalid_msg');
		 jQuery.post("index.php?option=com_enmasse&tmpl=component&controller=merchant&task=checkUserName&userName="+form.user_name.value,function(data) {
			    if(data == 'invalid')
	  			{
	  				form.user_name.focus();
	  				document.getElementById('duplicated_msg').style.display = "none";
	  			    ob.style.display = "block";
	  			}
			    else if(data == 'duplicated' && form.user_name.value!=form.temp_user_name.value)
			    {
			    	form.user_name.focus();
			    	ob.style.display = "none";
			    	document.getElementById('duplicated_msg').style.display = "block";
	    		}
	  			else
	  			{
	  				ob.style.display = "none";
	  				document.getElementById('duplicated_msg').style.display = "none";
			  	}
	 	   
	    });
	}
	
	
	</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="width-40 fltlft">
	<fieldset class="panelform">
		<legend><?php echo JText::_('MERCHANT_DETAIL');?></legend>
		<table class="admintable" style="width: 100%">
			<tr>
				<td width="100"   class="key"><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_MERCHANT_NAME'),JTEXT::_('TOOLTIP_MERCHANT_NAME_TITLE'), 
		                    '', JTEXT::_('MERCHANT_NAME'));?> *</td>
				<td><input class="text_area" type="text" name="name" id="name"
					size="50" maxlength="250" value="<?php echo htmlentities($row->name, ENT_QUOTES,"UTF-8");?>" />
					<input type="hidden"  name="tempName" value="<?php echo htmlentities($row->name, ENT_QUOTES,"UTF-8");?>" />
					</td>
			</tr>
			<tr>
				<td width="100"   class="key"><?php echo JHTML::tooltip(JTEXT::_('BRANCH_DESC_TOOLTIP'), JTEXT::_('BRANCH_DESC_TOOLTIP_TITLE'),  
		                    '', JTEXT::_('MERCHANT_DESCRIPTION'));?></td>
				<td><textarea name="description" id="description" cols="30" rows="5"><?php echo $row->description; ?></textarea>
				</td>
			</tr>	
			<tr>
				<td width="100"   class="key"><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_MERCHANT_URL'),JTEXT::_('TOOLTIP_MERCHANT_URL_TITLE'), 
		                    '', JTEXT::_('MERCHANT_WEB_URL'));?></td>
				<td><input class="text_area" type="text" name="web_url" id="web_url"
					size="50" maxlength="250" value="<?php echo $row->web_url;?>" /></td>
			</tr>
			<tr>
				<td width="100"   class="key"><?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_MERCHANT_LOGO'),JTEXT::_('TOOLTIP_MERCHANT_LOGO_TITLE'), 
		                    '', JTEXT::_('MERCHANT_LOGO_URL'));?></td>
				<td><input class="text_area" type="text" name="logo_url" id="logo_url"
					size="50" maxlength="250" readonly="readonly" value="<?php echo $row->logo_url;?>" />
		           
		            <a rel="{handler: 'iframe', size: {x: 500, y: 400}}"
					href="<?php echo 'index.php?option=com_enmasse&controller=uploader&task=display&parentId=logo_url&parent=merchant'; ?>"
					class="modal"><?php echo JText::_('MERCHANT_LOGO_URL_LINK');?></a></td>
			</tr>
			<tr>
				<td width="100"   class="key"> 
						<?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_MERCHANT_SALE_PERSON'),JTEXT::_('TOOLTIP_MERCHANT_SALE_PERSON_TITLE'), '', JTEXT::_('MERCHANT_SALE_PERSON')); ?>:
					</td>
				<td><?php
				echo JHTML::_('select.genericList',$salesPersonList, 'sales_person_id', null , 'id', 'name', $row->sales_person_id );
				?></td>
			</tr>
			
		</table>
	</fieldset>
</div>
<div class="width-60 fltlft">
	<fieldset class="panelform " >
		<legend><?php echo JText::_('MERCHANT_STATUS');?></legend>
		<table class="admintable">
			<tr>
				<td width="100"   class="key"><?php echo JText::_('CREATED_AT')?></td>
				<td><?php echo DatetimeWrapper::getDisplayDatetime($row->created_at); ?> </td>
			</tr>
			<tr>
				<td width="100"   class="key"><?php echo JText::_('UPDATED_AT')?></td>
				<td><?php echo DatetimeWrapper::getDisplayDatetime($row->updated_at); ?> </td>
			</tr>
			<tr>
				<td width="100"   class="key"><?php echo JText::_('PUBLISHED')?></td>
				<td><?php echo JHTML::_('select.booleanlist', 'published','class="inputbox"', $row->published ? $row->published : 1); ?></td>
			</tr>
			<tr>
				<td width="100"   class="key"><?php echo JText::_('MERCHANT_CONTACT_STATUS')?></td>
				<td><?php echo JHTML::_('select.genericList', $merStatusList, 'status', null , 'value', 'text', $row->status, false, true); ?> </td>
			</tr>
			<tr>
				<td width="100"   class="key"><?php echo JText::_('MERCHANT_NOTE')?></td>
				<td><textarea rows="4" cols="30" "><?php echo $row->note?></textarea></td>
			</tr>
			<tr>
				<td width="100"   class="key"><?php echo JText::_('MERCHANT_INFOR_MATERIAL_SENT')?></td>
				<td><?php echo JHTML::_('select.booleanlist', 'infor_material_sent','class="inputbox"', $row->infor_material_sent); ?></td>
			</tr>
			<tr>
				<td width="100"   class="key"><?php echo JText::_('MERCHANT_CONTACT_AGAIN')?></td>
				<td>
				<?php echo JHTML::_('calendar',$row->contact_again_at, 'contact_again_at','contact_again_at','%Y-%m-%d' , array('size'=>30, 'readonly'=>'')); ?>
				<span style="width: 10px">&nbsp;</span>
				<input type="checkbox" name="never_contact_again" <?php echo $row->never_contact_again ? "checked=\"checked\" " : "" ;?> value="1" >
				<label><?php echo JText::_('MERCHANT_NEVER_CONTACT_AGAIN')?></label>
				</td>
			</tr>
		</table>
		<div style="margin-top: 10px;">
			<input type="button" class="button" value="<?php echo JText::_("MERCHANT_ADD_NEW_DEAL")?>" onclick="javascript: window.location.href='<?php echo JRoute::_('index.php?option=com_enmasse&controller=deal&task=add&merchant_id=' .$row->id)?>'"/>
		</div>
	</fieldset>
</div>
<div class="width-100 fltlft">
	<fieldset class="panelform"><legend><?php echo JText::_('MERCHANT_USER_DETAIL')?></legend>
		<table class="admintable" style="width: 100%">
			<tr>
				<td width="100"   class="key"><?php echo JHTML::tooltip(JTEXT::_('MERCHANT_USERNAME_TOOLTIP'), JTEXT::_('MERCHANT_USERNAME_TOOLTIP_TITLE'), 
		                    '', JTEXT::_('MERCHANT_USER_NAME'));?></td>
				<td><input style="margin: 0 5px 0 0;" class="text_area" type="text" name="user_name"
					id="user_name" size="50" maxlength="250"
					value="<?php echo $row->user_name;?>" onkeyup="checkValidUser()" />
					<input type="hidden" name="temp_user_name" value="<?php echo $row->user_name;?>" />
					<div id='invalid_msg' style="display: none;color:red;">(<?php echo JText::_('INVALID_USER_NAME_MSG');?>)</div>
					<div id='duplicated_msg' style="display: none;color:red;">(<?php echo JText::_('USER_NAME_ALREADY_ASSIGNED');?>)</div>
					</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="panelform"><legend><?php echo JText::_('MERCHANT_GOOGLE_MAP')?></legend>
		<table class="admintable" style="width: 100%">
			<tr>
				<td width="100"   class="key"><?php echo JHTML::tooltip(JTEXT::_('MAP_WIDTH_TOOLTIP'), JTEXT::_('MAP_WIDTH_TOOLTIP_TITLE'), 
		                    '', JTEXT::_('MERCHANT_MAP_WIDTH'));?></td>
				<td><input class="text_area" type="text" name="google_map_width"
					id="google_map_width" size="15" maxlength="250"
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
				<td><input class="text_area" type="text" name="google_map_height"
					id="google_map_height" size="15" maxlength="250"
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

	<fieldset class="panelform"><legend>Branches</legend>
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
						<label for="name-<?php echo $count; ?>">
						<?php echo JHTML::tooltip(JTEXT::_('BRANCH_NAME_TOOLTIP'), JTEXT::_('BRANCH_NAME_TOOLTIP_TITLE'), 
			                    '', JTEXT::_('MERCHANT_NAME'));?>
			            </label>
			        </td>
					<td><input class="required" type="text" name="name-<?php echo $count; ?>" id="name-<?php echo $count; ?>" size="50" maxlength="250" value="<?php echo $branch['name']; ?>" />
					</td>
				</tr>
				<tr>
					<td width="100"   class="key">
						<label for="description-<?php echo $count; ?>">
						<?php echo JHTML::tooltip(JTEXT::_('BRANCH_DESC_TOOLTIP'), JTEXT::_('BRANCH_DESC_TOOLTIP_TITLE'),  
			                    '', JTEXT::_('MERCHANT_DESCRIPTION'));?>
			        	</label>
			        </td>
					<td><textarea name="description-<?php echo $count; ?>" id="description-<?php echo $count; ?>" cols="30" rows="5"><?php echo $branch['description']; ?></textarea>
					</td>
				</tr>
				<tr>
					<td width="100"   class="key">
						<label for="address-<?php echo $count;?>">
							<?php echo JHTML::tooltip(JTEXT::_('BRANCH_ADD_TOOLTIP'), JTEXT::_('BRANCH_ADD_TOOLTIP_TITLE'), 
				                    '', JTEXT::_('MERCHANT_ADDRESS'));?>
				        </label>
			        </td>
					<td><textarea class="required" name="address-<?php echo $count; ?>" id="address-<?php echo $count; ?>" cols="30" rows="5"><?php echo $branch['address']; ?></textarea>
					</td>
				</tr>
				<tr>
					<td width="100"   class="key">
						<label for="postal_code-<?php echo $count;?>">
							<?php echo JHTML::tooltip(JTEXT::_('BRANCH_POSTAL_CODE_TOOLTIP'), JTEXT::_('BRANCH_POSTAL_TOOLTIP_TITLE'), 
			                    '', JTEXT::_('MERCHANT_POSTAL_CODE'));?>
			            </label>
			        </td>
					<td><input class="required" type="text" name="postal_code-<?php echo $count; ?>" id="postal_code-<?php echo $count; ?>" size="15" maxlength="250" value="<?php echo $branch['postal_code'] ?>" />
					</td>
				</tr>
				<tr>
					<td width="100"   class="key">
						<label for="telephone-<?php echo $count;?>">
							<?php echo JHTML::tooltip(JTEXT::_('BRANCH_TEL_TOOLTIP'), JTEXT::_('BRANCH_TEL_TOOLTIP_TITLE'), 
			                    '', JTEXT::_('MERCHANT_TELEPHONE'));?>
			            </label>
			        </td>
					<td><input class="required" type="text" name="telephone-<?php echo $count; ?>" id="telephone-<?php echo $count; ?>" size="15" maxlength="250" value="<?php echo $branch['telephone'] ?>" />
					</td>
				</tr>
				<tr>
					<td width="100"   class="key">
						<label for="fax-<?php echo $count; ?>">
							<?php echo JHTML::tooltip(JTEXT::_('BRANCH_FAX_TOOLTIP'), JTEXT::_('BRANCH_FAX_TOOLTIP_TITLE'),
			                    '', JTEXT::_('MERCHANT_FAX'));?>
			        	</label>
			        </td>
					<td><input type="text" name="fax-<?php echo $count; ?>" id="fax-<?php echo $count; ?>" size="15" maxlength="250" value="<?php echo $branch['fax'] ?>" />
					</td>
				</tr>
				<tr>
					<td width="100"   class="key">
						<label for="google_map_lat-<?php echo $count;?>">
						<?php echo JHTML::tooltip(JTEXT::_('BRANCH_LAT_TOOLTIP'), JTEXT::_('BRANCH_LAT_TOOLTIP_TITLE'),
				                    '', JTEXT::_('MERCHANT_GOOGLE_LATITUDE'));?>
				        </label>
			        </td>
					<td><input class="text_area required" type="text" name="google_map_lat-<?php echo $count; ?>" id="google_map_lat-<?php echo $count; ?>" size="15" maxlength="250" value="<?php echo $branch['google_map_lat'];?>" /></td>
				</tr>
				<tr>
					<td width="100"   class="key">
						<label for="google_map_long-<?php echo $count;?>">
						<?php echo JHTML::tooltip(JTEXT::_('BRANCH_LONG_TOOLTIP'), JTEXT::_('BRANCH_LONG_TOOLTIP_TITLE'), 
				                    '', JTEXT::_('MERCHANT_GOOGLE_LONGTITUDE'));?>
				        </label>
			        </td>
					<td>
						<input type="hidden" name="branchname-<?php echo $count; ?>" value="branch<?php echo $count; ?>"/>
						<input class="text_area required" type="text" name="google_map_long-<?php echo $count; ?>" id="google_map_long-<?php echo $count; ?>" size="15" maxlength="250" value="<?php echo $branch['google_map_long'];?>" />
					</td>
				</tr>
				<tr>
					<td width="100"   class="key">
						<label for="google_map_zoom-<?php echo $count; ?>">
						<?php echo JHTML::tooltip(JTEXT::_('BRANCH_ZOOM_TOOLTIP'), JTEXT::_('BRANCH_ZOOM_TOOLTIP_TITLE'), 
			                    '', JTEXT::_('MERCHANT_GOOGLE_ZOOM'));?>
			            </label>
			            </td>
				<?php 
					if ($branch['google_map_zoom']=='' || !is_numeric($branch['google_map_zoom']))
					{
						$branch['google_map_zoom'] = 14;
					}
				?>	                    
					<td><input type="hidden" name="branchname-<?php echo $count; ?>" value="branch<?php echo $count; ?>"/><input class="text_area" type="text" name="google_map_zoom-<?php echo $count; ?>" id="google_map_zoom-<?php echo $count; ?>" size="15" maxlength="250" value="<?php echo $branch['google_map_zoom'];?>" /></td>
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
	<input type="hidden" name="controller" value="merchant" />
	<input type="hidden" name="task" value="" />
</div>

</form>
<script>
function addRow()
{
	count = jQuery("input[name='num_of_branches']").val();
	count = parseInt(count) + 1;
	jQuery("input[name='num_of_branches']").val(count);
	jQuery("#branches").append(
        '<tr>\n\
            <td width="100"   class="key">Check to remove</td>\n\
            <td><input type="checkbox" name="remove-' + count + '" id="remove-' + count + '"/></td>\n\
        </tr>\n\
        <tr>\n\
            <td width="100"   class="key">\n\
            <label for="name-' + count + '">\n\
            <?php echo JTEXT::_('MERCHANT_NAME');?></td>\n\
            </label>\n\
            <td><input class="required" type="text" name="name-' + count + '" id="name-' + count + '" size="50" maxlength="250" value="" /></td>\n\
        </tr>\n\
        <tr>\n\
            <td width="100"   class="key">\n\
            <label for="description-' + count + '">\n\
            <?php echo JTEXT::_('MERCHANT_DESCRIPTION');?>\n\
			</label>\n\
            </td>\n\
            <td><textarea name="description-' + count + '" id="description-' + count + '" cols="30" rows="5"></textarea></td>\n\
        </tr>\n\
        <tr>\n\
	        <td width="100"   class="key">\n\
		        <label for="address-' + count + '">\n\
		        <?php echo JTEXT::_('MERCHANT_ADDRESS');?>\n\
				</label>\n\
	        </td>\n\
            <td><textarea class="required" name="address-' + count + '" id="address-' + count + '" cols="30" rows="5"></textarea></td>\n\
        </tr>\n\
        <tr>\n\
        	<td width="100"   class="key">\n\
	        	<label for="postal_code-' + count + '">\n\
	        	<?php echo JTEXT::_('MERCHANT_POSTAL_CODE');?>\n\
				</label>\n\
        	</td>\n\
    		<td><input class="required" type="text" name="postal_code-' + count + '" id="postal_code-' + count + '" size="15" maxlength="250" value="" /></td>\n\
    	</tr>\n\
        <tr>\n\
	        <td width="100"   class="key">\n\
		        <label for="telephone-' + count + '">\n\
		        <?php echo JTEXT::_('MERCHANT_TELEPHONE');?>\n\
				</label>\n\
	        </td>\n\
        	<td><input class="required" type="text" name="telephone-' + count + '" id="telephone-' + count + '" size="15" maxlength="250" value="" /></td>\n\
        </tr>\n\
        <tr>\n\
	        <td width="100"   class="key">\n\
		        <label for="fax-' + count + '">\n\
		        <?php echo JTEXT::_('MERCHANT_FAX');?>\n\
				</label>\n\
        	</td>\n\
            <td><input type="text" name="fax-' + count + '" id="fax-' + count + '" size="15" maxlength="250" value="" /></td>\n\
        </tr>\n\
        <tr>\n\
	        <td width="100"   class="key">\n\
		        <label for="google_map_lat-' + count + '">\n\
		        <?php echo JTEXT::_('MERCHANT_GOOGLE_LATITUDE');?>\n\
				</label>\n\
			</td>\n\
            <td><input class="text_area required" type="text" name="google_map_lat-' + count + '" id="google_map_lat-' + count + '" size="15" maxlength="250" value="" /></td>\n\
        </tr>\n\
        <tr>\n\
        	<td width="100" class="key">\n\
		        <label for="google_map_long-' + count + '">\n\
		        <?php echo JTEXT::_('MERCHANT_GOOGLE_LONGTITUDE');?>\n\
				</label>\n\
			</td>\n\
            <td><input class="text_area required" type="text" name="google_map_long-' + count + '" id="google_map_long-' + count + '" size="15" maxlength="250" value="" /></td>\n\
        </tr>\n\
        <tr>\n\
        	<td width="100" class="key">\n\
        		<label for="goole_map_zoom-' + count + '">\n\
        		<?php echo JTEXT::_('MERCHANT_GOOGLE_ZOOM');?>\n\
				</label>\n\
			</td>\n\
            <td><input type="hidden" name="branchname-' + count + '" value="branch' + count + '"/>\n\
                <input class="text_area" type="text" name="google_map_zoom-' + count + '" id="google_map_zoom-' + count + '" size="15" maxlength="250" value="" />\n\
            </td>\n\
        </tr>\n\
        <tr>\n\
            <td colspan="2\"><hr/></td>\n\
        </tr>'
);
}
</script>