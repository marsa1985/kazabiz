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

$theme =  EnmasseHelper::getThemeFromSetting();//getThemeFromSetting();
JFactory::getDocument()->addScript("components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js");
JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict()');

// load language pack
$language = JFactory::getLanguage();
$base_dir = JPATH_SITE.DS.'components'.DS.'com_enmasse';
$version = new JVersion;
$joomla = $version->getShortVersion();
if(substr($joomla,0,3) >= '1.6'){
    $extension = 'com_enmasse16';
}else{
    $extension = 'com_enmasse';
}
if($language->load($extension, $base_dir, $language->getTag(), true) == false)
{
	 $language->load($extension, $base_dir, 'en-GB', true);
}
if(!EnmasseHelper::is_urlEncoded($item->pic_dir))
 {
 	$imageUrl = $item->pic_dir;
 }
 else
 {
	$imageUrlArr= unserialize(urldecode($item->pic_dir));
	$imageUrl = str_replace("\\","/",$imageUrlArr[0]);
 }

?>
<div id="SideDealModule<?php echo $css_suffix;?>" class="fl" align="center" style="<?php echo 'width:'.$width.'px ; height:'.$height.'px';?>">
	<div class="timeupdate"><?php echo JHTML::_('date', $item->end_at, JText::_('DATE_FORMAT_LC1'));?></div>
    <div class="SideDealModule_information"><a href="index.php?option=com_enmasse&controller=deal&task=view&id=<?php echo $item->id ."&slug_name=" .$item->slug_name;?>&sideDealFlag=1"> <?php echo JTEXT::_($item->name); ?></a></div>
    <a href="index.php?option=com_enmasse&controller=deal&task=view&id=<?php echo $item->id ."&slug_name=" .$item->slug_name;?>&sideDealFlag=1">
    <img src="<?php echo $imageUrl; ?>" alt="" height="90" width="100"/></a>
    <div class="bought"><?php echo $item->cur_sold_qty;?> <?php echo JText::_('DEAL_BOUGHT');?></div>
</div>

