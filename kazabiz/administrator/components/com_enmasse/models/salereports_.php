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
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");
jimport( 'joomla.application.component.model' );

class EnmasseModelSaleReports extends JModel
{
      var $_total = null;
	  var $_pagination = null;
	  function __construct()
	  {
	        parent::__construct();
	 
	        global $mainframe, $option;
            
            $version = new JVersion;
            $joomla = $version->getShortVersion();
            if(substr($joomla,0,3) >= '1.6'){
        	   $mainframe = JFactory::getApplication();
            }
	 
	        // define values need to pagination
	        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
	        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
	        $this->setState('limit', $limit);
	        $this->setState('limitstart', $limitstart);
	        
	        // define value need for sort
	        $filter_order     = $mainframe->getUserStateFromRequest(  $option.'filter_order', 'filter_order', 'position', 'cmd' );
	        $filter_order_dir = $mainframe->getUserStateFromRequest( $option.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
	        $this->setState('filter_order', $filter_order);
	        $this->setState('filter_order_dir', $filter_order_dir);
	        
	  }
	  function _buildContentOrderBy() // to generate order query content
	  {
	 
	                $orderby = '';
	                $filter_order     = $this->getState('filter_order');
	                $filter_order_dir = $this->getState('filter_order_dir');
	 
	                if(!empty($filter_order) && !empty($filter_order_dir) )
	                {
	                	if ($filter_order == 'cur_sold_qty'||$filter_order == 'position' || $filter_order == 'status'|| $filter_order == 'pdt_cat_id')
	                	{
	                		$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_dir. ' ,position ASC';
	                	}else
	                	{
	                		$orderby = ' ORDER BY position ASC';
	                	}
	                }
	 
	                return $orderby;
	   }

       function countAll()
       {
			$db = JFactory::getDBO();
			$query = "SELECT COUNT(*) FROM #__enmasse_deal";
			$db->setQuery($query);
			$count = $db->loadResult();
			return $count;
       }
                
	   function getTotal()
	   {
	   	 global $mainframe, $option;
	   	 $name = '';
	   	 $listDealIdFromLoc = null;
	   	 $listDealIdFromCategory = null;
	   	 $published = '';
	   	 $status    = '';
	   	 
	   	 $filter = $mainframe->getUserStateFromRequest( $option.'filter', 'filter', '', 'array' );
	   	 if($filter != null && count($filter)!=0)
	   	 {
	   	 	 if (isset($filter['name']) && $filter['name']!= null)
		   	 	$name = $filter['name'];
		   	 if (isset($filter['location_id']) && $filter['location_id']!= null)
		   	 	$listDealIdFromLoc = JModel::getInstance('dealLocation','enmasseModel')->getDealByLocationId($filter['location_id']);
		   	 if (isset($filter['category_id']) && $filter['category_id']!= null)
		   	 	$listDealIdFromCategory = JModel::getInstance('dealCategory','enmasseModel')->getDealByCategoryId($filter['category_id']);
		   	 if (isset($filter['published']) && $filter['published']!= null)
		   	 	$published = $filter['published'];
		   	 if (isset($filter['status']) && $filter['status']!= null)
		   	 	$status = $filter['status'];
	   	 }
	   	 //-----------------------------------------------------------	
	   	 // to create string of deal id base on category and location
	   	 	$ids = '';
			if(count($listDealIdFromLoc)!=0 && count($listDealIdFromCategory)!=0)
			{
				for($i=0; $i < count($listDealIdFromLoc); $i++)
				{
					for($x=0; $x< count($listDealIdFromCategory); $x++)
					{
						if($listDealIdFromLoc[$i] == $listDealIdFromCategory[$x])
						 $idArr[] = $listDealIdFromLoc[$i];
					}
				}
				
				if(count($idArr)!=0)
				 $ids .=implode(",",$idArr);
				else
				  $ids.='0';
			}
			else if ( count($listDealIdFromLoc)!=0)
				$ids .=implode(",",$listDealIdFromLoc);
			else if( count($listDealIdFromCategory)!=0)
				$ids .=implode(",",$listDealIdFromCategory);
			
		//-----------------------------------------------------------	
	   	// generate the query
	   	$query = "	SELECT 
						* 
					FROM 
						#__enmasse_deal 
					WHERE 
						1=1";
		if ( !empty($name) )
	    {
			$name = EnmasseHelper::escapeSqlLikeSpecialChar(JFactory::getDbo()->getEscaped($name));
			$query .= " and name like '%$name%'";
		}

		if($published != null)
	 		$query .= "	AND published = $published ";
	 		
		if ( !empty($status) && trim($status) != '' )
			$query .= " and status = '$status'";
	    if($ids!='')
		  $query .=" AND id IN (".$ids.") ";
		  
		  
	        // Load total records
	        if (empty($this->_total)) {
	            $query .= $this->_buildContentOrderBy();
	            $this->_total = $this->_getListCount($query);    
	        }
	        return $this->_total;
	  }
	  
	  
	 function getPagination()
	  {
	        //create pagination
	        if (empty($this->_pagination)) {
	            jimport('joomla.html.pagination');
	            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
	        }
	        return $this->_pagination;
	  }
	  
//Kayla add to Search on Reports
	function search($code, $name, $sale, $mer, $fromdate, $todate)
	{
				
		//------------------------------------------------------------
		// generate Query
		$db = JFactory::getDBO();
		$query = "	SELECT
						*
					FROM 
						#__enmasse_deal as d, #__enmasse_order_item as oi 
					WHERE
						d.name = oi.description
						AND oi.status LIKE 'Delivered'
				";
        if ( !empty($code) )
		{
			$code = EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($code));
			$query .= " and d.deal_code like '%$code%'";
		}
		if ( !empty($name) )
		{
			$name = EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($name));
			$query .= " and d.name like '%$name%'";
		}
		if ( !empty($sale) )
		{
			$query .= " and d.sales_person_id = $sale";
		}

