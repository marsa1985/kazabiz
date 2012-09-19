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

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php"); 
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
class EnmasseViewDealToday extends JView
{
    const CS_SESSION_LOCATIONID = 'CS_SESSION_LOCATIONID';

    function display($tpl = null)
    {
      	$nLocId = JRequest::getInt(self::CS_SESSION_LOCATIONID, null, 'COOKIE');

      	if($nLocId)
      	{
      		$deal = JModel::getInstance('deal', 'enmasseModel')->getDealMaxSoldQtyFromLocation($nLocId);
      		if(empty($deal->id))
      		{
      			$msg = JText::_("NO_DEAL_ON_YOUR_LOCATION");
      			JFactory::getApplication()->enqueueMessage($msg);
      		}
      	}
      	if(!$nLocId || empty($deal->id))
      	{      		
      		$deal = JModel::getInstance('deal','enmasseModel')->todayDeal();
      	}
        //Referral ID
        $referralId = JRequest::getVar('referralid');
        $this->assignRef( 'referralId', $referralId );		
		 
    	if(empty($deal))
		{
			//redirect to upcomming deal page because there havent deal on today
			$link = JRoute::_("index.php?option=com_enmasse&controller=deal&task=upcoming", false);
			$msg = JText::_('NO_DEAL_TODAY');

			JFactory::getApplication()->redirect($link, $msg);
		}
		
        $deal->merchant = JModel::getInstance('merchant','enmasseModel')->getById($deal->merchant_id);
        $deal->merchant->branches = json_decode($deal->merchant->branches, true);

        $this->assignRef( 'deal', $deal );

        $this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
    	$this->_layout="deal_today";
        parent::display($tpl);
    }

}
?>