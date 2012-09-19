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

class EnmasseModelComment extends JModel
{
	function getCommentByDealId($nDealId)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT 
						* 
					FROM 
						#__enmasse_comment 
					WHERE
                        status = 2
                    AND
						deal_id = $nDealId";
		$db->setQuery( $query );
		$aResult = $db->loadAssocList();
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}		
		return $aResult;
	}
    
	function store($data)
	{
		$row = $this->getTable();

		if(!$row->bind($data))
        {
			$this->setError($row->getErrors());
			return false;
		}	

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
}
?>