		if ( !empty($mer) )
		{
			$query .= " and d.merchant_id = $mer";
		}		
		
 		$fromdate = ($fromdate != null)? $fromdate : date('Y-m-d');
 		$todate = ($todate != null)? $todate : date('Y-m-d');
 		
 		$query .= "	AND oi.updated_at BETWEEN '$fromdate' AND '$todate'";

 		$query .= " GROUP BY d.deal_code";
		
		$rows = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $rows;
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

	function getStatus($id)
	{
		$db = JFactory::getDBO();
		$query = '	SELECT
							status 
						FROM 
							#__enmasse_deal 
						WHERE 
							id = '.$id;
		$db->setQuery($query);
		$status = $db -> loadResult();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $status;
	}

	function getById($id)
	{
		$row = JTable::getInstance('deal', 'Table');
		$row->load($id);
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $row;
	}
	
	function listConfirmed()
	{
		$db = JFactory::getDBO();
		$query = "	SELECT 
						* 
					FROM 
						#__enmasse_deal 
		            WHERE
		            	status= 'Confirmed'
		            ORDER BY created_at DESC
		              ";
		$db->setQuery( $query );
		$deals = $db->loadObjectList();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $deals;
	}
    function listDeal()
	{
		$db = JFactory::getDBO();
		$query = "	SELECT 
						* 
					FROM 
						#__enmasse_deal 
		            ORDER BY updated_at DESC
		              ";
		$db->setQuery( $query );
		$deals = $db->loadObjectList();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $deals;
	}
	
	function store($data)
	{
		$row = $this->getTable();
		$data->name = trim($data->name);
		if (!$row->bind($data)) {
			$row->success = false;
			$this->setError($this->_db->getErrorMsg());
			return $row;
		}

		if ( ! $row->position )
		{
			$db = JFactory::getDBO();
			$query = "SELECT * FROM #__enmasse_deal";
			$db->setQuery( $query );
			$rows = $db->loadObjectList();
			if ($db->getErrorNum()) {
				echo $db->stderr();
				return false;
			}
			$row->position = count($rows) + 1;
		}

		if($row->id <= 0) {
			$row->created_at = DatetimeWrapper::getDatetimeOfNow();
            $row->deal_code = $this->getNewDealCode();
        }
        
		$row ->updated_at = DatetimeWrapper::getDatetimeOfNow();
		
		if (!$row->check()) {
			$row->success = false;
			$this->setError($this->_db->getErrorMsg());
			return $row;
		}

		if (!$row->store()) {
			$row->success = false;
			echo $this->setError($this->_db->getErrorMsg());
			return $row;
		}
        $row->success = true;
		return $row;
	}


