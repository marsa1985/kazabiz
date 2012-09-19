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

// create list location for combobox
$locationJOptList = array();
$emptyJOpt = JHTML::_('select.option', '', JText::_('SUBSCR_CHOOSE_ONCE_LOCATION'));
array_push($locationJOptList, $emptyJOpt);

foreach ($data->locationList as $item) {
    $var = JHTML::_('select.option', $item->id, $item->name);
    array_push($locationJOptList, $var);
}
?>
<link href="components/com_enmasse/theme/<?php echo EnmasseHelper::getThemeFromSetting(); ?>/css/subscript.css" rel="stylesheet" type="text/css" />
<style>
    #acymailing-container table.acymailing_form td.acyfield_name acyfield_email{
        margin-left: 0px;
    }
    #locationId {margin-left:3px}
</style>
<div id="acymailing-container">
    <div class="acymodule">
        <?php
        if (!empty($data->module)) {
            echo JModuleHelper::renderModule($data->module);
        }
        ?>    
    </div>    
    <div >
    	<?php echo JHTML::_('select.genericList', $locationJOptList, 'locationId', 'onchange="on_change(this);"', 'value', 'text', ''); ?>
        <input type="hidden" name="hidSel" id="hidSel" value="0"/>        
    </div>
</div>