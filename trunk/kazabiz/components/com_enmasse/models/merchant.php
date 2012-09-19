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

class EnmasseModelMerchant extends JModel
{
	function getById($merchantId)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT 
						* 
					FROM 
						#__enmasse_merchant_branch
					WHERE
						id = $merchantId";
		$db->setQuery( $query );
		$merchant = $db->loadObject();

		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $merchant;
	}
	
	function getByUserName($username)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT 
						* 
					FROM 
						#__enmasse_merchant_branch 
					WHERE 
						user_name = '$username'
					AND published != 0";
		$db->setQuery( $query );
		$merchant = $db->loadObject();

		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $merchant;
	}
	
	function listAllPublished()
	{
		$db 	= JFactory::getDBO();
		$query = ' SELECT id, name FROM #__enmasse_merchant_branch WHERE published = 1';
		$db->setQuery($query);
		$category = $db->loadObjectList();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $category;
	}
	
	public function getTotal()
	{
		if (empty($this->_total))
		{
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
	
	/**
	 * 
	 * Store merchant data into the DB 
	 * @param array $data
	 * @return boolean true on success
	 */
	public function store($data)
	{
		$oRow = JTable::getInstance('merchant', 'Table');
		if(!$oRow->bind($data))
		{
			$this->setError($oRow->getError());
			return false ;
		}
		if(empty($oRow->id))
			$oRow->created_at = DatetimeWrapper::getDatetimeOfNow();
		
		$oRow->updated_at = DatetimeWrapper::getDatetimeOfNow();
		
		if(!$oRow->check())
		{
			$this->setError($oRow->getError());
			return false ;
		}
						
		if(!$oRow->store())
		{
			$this->setError($oRow->getError());
			return false ;
		}
		
		//set group for the merchant person
		$jUserId = EnmasseHelper::getUserByName($data['user_name'])->id;
		$nGroup = JModel::getInstance('setting', 'EnmasseModel')->getSetting(1)->merchant_group;
		$user = new JUser($jUserId);
		$user->groups = array_merge($user->groups, array($nGroup));
		$user->save();
		return true;
		
	}
	
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$nSaleId = JFactory::getSession()->get('salesPersonId', 0);
		$this->setState('sales_person.id', $nSaleId);
		
		// define values need to pagination
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		 
		// define value need for sort
		$filter_order     = $app->getUserStateFromRequest(  $this->option.'filter_order', 'filter_order', 'name', 'cmd' );
		$filter_order_dir = $app->getUserStateFromRequest( $this->option.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_dir', $filter_order_dir);
	}

	/**
	 * 
	 * Contruct the query for get list record by sale person id,
	 * include filter and order condition.
	 * @return string SQL query. 
	 */
	private function _getListQuery()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$filter = JRequest::getVar('filter', '','', 'array' );
		$query = "SELECT 
						* 
					FROM 
						#__enmasse_merchant_branch
					WHERE  sales_person_id = " .$this->getState('sales_person.id');
		
		if($filter != null)
		{
			//adding list filter condition
			if(isset($filter['id']) && $filter['id']!="")
				$query .= " AND id = " .intval($filter['id']);
				
			if(isset($filter['name']) && $filter['name']!="")
	   			$query .= " AND name LIKE '%" .EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($filter['name'])) ."%' ";
	   		
	   		if(isset($filter['published']) && $filter['published']!="")
	   			$query .= " AND published = " .$filter['published'];
		}
		
		//adding order by condition
		$filter_order     = $this->getState('filter_order');
	    $filter_order_dir = $this->getState('filter_order_dir');
		if(!empty($filter_order))
	    {
	       	if ($filter_order == 'name' || $filter_order == 'user_name'|| $filter_order == 'created_at' )
	        	$query .= ' ORDER BY '.$filter_order.' '.$filter_order_dir;
	    }
	    
	    return $query;
	    
	}
	
	public function checkUserNameDup($username, $existId)
	{
		if(trim($username)=="")
			return null;
			
		$db 	= JFactory::getDBO();
		$query 	= " SELECT 
						* 
					FROM 
						#__enmasse_merchant_branch
					WHERE 
						user_name='$username'";
		if($existId != null)
			$query 	.= "AND id != " . $existId;
			
		$db->setQuery($query);
		$dupList = $db->loadObjectList();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		if(count($dupList)>0)
			return $dupList[0];
		else
			return null;
	}
	
	public function getMerchantByName($merchantName)
	{
		$db 	= JFactory::getDBO();
		$query = ' SELECT 
		              * 
		           FROM 
		              #__enmasse_merchant_branch
		           WHERE 
		            name = "'.$merchantName.'"';
		$db->setQuery($query);
		$merchant = $db->loadObject();
		return $merchant;
	}
	
	function retrieveName($id)
	{
		$db 	= JFactory::getDBO();
		$query = ' SELECT name FROM #__enmasse_merchant_branch WHERE id='.$id;
		$db->setQuery($query);
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $db->loadResult();
			
	}	
}
?>