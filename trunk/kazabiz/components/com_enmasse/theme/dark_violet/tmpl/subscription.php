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
// getting data from view
$data = $this->data;

// getting class for subscription
$integrationClass = EnmasseHelper::getSubscriptionClassFromSetting();
$sub_page = JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."subscription". DS .$integrationClass. DS."sub_page.php";
$oMenu = JFactory::getApplication()->getMenu();
$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=dealtoday',true);
$sRedirectLink = JRoute::_('index.php?option=com_enmasse&controller=deal&task=today&Itemid=' . $oItem->id, false);
if(!$sub_page){
	JFactory::getApplication()->redirect($sRedirectLink,$msg);
	exit;
}

JFactory::getDocument()->addScript('components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js');
JFactory::getDocument()->addScript('components/com_enmasse/theme/js/jquery/ui/jquery.ui.core.js');
JFactory::getDocument()->addScript('components/com_enmasse/theme/js/jquery/ui/jquery.ui.widget.js');
JFactory::getDocument()->addScript('components/com_enmasse/theme/js/jquery/ui/jquery.ui.mouse.js');
JFactory::getDocument()->addScript('components/com_enmasse/theme/js/jquery/ui/jquery.ui.button.js');
JFactory::getDocument()->addScript('components/com_enmasse/theme/js/jquery/ui/jquery.ui.dialog.js');
JFactory::getDocument()->addScript('components/com_enmasse/theme/js/jquery/ui/jquery.ui.position.js');
JFactory::getDocument()->addScript('components/com_enmasse/theme/js/jquery/ui/jquery.ui.draggable.js');
JFactory::getDocument()->addScript('components/com_enmasse/theme/js/jquery/ui/jquery.ui.resizable.js');
JFactory::getDocument()->addScript('components/com_enmasse/theme/js/jquery/ui/jquery.effects.core.js');
 
JFactory::getDocument()->addStyleSheet('components/com_enmasse/theme/js/jquery/themes/' . $theme .'/jquery.ui.all.css');
JFactory::getDocument()->addStyleSheet('components/com_enmasse/theme/' . $theme . '/css/screen.css');
?>

<style>
    body { font-size: 62.5%; }
    label, input { /*display:block;*/ }
    input.text { margin-bottom:12px; width:95%; padding: .4em; }
    fieldset { padding:0; border:0; margin-top:0px; }
    h1 { font-size: 1.2em; margin: .6em 0; }
    div#users-contain { width: 350px; margin: 20px 0; }
    div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .validateTips { border: 1px solid transparent; padding: 0; }
    table.acymailing_form, table.acymailing_form td{ border: none !important;}
    p{margin: 0;}
</style>

<div class="demo">
    <div id="dialog-form" title="<?php echo JText::_('SUBSCRIPTION_PAGE_TITLE');?>">
        <p class="validateTips" style="color:red"></p>
        <fieldset>
                <?php
                    include $sub_page;
                ?>                
        </fieldset>        
    </div>    
</div>
<script type="text/javascript">
jQuery(function() {
    
    // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
    jQuery( "#dialog:ui-dialog" ).dialog( "destroy" );
    
    // modified text fields
    jQuery('#user_name_formAcymailing1').attr('size', 30);
    jQuery("#user_email_formAcymailing1").attr('size', 30)
    
    var name = jQuery("#user_name_formAcymailing1"),
    email = jQuery("#user_email_formAcymailing1"),
    location = jQuery("div#acymailing-container select#locationId"),
    allFields = jQuery( [] ).add( name ).add( email ).add( location ),
    tips = jQuery( ".validateTips" );
    
    function updateTips( t ) {
        tips
        .text( t )
        .addClass( "ui-state-highlight" );
        setTimeout(function() {
            tips.removeClass( "ui-state-highlight", 1500 );
        }, 500 );
    }

    function checkLength( o, n, min, max ) {
    	if ( o.val().length > max || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            updateTips( "Length of " + n + " must be between " +
                min + " and " + max + "." );
            return false;
        } else {
            return true;
        }
    }
    
    function checkRequiredField(o, n, hint){
    	if(o.val()== hint || o.val=='')
    	{
    		 o.addClass( "ui-state-error" );
             updateTips(  n + " field is required.");
             return false;
    	}
    	return true;
    }

    function checkRegexp( o, regexp, n ) {
        if ( !( regexp.test( o.val() ) ) ) {
            o.addClass( "ui-state-error" );
            updateTips( n );
            return false;
        } else {
            return true;
        }
    }
    
    jQuery( "#dialog-form" ).dialog({
        autoOpen: false,
        /*height: 300,*/
        width: 350,
        modal: true,
        resizable: false,
        buttons: {
            "<?php echo JText::_('SUBMIT_YOUR_LOCATION')?>": function() {
                var bValid = true;
                allFields.removeClass( "ui-state-error" );
                
                bValid = bValid && checkRequiredField (name, 'name', acymailing['NAMECAPTION']);//notice acymailing['NAMECAPTION'] was define in javascript of acymailing module
                bValid = bValid && checkLength( name, "name", 3, 16 );
                bValid = bValid && checkLength( email, "email", 6, 80 );
                
                //bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );
                // From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
                bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "Invalid email address" );
                //bValid = bValid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );
                
                // getting selectedIndex of the Selection and check validate
                bValid = bValid && checkRequiredField (location, 'location', '');
                //bValid = bValid && checkLength( location, "location", 1, 10 );                    
                if ( bValid ) {
                    submit_multiform(location.val(), email.val());                
                }
            },
            "<?php echo JText::_('SUBSCRIPTION_CANCEL')?>": function() {
                jQuery( this ).dialog( "close" );
                window.location.href = "<?php echo $sRedirectLink; ?>";
            }
        },
        close: function() {
            allFields.val( "" ).removeClass( "ui-state-error" );
            window.location.href = "<?php echo $sRedirectLink; ?>";
        }
    });    
});

function on_change(selectobj){
    var hidSel = selectobj.options[selectobj.selectedIndex].value;
    jQuery('#hidSel').val(hidSel);
    return false;        
}

function submit_multiform(nlocationID, sEmail)
{
    var numberForms = document.forms.length;                                               
    var formIndex;
    for (formIndex = 0; formIndex < numberForms; formIndex++)
    {      
        formName = document.forms[formIndex].name.toString(); 
        if(formName.substring(0,14) == 'formAcymailing')
        {
            document.forms[formIndex].redirect.value = 'index.php?option=com_enmasse&controller=deal&task=dealSetLocationCookie&locationId='+ nlocationID + '&email=' + sEmail;
            try{
                return submitacymailingform('optin',formName);
            }catch(err){
                alert('The form could not be submitted');
                return false;
            }
        }
    }
}
</script>
<script>
    jQuery(document).ready(function(){
        jQuery( "#dialog-form" ).dialog( "open");
    });
</script>