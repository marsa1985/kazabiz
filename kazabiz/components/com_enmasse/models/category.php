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

class EnmasseModelCategory extends JModel
{
	function listAll()
	{
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__enmasse_category";
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return $rows;
	}
	
	
	function listAllPublished()
	{
		$db 	= JFactory::getDBO();
		$query = ' SELECT 
		              id, name 
		           FROM 
		              #__enmasse_category 
		           WHERE 
		               published = 1
		           ORDER BY
		               name ASC';
		$db->setQuery($query);
		$category = $db->loadObjectList();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return $category;
	}

	function getById($id)
	{
		$row = JTable::getInstance('category', 'Table');
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
		$query = ' SELECT name as text FROM #__enmasse_category WHERE id = '.$id;
		$db->setQuery($query);
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $db->loadResult();
	}
	function getCategoryListInArrId($idArr)
	{
		$db 	= JFactory::getDBO();
		$query = " SELECT 
		              id, name 
		           FROM 
		              #__enmasse_category 
		           WHERE 
		             id IN (".implode(",", $idArr).")
		            ORDER BY
		              name ASC";
		$db->setQuery($query);
		$category = $db->loadObjectList();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return $category;
	}
}
?>