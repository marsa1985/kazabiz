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
jimport( 'joomla.html.pagination' );
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");

class EnmasseModelSalesPerson extends JModel
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
	        $filter_order     = $mainframe->getUserStateFromRequest(  $option.'filter_order', 'filter_order', 'name', 'cmd' );
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
	                	if ($filter_order == 'name'||$filter_order == 'created_at' || $filter_order == 'user_name')
	                        $orderby = ' ORDER BY '.$filter_order.' '.$filter_order_dir;
	                }
	 
	                return $orderby;
	   }

            function countAll()
            {
                    $db =& JFactory::getDBO();
                    $query = "SELECT COUNT(*) FROM #__enmasse_sales_person";
                    $db->setQuery($query);
                    $count = $db->loadResult();
                    return $count;
            }
                
	   function getTotal()
	   {
	        // Load total records
	        if (empty($this->_total)) {
	            $query = "SELECT * FROM #__enmasse_sales_person".$this->_buildContentOrderBy();
	            $this->_total = $this->_getListCount($query);    
	        }
	        return $this->_total;
	  }
	   function getPagination($name)
	  {
	        //create pagination
	        if (empty($this->_pagination)) {
	            jimport('joomla.html.pagination');
	            //$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
	            $this->_pagination = new JPagination(count($this->getSearchTotal($name)), $this->getState('limitstart'), $this->getState('limit') );
	        }
	        return $this->_pagination;
	  }
	function search($name)
	{
		$name = str_replace("%","!%",addslashes($name));
		$db =& JFactory::getDBO();
		$query = "	SELECT 
						* 
					FROM 
						#__enmasse_sales_person";
		if (!empty($name))
		{
			$name = EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($name));
			$query .= " where name like '%$name%' ";
		}
			
		$query.=$this->_buildContentOrderBy();
		$db->setQuery( $query );
		$rows = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $rows;
	}
	
	function getSearchTotal($name)
	{
		$name = str_replace("%","!%",addslashes($name));
		$db =& JFactory::getDBO();
		$query = "	SELECT 
						* 
					FROM 
						#__enmasse_sales_person";
		if (!empty($name))
		{
			$name = EnmasseHelper::escapeSqlLikeSpecialChar($db->getEscaped($name));
			$query .= " where name like '%$name%' ";
		}
			
		$query.=$this->_buildContentOrderBy();
		$db->setQuery( $query );
		$rows = $this->_getList($query);
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $rows;
	}
	
	function listAllPublished()
	{
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__enmasse_sales_person
		           WHERE published = 1";
		$db->setQuery( $query );
		$objList = $db->loadObjectList();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return $objList;
	}
	
	function listAllPublishedExcept($arrayId)
	{
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__enmasse_sales_person
		           WHERE published = 1
		           AND id NOT IN (" . implode(",", $arrayId) . ")";
		$db->setQuery( $query );
		$objList = $db->loadObjectList();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return $objList;
	}
	

	function getById($id)
	{
		$row = JTable::getInstance('salesPerson', 'Table');
		$row->load($id);
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $row;
	}
	
	function retrieveName($id)
	{
		$db 	= JFactory::getDBO();
		$query 	= ' SELECT name as text FROM #__enmasse_sales_person WHERE id = '.$id;
		$db->setQuery($query);
		$name = $db->loadResult();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return $name;
	}
	
	function store($data)
	{
		$row = $this->getTable();
		$data['name'] = trim($data['name']);
		if (!$row->bind($data)) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		if($row->id <= 0)
			$row->created_at = DatetimeWrapper::getDatetimeOfNow();
		$row->updated_at = DatetimeWrapper::getDatetimeOfNow();
		
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		//set group for the sale person
		$jUserId = EnmasseHelper::getUserByName($data['user_name'])->id;
		$nGroup = JModel::getInstance('setting', 'EnmasseModel')->getSetting(1)->sale_group;
		$user = new JUser($jUserId);
		$user->groups = array_merge($user->groups, array($nGroup));
		$user->save();
		return true;
	}
	function checkUserNameDup($username, $existId=null)
	{
		if(trim($username)=="")
			return null;
		
		$db 	= JFactory::getDBO();
		$query 	= "	SELECT 
						* 
					FROM 
						#__enmasse_sales_person 
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
	
	function checkNameDup($name, $existId=null)
	{
		if(trim($name)=="")
			return null;
		
		$db 	= JFactory::getDBO();
		$query 	= "	SELECT 
						* 
					FROM 
						#__enmasse_sales_person 
					WHERE 
						name='$name'";
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

	function deleteList($cids)
	{
		$row = $this->getTable();
		$nGroup = JModel::getInstance('setting', 'EnmasseModel')->getSetting(1)->sale_group;

		foreach($cids as $cid)
		{
			$row->load($cid);
			if (!$row->delete()) {
				$this->setError( $row->getErrorMsg() );
				return false;
			}
			//remove sale group for this user from joomla user group table
			$jUserId = EnmasseHelper::getUserByName($row->user_name)->id;
			$user = new JUser($jUserId);
			$user->groups = array_diff($user->groups, array($nGroup));
			$user->save();
		}
		return true;
	}
    function getSaleByName($name)
	{
		$db 	= JFactory::getDBO();
		$query = ' SELECT 
		              * 
		           FROM 
		              #__enmasse_sales_person
		           WHERE 
		            name = "'.$name.'"';
		$db->setQuery($query);
		$sale = $db->loadObject();
		return $sale;
	}
    function getSaleByUserName($userName)
	{
		$db 	= JFactory::getDBO();
		$query = ' SELECT 
		              * 
		           FROM 
		              #__enmasse_sales_person
		           WHERE 
		            user_name = "'.$userName.'"';
		$db->setQuery($query);
		$sale = $db->loadObject();
		return $sale;
	}
}
?>