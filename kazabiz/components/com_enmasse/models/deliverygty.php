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

jimport( 'joomla.application.component.model' );

class EnmasseModelDeliveryGty extends JModel
{
	function getById($id)
	{
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__enmasse_delivery_gty WHERE id = $id";
		$db->setQuery( $query );
		$payGty = $db->loadObject();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $payGty;
	}	
}
?>