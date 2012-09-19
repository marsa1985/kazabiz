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

class enmasseViewsubscriptpage extends JView
{
    function display($tpl = null)
    {
    	//redirect user to dealtoday page if use already subscription
    	if(JRequest::getVar('CS_SESSION_LOCATIONID', '', 'COOKIE'))
    	{
    		JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_enmasse&controller=deal&task=today'));
    	}
    	// get pagameters
    	$app	= JFactory::getApplication();
    	$params = $app->getParams();
    	$parameters = new JObject();
    	$parameters->module_id = $params->get('subscribe_module_id');
    	$parameters->params = $params;
    	//------------------------
		//gemerate integration class
		 $integrateFileName = EnmasseHelper::getSubscriptionClassFromSetting().'.class.php';
		 $integrationClass = EnmasseHelper::getSubscriptionClassFromSetting();
		 require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."subscription". DS .$integrationClass. DS.$integrateFileName);
		 $integrationObject = new $integrationClass();
		 
		 // assign data which get from integration class to view
		 $data = $integrationObject->getViewData($parameters);
		 $data->module->user = 0;
    	 $this->assignRef( 'data', $data);
     	 $this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
    	 $this->_layout="subscription";
         parent::display($tpl);
    }

}
?>