	function deleteList($cids)
	{
		$row = $this->getTable();
		
		foreach($cids as $cid) {
			//Remove the links between deal and categories, locations
			$db = JFactory::getDBO();
			$query = "DELETE FROM #__enmasse_deal_location WHERE deal_id=$cid";
			$db->setQuery($query);
			$db->query();				
			$query = "DELETE FROM #__enmasse_deal_category WHERE deal_id=$cid";
			$db->setQuery($query);
			$db->query();				
			if (!$row->delete( $cid )) {				
				$this->setError( $row->getErrorMsg() );
				return false;
			}
		}

		return true;
	}

	//////////// to refresh order of deal
	function refreshOrder($by=null)
	{
		if(empty($by))
		$by = 'id';
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__enmasse_deal order by $by ASC";
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		$count = 0;
		foreach ($rows as $data)
		{
			$row = JTable::getInstance('deal', 'Table');
			if (!$row->bind($data))
			{
				echo $row->getError();
				echo "<script> alert('".$row->getError()."');
	            window.history.go(-1); </script>\n";
				exit();
			}
			$row->position= ++$count;
			if (!$row->store())
			{
				echo $row->getError();
				echo "<script> alert('".$row->getError()."');
	            window.history.go(-1); </script>\n";
				exit();
			}
		}
	}

	function getCurrentPosition($id)
	{
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__enmasse_deal where id=$id";
		$db->setQuery( $query );
		$cur = $db->loadObject();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		return $cur;
	}

	function getNextPosition($temp)
	{
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__enmasse_deal where position=$temp";
		$db->setQuery( $query );
		$other = $db->loadObject();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		return $other;
	}
	
	function getPortalId($id)
	{
		$db = JFactory::getDBO();
		$query = "SELECT portal_id FROM #__enmasse_deal where id=$id";
		$db->setQuery( $query );
		$pid = $db->loadResult();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		return $pid;
	}
	
    function getListPosition()
	{
		$db = JFactory::getDBO();
		$query = "SELECT position FROM #__enmasse_deal ";
		$db->setQuery( $query );
		$list = $db->loadResultArray();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		return $list;
	}
	
	function getOtherPosition($id)
	{
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__enmasse_deal where id!=$id";
		$db->setQuery( $query );
		$other = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		return $other;
		
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
	                	id = '$id'";
		$db->setQuery( $query );
		$db->query();
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return true;
	}
	
   function reduceQtySold($id, $qty)
	{
		$db = JFactory::getDBO();
		
		$query = "	UPDATE 
						#__enmasse_deal 
					SET 
						cur_sold_qty = cur_sold_qty - $qty,
						updated_at = '".DatetimeWrapper::getDatetimeOfNow()."'
	                WHERE 
	                	id = '$id'";
		$db->setQuery( $query );
		$db->query();
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return true;
	}
	
   function getDealBySaleId($saleId)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT 
						* 
					FROM 
						#__enmasse_deal 
		            WHERE sales_person_id = $saleId  
		            AND status != 'Voided' AND end_at <= '".DatetimeWrapper::getDatetimeOfNow()."'";
		$db->setQuery( $query );
		$deal = $db->loadObjectList('id');
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $deal;
	}
	
  function getDealByMerchantId($merchantId)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT 
						* 
					FROM 
						#__enmasse_deal 
		            where merchant_id = $merchantId
		            AND status != 'Voided' AND end_at <= '".DatetimeWrapper::getDatetimeOfNow()."'";
		$db->setQuery( $query );
		$deals = $db->loadObjectList('id');
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $deals;
	}
	
	public function getDealByName($name)
	{
		$db 	= JFactory::getDBO();
		$query = ' SELECT 
		              * 
		           FROM 
		              #__enmasse_deal
		           WHERE 
		            name = "'.$name.'"';
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
}
?>