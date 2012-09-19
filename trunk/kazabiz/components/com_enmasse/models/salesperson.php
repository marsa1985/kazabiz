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

class EnmasseModelSalesPerson extends JModel
{
	function getById($id)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT 
						* 
					FROM 
						#__enmasse_sales_person 
					WHERE
						id = $id";
		$db->setQuery( $query );
		$obj = $db->loadObject();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $obj;
	}
	
	function getByUserName($username)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT 
						* 
					FROM 
						#__enmasse_sales_person 
					WHERE 
						user_name = '$username'
					AND published != 0";
		$db->setQuery( $query );
		$obj = $db->loadObject();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $obj;
	}
}
?>