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
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."DatetimeWrapper.class.php");
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");

class EnmasseModelOrder extends JModel
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
	        
	        
	  }
	 
	  
	   function getTotal()
	   {
	     global $mainframe, $option;
	     $status = '';
		 $dealName = '';
		 $dealCode = '';
		 $bPartial = false;
	   	 $filter = $mainframe->getUserStateFromRequest( $option.'filter', 'filter', '', 'array' );
	   	 $db = JFactory::getDBO();
	   	 if($filter != null)
	   	 {
	   	 	if (isset($filter['status']) && $filter['status']!=null)
		   		 $status = $filter['status'];
		   	if (isset($filter['deal_name']) && $filter['deal_name']!=null )
		   		 $dealName = $filter['deal_name'];
		   	if (isset($filter['deal_code']) && $filter['deal_code']!=null )
		   		 $dealCode = $filter['deal_code'];
		   	if (isset($filter['partial']) && $filter['partial']!=null )
		   		 $bPartial = $filter['partial'];
	   	 }
	     // Load total records
	     if (empty($this->_total)) 
	     {
	     		$query = "	SELECT 
						o.*,
						oi.description as deal_name,
						oi.qty as qty,
                        d.deal_code as deal_code
					FROM 
						#__enmasse_order AS o,
						#__enmasse_order_item AS oi,
                        #__enmasse_deal AS d
					WHERE 
						o.id = oi.order_id AND oi.pdt_id = d.id";
				if(!empty($status))
					$query .="	AND o.status = '$status'";

		        if(!empty($dealCode))
				{
					$dealCode = EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($dealCode));
					$query .=" AND d.deal_code like '%$dealCode%'";
				}
	     		if(!empty($dealName))
				{
					$dealName = EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($dealName));
					$query .=" AND d.name like '%$dealName%'";
				}
				if($bPartial)
				{
					$query .=" AND d.prepay_percent < 100.0 ";
				}
				if(!empty($filter['year']))
				{
					$query .="	AND YEAR(o.created_at) = " .intval($filter['year']);
				}

				if(!empty($filter['month']))
				{
					$query .="	AND MONTH(o.created_at) = " .intval($filter['month']);
				}
	     		$this->_total = $this->_getListCount($query);
	     }
	     
	  	return $this->_total;
	  }
	  
	  public function getPagination()
	  {
	        //create pagination
	        if (empty($this->_pagination)) {
	            jimport('joomla.html.pagination');
	            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
	        }
	        return $this->_pagination;
	  }
	
	
	function search($status = null, $dealCode = null,$dealName = null, $orderBy=null, $orderType=null, $bPartial = false)
	{
		global $option;
        $app = JFactory::getApplication();
        $filter = $app->getUserStateFromRequest( $option.'filter', 'filter', array(), 'array' );      
		$db = JFactory::getDBO();
		$query = "	SELECT 
						o.*,
						oi.description as deal_name,
						oi.qty as qty,
                        d.deal_code as deal_code
					FROM 
						#__enmasse_order AS o,
						#__enmasse_order_item AS oi,
                        #__enmasse_deal AS d
					WHERE 
						o.id = oi.order_id AND oi.pdt_id = d.id";
		if(!empty($status))
			$query .="	AND o.status = '$status'";

        if(!empty($dealCode))
		{
			$dealCode = EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($dealCode));
			$query .=" AND d.deal_code like '%$dealCode%'";
		}
		if(!empty($dealName))
		{
			$dealName = EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($dealName));
			$query .=" AND d.name like '%$dealName%'";
		}
		if($bPartial)
		{
			$query .=" AND d.prepay_percent < 100.0 ";
		}
		
		if(!empty($filter['year']))
		{
			$query .="	AND YEAR(o.created_at) = " .intval($filter['year']);
		}
		
		if(!empty($filter['month']))
		{
			$query .="	AND MONTH(o.created_at) = " .intval($filter['month']);
		}
                
		if(!empty($orderBy))
			$query .=" ORDER BY $orderBy ";
		if(!empty($orderBy) && !empty($orderType))
			$query .="	$orderType ";		

		$db->setQuery( $query );
		
		$rows = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		// Assign total records for pagination
		$this->_total = $this->_getListCount($query);
		
		return $rows;
	}

	function getById($id)
	{
		$order_row = JTable::getInstance('order', 'Table');
		$order_row->load($id);
		return $order_row;
	}

	function updateStatus($id,$value)
	{
		$db = JFactory::getDBO();
		$query = 'UPDATE #__enmasse_order SET status ="'.$value.'", updated_at = "'. DatetimeWrapper::getDatetimeOfNow() .'" where id ='.$id;
		$db->setQuery( $query );
		$db->query();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return true;
	}
	
	public function updateToFullPaid($id)
	{
		$db = JFactory::getDBO();
		$query = 'UPDATE #__enmasse_order SET paid_amount = total_buyer_paid, updated_at = "'. DatetimeWrapper::getDatetimeOfNow() .'" where id ='.$id;
		$db->setQuery( $query );
		$db->query();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return true;
	}
	
	function store($data)
	{
		$row = JTable::getInstance('order', 'Table');
		if (!$row->bind($data))
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if($row->id <= 0)
			$row ->created_at = DatetimeWrapper::getDatetimeOfNow();
		$row ->updated_at = DatetimeWrapper::getDatetimeOfNow();

		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
	
	public function getListIdUndelivered($arId)
	{
		$db = JFactory::getDbo();
		$query = "SELECT id
					FROM #__enmasse_order
					WHERE status != 'Delivered'
					AND status != 'Holding_By_Deliverer'
					AND id IN (" .implode(',', $arId) . ")";
		
		$db->setQuery($query);
		
		return $db->loadColumn();
	}
}
?>