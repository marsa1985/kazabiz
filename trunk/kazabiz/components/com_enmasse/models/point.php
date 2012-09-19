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
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");
jimport( 'joomla.application.component.model' );

class EnmasseModelPoint extends JModel
{
	function doRefund($userId, $orderId, $point)
	{
		$db = JFactory::getDBO();
		$query = "UPDATE #__enmasse_order SET refunded_amount = '".$point."' WHERE id = '".$orderId."' AND refunded_amount = '0'";
		$db->setQuery( $query );
		$db-> query();		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		//------------------------
		//generate integration class			
		$isPointSystemEnabled = EnmasseHelper::isPointSystemEnabled();
		if($isPointSystemEnabled==true)
		{
			$integrationClass = EnmasseHelper::getPointSystemClassFromSetting();
			$integrateFileName = $integrationClass.'.class.php';	
			require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."pointsystem". DS .$integrationClass. DS.$integrateFileName);
			$integrationObject = new $integrationClass();		
			$integrationObject->integration($userId,'refunddeal',$point);
			return true;		
		}
		return false;		
	}	
}
