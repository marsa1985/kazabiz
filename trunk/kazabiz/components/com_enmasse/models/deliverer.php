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

class EnmasseModelDeliverer extends JModel
{
	public function getOrdersByUserId($userId)
	{
		$db = JFactory::getDbo();
		$query = "SELECT order_id
					FROM #__enmasse_order_deliverer
					WHERE user_id = $userId";
		
		$db->setQuery($query);
		
		return $db->loadColumn(0);
	}
}