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
//$integrationClass = acystarter;
$sub_page = JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."subscription". DS .$integrationClass. DS."sub_page.php";
$oMenu = JFactory::getApplication()->getMenu();
$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=dealtoday',true);
$sRedirectLink = JRoute::_('index.php?option=com_enmasse&controller=deal&task=today&Itemid=' . $oItem->id, false);
//echo $_COOKIE['CS_SESSION_LOCATIONID'];exit();
if(!$sub_page){
	JFactory::getApplication()->redirect($sRedirectLink,$msg);
	exit;
}
JFactory::getDocument()->addStyleSheet('components/com_enmasse/theme/js/jquery/themes/' . $theme .'/jquery.ui.all.css');
JFactory::getDocument()->addScript("components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js");
JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict()');

$app = JFactory::getApplication();
$app->setUserState('staticTitle', JText::_('SUBSCRIPTION'));
?>

<style>
    body { font-size: 62.5%; }
    label, input { display:block; }
    input.text { }
    h1 { font-size: 1.2em; margin: .6em 0; }
    div#users-contain { width: 350px; margin: 20px 0; }
    div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .validateTips { border: 1px solid transparent; padding: 0.3em; }
    table.acymailing_form, table.acymailing_form td{ border: none !important;}
    
    .inputbox {
    	border:1px solid #CCCCCC;
    	width:200px;
    	height:25px;
    }
    
    .bottom_subscription_page{
    	float:none;
    	clear:both;
    }
    
    .bottom_subscription_page input{
    	margin:0px;
    	padding:0px;
    }
    
    .cartbutton{
    	display:block;
    }
    
    form td {
    	height: 30px;
	}
	
	fieldset{
		 width:170px;
		 margin:auto;
	}
</style>

    <div id="dialog-form" title="<?php echo JText::_('SUBSCRIPTION_PAGE_TITLE');?>">
        <p class="validateTips" style="color:red"></p>
        <fieldset>
                <?php
                    include $sub_page;
                ?>                
        </fieldset>        
    </div>    

<script type="text/javascript">
jQuery(function() {
    // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
    // modified text fields
    jQuery('#user_name_formAcymailing1').attr('size', 50);
    jQuery("#user_email_formAcymailing1").attr('size', 50);
/*
    if(jQuery('#locationId').length || (!jQuery('#user_email_formAcymailing1').length)){
    	jQuery('#locationId').change(function(){
			if(jQuery('#locationId').val()=='') return;
			window.location.href='index.php?option=com_enmasse&controller=deal&task=dealSetLocationCookie&locationId='+ jQuery('#locationId').val();
        });
    }
  */  
    //if(jQuery('#locationId').length==0 || jQuery('#user_email_formAcymailing1').length==0) jQuery('#bottom_sub').remove();
    jQuery('#btnNext, #btnSkip').css({'padding-left':'20px'});
    jQuery('.bottom_subscription_page input').css({'margin-right':'2px'});
	jQuery('.cartbutton').css({'float':'none', 'margin-left':'3px','width':'169px'}).addClass('button_big');

	
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
             updateTips( "Field " + n + " is required ");
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
    
    
});

function on_change(selectobj){
    var hidSel = selectobj.options[selectobj.selectedIndex].value;
    jQuery('#hidSel').val(hidSel);
    return false;        
}

function submit_multiform(nlocationID, sEmail)
{	
	if(jQuery('#locationId').length){

		if(!jQuery('#user_email_formAcymailing1').length){//emlocation
			if( document.getElementById('locationId').value==''){
				alert('Please choose one location');
				return;
			}else{
				window.location.href='index.php?option=com_enmasse&controller=deal&task=dealSetLocationCookie&locationId='+ document.getElementById('locationId').value+ '&email=' + sEmail;
			}
		}
		
		if(document.getElementById('locationId').value=='' || (eval(jQuery('#hidSel').val())==0)){// stater
			alert('Please choose one location');
			return;
		}else{
			if(nlocationID==undefined) nlocationID = jQuery('#hidSel').val();
		}
	}

	if(sEmail==undefined) sEmail = jQuery('#user_email_formAcymailing1').val();

	

	//if(eval(jQuery('#hidSel').val())==0) 
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

<?php 
	if($integrationClass == 'acyenterprise'){
?>

<?php }elseif($integrationClass == 'emlocation'){?>
	

<?php } elseif($integrationClass == 'acystarter'){?>
	<div class="bottom_subscription_page" id="bottom_sub">
	<div style="width:200px; margin:auto">
	<div style="margin-left:12px">
    	<input type="button" onclick="submit_multiform();" value="<?php echo JText::_('SUBMIT_YOUR_LOCATION')?>" class="cartbutton button_big" style="width: 169px; float: none; margin: 10px 2px 0px 3px;">
    	<input type="button" onclick="location.href='index.php?option=com_enmasse&amp;controller=deal&amp;task=today&amp;Itemid=101'" value="<?php echo JText::_('SUBSCRIPTION_CANCEL')?>" class="cartbutton button_big" style="width: 169px; float: none; margin: 10px 2px 0px 3px;">
	</div>
	</div>
</div>
<?php } ?>