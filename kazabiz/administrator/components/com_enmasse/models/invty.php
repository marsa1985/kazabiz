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


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
jimport( 'joomla.application.component.model' );

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
						order_item_id = ".$orderItemId;
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
            
			$created_at = DatetimeWrapper::getDatetimeOfNow();
			$query = "INSERT INTO #__enmasse_invty (name, order_item_id, pdt_id, status,created_at) VALUES ('".$name."',".$orderItemId.",'".$pdtId."','".$status."','".$created_at."')";
			$db->setQuery( $query );
			$db->query();
			
			if ($this->_db->getErrorNum()) {
				JError::raiseError( 500, $this->_db->stderr() );
				return false;
			}
		}
		return true;
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
    function getCouponFreeByPdtID($id)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT *
		          FROM #__enmasse_invty
		          WHERE pdt_id = '.$id.' AND status= "Free" AND deallocated_at < '. strtotime(DatetimeWrapper::getDatetimeOfNow());
		$db->setQuery( $query );
		return $db->loadObjectList();
		
	}
	function getSoldCouponByPdtId($id)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT *
		          FROM #__enmasse_invty
		          WHERE pdt_id = '.$id.' AND ( status= "Sold" OR status="Taken") ';
		$db->setQuery( $query );
		$coupons = $db->loadObjectList();
		return $coupons;
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
		                  pdt_id ='.$pdt_id.' AND status="'.$status.'"'; 
		$db->setQuery($query);
		$db->query();
		return true;
	}
	
	/**
	 * 
	 * Update merchant settlement status for list coupon with id in array $arId
	 * @param $arId array
	 * @return boolean true on success
	 */
	public function payOutCoupons($arId)
	{
		$db = JFactory::getDBO();
		$query = "	UPDATE 
						#__enmasse_invty 
					SET settlement_status = " . $db->quote(EnmasseHelper::$MERCHANT_SETTLEMENT_STATUS_LIST['Paid_Out'])."
	                WHERE id IN (" .implode(", ", $arId) . ") ";
		$db->setQuery( $query );
		$invty = $db->query();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}
		return true;
	}
	
	/**
	 * 
	 * Roll merchant settlement status for list coupon with id in array $arId back to Not Paid Out status
	 * @param $arId array
	 * @return boolean true on success
	 */
	public function doNotPayOutCoupons($arId)
	{
		$db = JFactory::getDBO();
		$query = "	UPDATE 
						#__enmasse_invty 
					SET settlement_status = " . $db->quote(EnmasseHelper::$MERCHANT_SETTLEMENT_STATUS_LIST['Not_Paid_Out'])."
	                WHERE id IN (" .implode(", ", $arId) . ") ";
		$db->setQuery( $query );
		$invty = $db->query();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}
		return true;
	}
	
	/**
	 * 
	 * Get coupon ids with given status
	 * @param mixed $status string or array, if it is array, we using OR operator for query condition
	 * @param array $arFilter filter condition
	 * @return array id of coupons
	 */
	public function getBySettlementStatus($status, $arFilter)
	{
		$db = JFactory::getDBO();
		$stCon = '';
		if(is_array($status))
		{
			foreach ($status as $item)
			{
				$stCon .= "  settlement_status = " .$db->quote($item) ." OR";
			}
			$stCon = substr($stCon,0, -2);//trim 'OR' at end of string
		}else 
		{
			$stCon = " settlement_status = " .$db->quote($status);
		}		
		
		$query = "SELECT id
					FROM #__enmasse_invty
					WHERE " . $stCon;
		
		if(!empty($arFilter['deal_id']))
		{
			$query .= " AND pdt_id = " .$arFilter['deal_id'];
		}
		
		$db->setQuery($query);
		
		$arIds = $db->loadResultArray();
		
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}
		return $arIds;
	}
    
    function freeCouponByOrderItemId($orderItemId)
    {
        $db = JFactory::getDBO();
        $query = "	UPDATE 
                        #__enmasse_invty 
                    SET status = 'Free', deallocated_at = '0000-00-00 00:00:00'
                    WHERE order_item_id = $orderItemId";
        $db->setQuery( $query );
        $invty = $db->query();
        if ($this->_db->getErrorNum()) {
            JError::raiseError( 500, $this->_db->stderr() );
            return false;
        }
        return true;
    }    
}
?>