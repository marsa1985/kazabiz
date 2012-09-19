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
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");
jimport( 'joomla.application.component.model' );

class EnmasseModelDeal extends JModel
{
	function todayDeal()
	{
		// deal
		$db = JFactory::getDBO();
		$query = "	SELECT
						* 
					FROM 
						#__enmasse_deal 
					WHERE
	              		published = '1' AND 
	              		status != '".EnmasseHelper::$DEAL_STATUS_LIST['Pending']."' AND 
	              		status != '".EnmasseHelper::$DEAL_STATUS_LIST['Voided']."' AND  
	              		end_at >= '". DatetimeWrapper::getDatetimeOfNow() . "' AND 
	              		start_at <='".DatetimeWrapper::getDatetimeOfNow()."'
	              	ORDER BY
	              		position ASC
	              	LIMIT
	              		1
	              ";
		$db->setQuery( $query );
		$deal = $db->loadObject();
		
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}
		return $deal;
	}

	function viewDeal($id)
	{
        $oDeal = JTable::getInstance('deal', 'Table');
        $oDeal->load($id);
		return $oDeal;
	}

	function searchStartedPublishedDeal($keyword=null, $categoryId=null, $locationId=null, $sortBy=null)
	{
		$db = JFactory::getDBO();
		// generate the query
		$query = "	SELECT 
						d.* 
					FROM 
						#__enmasse_deal d
					WHERE
					    d.status NOT LIKE 'Pending' AND
		          		published = '1' AND
						d.start_at <='".DatetimeWrapper::getDatetimeOfNow()."' 
		          		AND d.end_at >= '".DatetimeWrapper::getDatetimeOfNow()."' ";
		if($keyword != null)
		{
			$keyword = EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($keyword));
			$query .= "	AND d.name like '%$keyword%'";
		}
		if($categoryId)
			$query .=" AND d.id IN (SELECT cat.deal_id FROM #__enmasse_deal_category cat WHERE cat.category_id = $categoryId ) ";
	 	
		if($locationId)
			$query .=" AND d.id IN (SELECT loc.deal_id FROM #__enmasse_deal_location loc WHERE loc.location_id = $locationId ) ";
		 
		if($sortBy != null)
	 	{
	 		$query .= "	ORDER BY $sortBy";
	 	}	
		else
		{
			$query .= "	ORDER BY d.position ASC";
		}
		
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}
		
		return $rows;
	}
	function getDealMaxSoldQtyFromLocation($locationId)
	{
		$dtNow = DatetimeWrapper::getDatetimeOfNow();
		$db = JFactory::getDBO();
		$query = "SELECT 
						d.*
					FROM 
						#__enmasse_deal d
					INNER JOIN #__enmasse_deal_location l ON d.id = l.deal_id 
					WHERE
		          		d.published = '1' AND
		          		d.status != '" .EnmasseHelper::$DEAL_STATUS_LIST['Pending']."' AND 
		          		d.status != '" .EnmasseHelper::$DEAL_STATUS_LIST['Voided']."' AND 
						d.start_at <='".$dtNow."' 
		          		AND d.end_at >= '".$dtNow."' 
		          		AND l.location_id = $locationId
                ORDER BY d.cur_sold_qty DESC
                LIMIT 1";	 
		$db->setQuery( $query );
		return  $db->loadObject();
	}
	
    function searchExpiredPublishedDeal($keyword=null, $categoryId=null, $locationId=null, $sortBy=null)
	{
		$db = JFactory::getDBO();
						
	    // generate the query
		$query = "	SELECT 
						d.* 
					FROM 
						#__enmasse_deal d
					WHERE
						d.status NOT LIKE 'Pending' AND
		          		d.published = '1' AND
						d.end_at <= '".DatetimeWrapper::getDatetimeOfNow()."'";
		if($keyword != null)
		{
			$keyword = EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($keyword));
	 		$query .= "	AND d.name like '%$keyword%'";
		}
		if($categoryId)
			$query .=" AND d.id IN (SELECT cat.deal_id FROM #__enmasse_deal_category cat WHERE cat.category_id = $categoryId ) ";
	 	
		if($locationId)
			$query .=" AND d.id IN (SELECT loc.deal_id FROM #__enmasse_deal_location loc WHERE loc.location_id = $locationId ) ";
		  
		if($sortBy != null)
			$query .= "	ORDER BY $sortBy";
			
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		
		if ($db->getErrorNum())
		{
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}
		
		return $rows;                
	}

	function upcomingDeal()
	{
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__enmasse_deal WHERE
		          published = '1'";
		$query.= " AND start_at > '".DatetimeWrapper::getDatetimeOfNow()."'";


		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return $rows;
	}

	function getById($id, $authorId = null)
	{
		static $instance = array();//cache deals
		if(empty($instance[$id]))
		{
			$db = JFactory::getDBO();
			$query = "	SELECT
							* 
						FROM 
							#__enmasse_deal 
						WHERE
							id = $id " ;
			if($authorId)
			{
				$query .=" AND sales_person_id = $authorId";
			}
			
			$db->setQuery( $query );
			
			$instance[$id] = $db->loadObject();
			
			if ($db->getErrorNum())
    		{
				JError::raiseError( 500, $db->getErrorMsg() );
				return false;
			}
			
		}
		return $instance[$id];
	}

	function listConfirmedByMerchantId($merchantId)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT
						*
					FROM 
						#__enmasse_deal 
					WHERE
						status = 'Confirmed' AND 
						merchant_id = $merchantId";

		$db->setQuery( $query );
		$dealList = $db->loadObjectList();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}
		return $dealList;
	}

	function searchBySalesPerson($salesPersonId, $keyword, $published, $status, $sCode = null)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT
						*
					FROM 
						#__enmasse_deal 
					WHERE
						sales_person_id = $salesPersonId";
		if($keyword != null)
		{
			$keyword = EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($keyword));
	 		$query .= "	AND name like '%$keyword%'";
		}
		if($published != null)
	 		$query .= "	AND published = $published";
		if($status != null)
	 		$query .= "	AND status like '$status'";

		if($sCode != null)
		{
			$sCode = EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($sCode));
	 		$query .= "	AND deal_code like '%$sCode%'";
		}
		
		$query .= " ORDER BY created_at DESC";
		$db->setQuery( $query );
		$dealList = $db->loadObjectList();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}
		
		return $dealList;
	}
