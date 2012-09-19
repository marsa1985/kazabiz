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

class EnmasseModelLocation extends JModel
{
	  var $_total = null;
	  var $_pagination = null;
	  public function __construct()
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
	 
	                if(!empty($filter_order) && !empty($filter_order_dir))
	                {
	                	if ($filter_order == 'name'||$filter_order == 'created_at' || $filter_order == 'updated_at')
	                        $orderby = ' ORDER BY '.$filter_order.' '.$filter_order_dir;
	                }
	 
	                return $orderby;
	   }

	   public function countAll()
	   {
		   	$db = JFactory::getDBO();
		   	$query = "SELECT COUNT(*) FROM #__enmasse_location";
		   	$db->setQuery($query);
		   	$count = $db->loadResult();
		   	return $count;
	   }

	   public function getTotal()
	   {
	        // Load total records
	        if (empty($this->_total)) {
	            $query = "SELECT * FROM #__enmasse_location".$this->_buildContentOrderBy();
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
	  
	  
	public function search()
	{
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__enmasse_location".$this->_buildContentOrderBy();
		$db->setQuery( $query );
//		$rows = $db->loadObjectList();
        $rows = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); //to get rows with limited of pagination
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $rows;
	}
	
	public function listAllPublished()
	{
		$db     = JFactory::getDBO();
		$query = ' SELECT
		             id, name 
		           FROM 
		             #__enmasse_location 
		           WHERE 
		              published=1
		           ORDER BY
		               name ASC';
		$db->setQuery($query);
		$location = $db->loadObjectList();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $location;
	}
	
	public function retrieveName($id)
	{
		$db = JFactory::getDBO();
		$query = "SELECT name FROM #__enmasse_location";
		$query .= " where id = ".$id;
		$db->setQuery( $query );
		$loc = $db->loadObject();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $loc->name;
	}
	
	public function getById($id)
	{
		$row = JTable::getInstance('location', 'Table');
		$row->load($id);
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $row;
	}

	public function store($data)
	{
		$row = $this->getTable();
		$data['name'] = trim($data['name']);
		if (!$row->bind($data)) {
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
			$row->success = false;
			$this->setError($this->_db->getErrorMsg());
		}
		else
		{
			$row->oldname = $data['oldname'];
		  $row->success = true;
		}
		 return $row;
	}

	public function deleteList($cids)
	{
		$row = $this->getTable();

		foreach($cids as $cid) {
			if (!$row->delete( $cid )) {
				$this->setError( $row->getErrorMsg() );
				return false;
			}
		}

		return true;
	}
	public function getLocationListInArrId($idArr)
	{
		$db 	= JFactory::getDBO();
		$query = " SELECT 
		              id, name 
		           FROM 
		              #__enmasse_location
		           WHERE 
		             id IN (".implode(",", $idArr).")
		            ORDER BY
		              name ASC";
		$db->setQuery($query);
		$location = $db->loadObjectList();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return $location;
	}
	
	public function getLocationByName($name)
	{
		$db 	= JFactory::getDBO();
		$query = ' SELECT 
		              * 
		           FROM 
		              #__enmasse_location
		           WHERE 
		            name = "'.$name.'"';
		$db->setQuery($query);
		$location = $db->loadObject();
		return $location;
	}
	
}
?>