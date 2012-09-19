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
// create list location for combobox
JFactory::getDocument()->addScript("components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js");
JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict()');

$locationJOptList = array();
$emptyJOpt = JHTML::_('select.option', '', JText::_('') );
array_push($locationJOptList, $emptyJOpt);
foreach ($this->locationList as $item)
{
	$var = JHTML::_('select.option', $item->id, JText::_($item->name) );
	array_push($locationJOptList, $var);
}
?>
<div >
<div id='choose_location_title' ><?php echo JText::_('CHOOSE_YOUR_LOCATION');?></div>
<form action='index.php' name="submitLocation" >
 <input type="hidden" name="option" value="com_enmasse" />
 <input type="hidden" name='controller' value="deal" />
 <input type="hidden" name="task" value="dealSetLocationCookie" />
 <div id='location_choose_list'>
 <?php echo JHTML::_('select.genericList', $locationJOptList, 'locationId', null , 'value', 'text', '');?>
 </div>
 <div class="bottom_locationlist">
 	<div class="green_button" style="float:right;">
      <div class="leftbutton"></div>
      <a class="centerbutton simplemodal-close" ><?php echo JText::_('SKIP_THIS_STEP');?></a>
      <div class="rightbutton"></div>
    </div>
    <div style="width: 10px;float: right" >&nbsp;&nbsp;</div>
    <div class="green_button" style="float:right;">
      <div class="leftbutton"></div>
      <a class="centerbutton" onclick="document.submitLocation.submit();"><?php echo JText::_('SUBMIT_YOUR_LOCATION');?></a>
      <div class="rightbutton"></div>
    </div>
     
    </div>
</form>
</div>