//Kayla add for Sale Report function	
	function searchBySaleReports($salesPersonId, $keyword, $merchantId, $fromdate, $todate, $sCode = null)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT
						*
					FROM 
						#__enmasse_deal as d, #__enmasse_order_item as oi 
					WHERE
						d.name = oi.description
						AND oi.status LIKE 'Delivered'
						AND d.sales_person_id = $salesPersonId";
	
		if($keyword != null)
		{
			$keyword = EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($keyword));
	 		$query .= "	AND d.name like '%$keyword%'";
		}
		
		if($merchantId != null)
	 		$query .= "	AND d.merchant_id = $merchantId";

 		$fromdate = ($fromdate != null)? $fromdate : date('Y-m-d');
 		$todate = ($todate != null)? $todate : date('Y-m-d');
 		
 		$query .= "	AND oi.updated_at BETWEEN '$fromdate' AND '$todate'";
		//$query .= "	AND oi.updated_at BETWEEN '2012-02-01' AND '2012-02-10'";
 	
		if($sCode != null)
		{
			$sCode = EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($sCode));
	 		$query .= "	AND d.deal_code like '%$sCode%'";
		}
		
		//$query .= " ORDER BY created_at DESC";
		$query .= " GROUP BY d.deal_code";
		
		$db->setQuery( $query );
		$dealList = $db->loadObjectList();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}
		
		return $dealList;
	}
		
	function addQtySold($id, $qty)
	{
		$db = JFactory::getDBO();
		
		$query = "	UPDATE 
						#__enmasse_deal 
					SET 
						cur_sold_qty = cur_sold_qty + $qty,
						updated_at = '".DatetimeWrapper::getDatetimeOfNow()."'
	                WHERE 
	                	id = $id";
		$db->setQuery( $query );
		$db->query();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	function store($data)
	{
		$row = $this->getTable();

		if (!$row->bind($data)) {
			$this->setError($row->getErrors());
			return false;
		}

		if ( !$row->position )
		{
			$db = JFactory::getDBO();
			$query = "SELECT COUNT(*) FROM #__enmasse_deal";
			$db->setQuery( $query );
			$nNumRows = $db->loadResult();
			if ($db->getErrorNum())
			{
				$this->setError($db->getErrorMsg());
				return false;
			}
			$row->position = $nNumRows + 1;
		}

		if($row->id <= 0)
		{
			// in case add new deal, set created_at and deal_code attribute
			$row->created_at = DatetimeWrapper::getDatetimeOfNow();
			$row->deal_code = $this->getNewDealCode();
		}
		
		$row->updated_at = DatetimeWrapper::getDatetimeOfNow();
				

		if (!$row->check()) {
			$row->success = false;
			$this->setError($row->getErrors());
			return $row;
		}

		if (!$row->store()) {
			$row->success = false;
			$this->setError($row->getErrors());
			return $row;
		}
		$row->success = true;
		return $row;
	}
	/**
	 * 
	 * Check whether the deal (that confirmed) belongs to the merchant or not
	 * @param integer $nMerId
	 * @param integer $nDealId
	 * @return boolean 
	 */
	
	public function checkMerchantOfDeal($nMerId, $nDealId)
	{
		$db 	= JFactory::getDBO();
		$query  = "SELECT COUNT(*)
					FROM #__enmasse_deal
		           	WHERE  id = $nDealId AND merchant_id = $nMerId".
					"  AND status=" .$db->quote(EnmasseHelper::$DEAL_STATUS_LIST['Confirmed']);
		$db->setQuery($query);
		return $db->loadResult();
	}
	
    function getDealByName($name)
	{
		$db 	= JFactory::getDBO();
		$query = ' SELECT 
		              * 
		           FROM 
		              #__enmasse_deal
		           WHERE 
		            name = '.$db->getEscaped($name);
		$db->setQuery($query);
		$deal = $db->loadObject();
		return $deal;
	}
	
	private function getNewDealCode()
	{
		$text = "DE" .date('ym', time());
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(id)
		      FROM #__enmasse_deal
		      WHERE deal_code LIKE '$text%'";              
		$db->setQuery($query);
		$num = $db->loadResult();
		$str = (string)($num + 1);
		if (strlen($str) < 5) {
			$str = str_repeat('0', 5 - strlen($str)).$str;
		}
		return $text.'-'.$str;
	}
    
	function updateStatus($id,$value)
	{
		$db = JFactory::getDBO();
		$query = 'UPDATE #__enmasse_deal SET status ="'.$value.'", updated_at = "'. DatetimeWrapper::getDatetimeOfNow() .'" where id ='.$id;
		$db->setQuery( $query );
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $db-> query();
	}    
	
}
