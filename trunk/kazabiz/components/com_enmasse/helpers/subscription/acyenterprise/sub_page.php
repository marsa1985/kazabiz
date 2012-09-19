<?php
/* ------------------------------------------------------------------------
  # En Masse - Social Buying Extension 2010
  # ------------------------------------------------------------------------
  # By Matamko.com
  # Copyright (C) 2010 Matamko.com. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.matamko.com
  # Technical Support:  Visit our forum at www.matamko.com
  ------------------------------------------------------------------------- */
jimport('joomla.application.module.helper');
$oMenu = JFactory::getApplication()->getMenu();
$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=dealtoday',true);
$sRedirectLink = JRoute::_('index.php?option=com_enmasse&controller=deal&task=today&Itemid=' . $oItem->id, false);
?>
<link href="components/com_enmasse/theme/<?php echo EnmasseHelper::getThemeFromSetting(); ?>/css/subscript.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
    function submit_multiform()
    {
        var numberForms = document.forms.length;
        //alert(numberForms);

        var formIndex;
        for (formIndex = 0; formIndex < numberForms; formIndex++)
        {      
            formName = document.forms[formIndex].name.toString(); 
        
            if(formName.substring(0,14) == 'formAcymailing')
            {
                document.forms[formIndex].redirect.value = 'index.php?option=com_enmasse&controller=deal&task=dealSetLocationCookie&locationId='+ document.forms[formIndex]['user[location]'].value;
                try{ return submitacymailingform('optin',formName); }catch(err){alert('The form could not be submitted');return false;}
            }
        }
    }
</script>				
<div id='acymailing-container'>

    <?php
    if (!empty($data->module)) {
        echo JModuleHelper::renderModule($data->module);
    }
    ?>
</div>
<div class="bottom_subscription_page">
    <input style="width:auto;float:right;margin:10px 20px 0px 0px;" type="button" class="cartbutton" value="<?php echo JText::_('SUBMIT_YOUR_LOCATION'); ?>" onclick="submit_multiform();" />

    <input style="width:auto;float:right;margin:10px 10px 0px 0px;" type="button" class="cartbutton" value="<?php echo JText::_('SKIP_THIS_STEP'); ?>" onclick="location.href='<?php echo $sRedirectLink; ?>'" />

</div>

