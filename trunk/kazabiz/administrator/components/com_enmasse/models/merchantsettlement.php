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
jimport( 'joomla.application.component.model' );

class EnmasseModelMerchantSettlement extends JModel
{
 	protected $_total = null;
	protected $_pagination = null;
	
	public function __construct()
	{
		parent::__construct();

		global $mainframe, $option;

		$version = new JVersion;
		$joomla = $version->getShortVersion();
		if(substr($joomla,0,3) >= 1.6){
			$mainframe = JFactory::getApplication();
		}
		// define values need to pagination
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		// define value need for sort
		$filter_order     = $mainframe->getUserStateFromRequest(  'com_enmasse.filter_order', 'filter_order', 'name', 'cmd' );
		$filter_order_dir = $mainframe->getUserStateFromRequest(  'com_enmasse.filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_dir', $filter_order_dir);
		 
	}
	  
	public function countAll()
	{
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__enmasse_merchant_deal_settlement";
		$db->setQuery($query);
		$count = $db->loadResult();
		return $count;
	}

	 
	public function getTotal()
	{
		if (empty($this->_total)) {
			$this->_total = $this->_getListCount($this->_getListQuery());
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
     
	 
	public function search()
	{
		$rows = $this->_getList($this->_getListQuery(), $this->getState('limitstart'), $this->getState('limit'));
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return $rows;
	}
	
	public function store($data)
	{
		//TODO if need save the data
	}
	
	public function getReportData($arCouponId)
	{
		global $option;
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$filter = $app->getUserStateFromRequest( $option.'filter', 'filter', '', 'array' );
				
		$query =   "SELECT oi.*,
					 o.description AS order_description,
					 o.buyer_detail AS order_buyer_detail,
					 o.delivery_detail AS order_delivery_detail,
					 o.point_used_to_pay AS order_point_used_to_pay,
					 i.status AS coupon_status,
					 i.name AS coupon_serial,
					 i.settlement_status AS coupon_settlement_status,
					 d.deal_code AS deal_code
					FROM #__enmasse_order_item oi
					INNER JOIN #__enmasse_invty i ON oi.id = i.order_item_id
					INNER JOIN #__enmasse_order o ON oi.order_id = o.id
					INNER JOIN #__enmasse_deal d ON oi.pdt_id = d.id
					WHERE i.id IN (" .implode(', ', $arCouponId) .")";
		
		if($filter != null)
		{
			//adding list filter condition
			if(isset($filter['deal_id']) && $filter['deal_id']!="")
	   			$query .= " AND d.id = " .intval($filter['deal_id']);
	   		if(isset($filter['merchant_id']) && $filter['merchant_id']!="")
	   			$query .= " AND d.merchant_id = " .intval($filter['merchant_id']);
	   	}
		  			
		//adding order by condition
		$filter_order     = $this->getState('filter_order');
	    $filter_order_dir = $this->getState('filter_order_dir');
		if(!empty($filter_order))
	    {
	       	if ($filter_order == 'oi.created_at')
	        	$query .= ' ORDER BY '.$filter_order.' '.$filter_order_dir;
	    }
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * 
	 * Contruct the query for get list record,
	 * include filter and order condition.
	 * @return string SQL query. 
	 */
	private function _getListQuery()
	{
		global $option;
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$filter = $app->getUserStateFromRequest( $option.'filter', 'filter', '', 'array' );
				
		$query =   "SELECT oi.*,
					 o.description AS order_description,
					 o.buyer_detail AS order_buyer_detail,
					 o.delivery_detail AS order_delivery_detail,
					 o.point_used_to_pay AS order_point_used_to_pay,
					 i.id AS coupon_id,
					 i.status AS coupon_status,
					 i.name AS coupon_serial,
					 i.settlement_status AS coupon_settlement_status,
					 d.deal_code AS deal_code
					FROM #__enmasse_order_item oi
					INNER JOIN #__enmasse_invty i ON oi.id = i.order_item_id
					INNER JOIN #__enmasse_order o ON oi.order_id = o.id
					INNER JOIN #__enmasse_deal d ON oi.pdt_id = d.id
					WHERE d.status = " . $db->quote(EnmasseHelper::$DEAL_STATUS_LIST['Confirmed']).
					"AND (oi.status = " .$db->quote(EnmasseHelper::$ORDER_ITEM_STATUS_LIST['Paid']) .
					"OR oi.status = " .$db->quote(EnmasseHelper::$ORDER_ITEM_STATUS_LIST['Delivered']) .
					")";
		
		if($filter != null)
		{
			//adding list filter condition
			if(isset($filter['deal_id']) && $filter['deal_id']!="")
	   			$query .= " AND d.id = " .intval($filter['deal_id']);
	   		if(isset($filter['merchant_id']) && $filter['merchant_id']!="")
	   			$query .= " AND d.merchant_id = " .intval($filter['merchant_id']);
	   		if(isset($filter['status']) && trim($filter['status'])!="")
	   			$query .= " AND i.settlement_status = " .$db->quote(($filter['status']));
	   		
		}
		  			
		//adding order by condition
		$filter_order     = $this->getState('filter_order');
	    $filter_order_dir = $this->getState('filter_order_dir');
		if(!empty($filter_order))
	    {
	       	if ($filter_order == 'oi.created_at')
	        	$query .= ' ORDER BY '.$filter_order.' '.$filter_order_dir;
	    }
	    //echo $query; die;
	    return $query;
	    
	}
}
?>