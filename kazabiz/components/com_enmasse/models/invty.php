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

class EnmasseModelInvty extends JModel
{
	function listByOrderItemId($orderItemId)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT
						* 
					FROM 
						#__enmasse_invty 
					WHERE
	              		order_item_id = $orderItemId";
		$db->setQuery( $query );
		$invtyList = $db->loadObjectList();

		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $invtyList;
	}
		
	function getByName($name)
	{
		$db = JFactory::getDBO();
		$name = $db->getEscaped($name);
		$query = "	SELECT
						* 
					FROM 
						#__enmasse_invty 
					WHERE
	              		name = '$name'";
		$db->setQuery( $query );
		$invty = $db->loadObject();

		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $invty;
	}
	
	function updateStatusByName($name,$status)
	{
		$db = JFactory::getDBO();
		$query = "	UPDATE 
						#__enmasse_invty 
					SET status = '$status'
	                WHERE name = '$name'";
		$db->setQuery( $query );
		$invty = $db->query();
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return true;
	}
	
	function generateForOrderItem($pdtId, $orderItemId, $qty, $status)
	{
		$db = JFactory::getDBO();
		for ( $j = 0; $j < $qty; $j++)
		{
			$name =$pdtId;
			$name.="-";
			$name.=$orderItemId;
			$name.="-";
			$name.= $j+1;
            // get random number
			$length = 5;
            $characters = '0123456789';
            $string = '';    
            for ($p = 0; $p < $length; $p++) {
                 $string .= $characters[mt_rand(0, strlen($characters))];
            }
            $name.="-".$string;            
            
			$query = "INSERT INTO #__enmasse_invty (name, order_item_id, pdt_id, status, created_at) VALUES ('".$name."',".$orderItemId.",'".$pdtId."','".$status."','".DatetimeWrapper::getDatetimeOfNow()."')";
			$db->setQuery( $query );
			$db->query();
			
			if ($this->_db->getErrorNum()) {
				JError::raiseError( 500, $this->_db->stderr() );
				return false;
			}
		}
		return true;
	}
	
	function getCouponFreeByPdtID($id)
	{
		$db = JFactory::getDBO();
		$query = "SELECT *
		          FROM #__enmasse_invty
		          WHERE pdt_id = ".$id." AND status= 'Free' AND deallocated_at < '". DatetimeWrapper::getDatetimeOfNow()."'";
		$db->setQuery( $query );
		return $db->loadObjectList();
		
	}
	function generateCouponFreeStatus($pdtId,$couponName,$status)
	{
		$db = JFactory::getDBO();
		$created_at = DatetimeWrapper::getDatetimeOfNow();
		$query = "INSERT INTO #__enmasse_invty (name, pdt_id, status,created_at) VALUES ('".$couponName."','".$pdtId."','".$status."','".$created_at."')";
		$db->setQuery( $query );
		$db->query();
	    if ($this->_db->getErrorNum()) {
				JError::raiseError( 500, $this->_db->stderr() );
				return false;
			}
		return true;
	}
    
	function removeById($id)
	{
		$db = JFactory::getDBO();
		$query = 'DELETE FROM 
		                  #__enmasse_invty
		          WHERE
		                  id ='.$id ; 
		$db->setQuery($query);
		$db->query();
		return true;
		
	}
	function removeCouponByPdtIdAndStatus($pdt_id,$status)
	{
		$db = JFactory::getDBO();
		$query = 'DELETE FROM 
		                  #__enmasse_invty
		          WHERE
		                  pdt_id ='.$pdt_id.' AND status='.$status; 
		$db->setQuery($query);
		$db->query();
		return true;
	}
	
	function updateStatusByOrderItemId($orderItemId,$status)
	{
		$db = JFactory::getDBO();
		$query = "	UPDATE 
						#__enmasse_invty 
					SET status = '$status'
	                WHERE order_item_id = $orderItemId";
		$db->setQuery( $query );
		$invty = $db->query();
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return true;
	}
	function updateStatusByPdtIdAndStatus($pdt_id,$updateStatus,$oldStatus)
	{
		$db = JFactory::getDBO();
		$query = "	UPDATE 
						#__enmasse_invty 
					SET status = '$updateStatus'
	                WHERE pdt_id = $pdt_id AND status = '$oldStatus'";
		$db->setQuery( $query );
		$invty = $db->query();
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return true;
	}
	
	/**
	 * 
	 * Update merchant settlement status for list coupon with id in array $arId
	 * set settlement_status to "Should_Be_Paid_Out"
	 * @param array $arId
	 * @return boolean true on success
	 */
	public function payOutCoupons($arId)
	{
		$db = JFactory::getDBO();
		$query = "	UPDATE 
						#__enmasse_invty 
					SET settlement_status = " . $db->quote(EnmasseHelper::$MERCHANT_SETTLEMENT_STATUS_LIST['Should_Be_Paid_Out'])."
	                WHERE id IN (" .implode(", ", $arId) . ") ";
		$db->setQuery( $query );
		$db->query();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}
		return true;
	}
	
	/**
	 * 
	 * Get list coupon id with settlement status is Not_Paid_Out by deal id
	 * @param integer $nDealId
	 * @return array list coupon
	 */
	public function getNotPaidOutCouponByDealId($nDealId)
	{
		$db = JFactory::getDBO();
		$query = " SELECT i.*
					FROM #__enmasse_invty i
					LEFT JOIN #__enmasse_order_item oi ON i.order_item_id = oi.id
					WHERE i.pdt_id = $nDealId" .
					" AND i.status = " .$db->quote(EnmasseHelper::$INVTY_STATUS_LIST['Used']) .
					" AND(oi.status = " .$db->quote(EnmasseHelper::$ORDER_ITEM_STATUS_LIST['Paid']) .
					" OR oi.status = " .$db->quote(EnmasseHelper::$ORDER_ITEM_STATUS_LIST['Delivered']) .
					") AND i.settlement_status = " .$db->quote(EnmasseHelper::$MERCHANT_SETTLEMENT_STATUS_LIST['Not_Paid_Out']);
					
		
		$db->setQuery( $query );
		return $db->loadObjectList('id');
		
	}
}
?>