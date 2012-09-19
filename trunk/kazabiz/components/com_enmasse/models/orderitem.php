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

jimport( 'joomla.application.component.model' );
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");

class EnmasseModelOrderItem extends JModel
{
	function getById($orderItemId)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT
						* 
					FROM 
						#__enmasse_order_item 
					WHERE
	              		id = $orderItemId
	              ";
		$db->setQuery( $query );
		$orderItem = $db->loadObject();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $orderItem;
	}
	
	function listByPdtIdAndStatus($pdtId, $status=null)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT
						* 
					FROM 
						#__enmasse_order_item 
					WHERE";
		if($status!=null)						
			$query .= "	status = '".$status."' AND";
		$query .= "		pdt_id = $pdtId";
		$db->setQuery( $query );
		$orderItemList = $db->loadObjectList();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return $orderItemList;
	}
	
	function listByOrderId($orderId)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT
						* 
					FROM 
						#__enmasse_order_item 
					WHERE
	              		order_id = $orderId
	              ";
		$db->setQuery( $query );
		$orderItemList = $db->loadObjectList();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $orderItemList;
	}
	
	function updateStatus($id,$value)
	{
		$db = JFactory::getDBO();
		$query = 'UPDATE #__enmasse_order_item SET status ="'.$value.'", updated_at = "'. DatetimeWrapper::getDatetimeOfNow() .'" where id ='.$id;
		$db->setQuery( $query );
		$db-> query();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return true;
	}
	
	
}
?>