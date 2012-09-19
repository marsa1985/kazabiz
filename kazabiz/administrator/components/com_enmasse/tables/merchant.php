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

class TableMerchant extends JTable
{
	var $id = null;
	var $name = null;
	var $user_name = null;
	var $sales_person_id = null;
	var $web_url = null;
	var $logo_url = null;
	var $location_id = null;
	var $description = null;
	var $google_map_width = null;
	var $google_map_height = null;
	var $branches = null;
	var $published = null;
	var $created_at = null;
	var $updated_at = null;
	
	function __construct(&$db)
	{
		parent::__construct( '#__enmasse_merchant_branch', 'id', $db );
	}
	
	public function check()
	{
		$sUrlPtn="/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
		if($this->web_url != null && !preg_match($sUrlPtn, $this->web_url))
		{
			$this->setError(JText::_('INVALID_WEB_URL'));
			return false;
		}
		
		// check name with max, min, invalid charactor
		if($this->name == null || !preg_match("/^[a-zA-Z0-9]+$/", $this->name))
		{
			$this->setError(JText::_('SPECIAL_CHARACTOR_IN_MERCHANT_NAME'));
			return false;
		}
		if ( strlen($this->name) < 8 || strlen($this->name) > 50) {
			$this->setError(JText::_('MERCHANT_NAME_OUT_RANGE'));
			return false;
		}
		
		
		//check duplicate merchant name
		$id = $this->id ? $this->id : 0;
		$db = $this->getDbo();
		$query = "SELECT id
					FROM #__enmasse_merchant_branch
					WHERE id != $id AND name='{$this->name}'";

		$db->setQuery($query);
		if($db->loadResult())
		{
			$this->setError(JText::_('DUPLICATE_MERCHANT_NAME_ERROR_MSG'));
			return false;
		}

		
		return true;
	}
}

?>