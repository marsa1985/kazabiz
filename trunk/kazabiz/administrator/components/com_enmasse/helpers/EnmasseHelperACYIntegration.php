<?php
require_once JPATH_ADMINISTRATOR . DS ."components". DS . 'com_acymailing' .DS .'helpers' .DS .'/helper.php';;
class EnmasseHelperACYIntegration
{
	public static function getMails()
	{
		$db = JFactory::getDbo();
		$query = "SELECT * FROM " .acymailing::table('mail')
				. "  WHERE published = 1 AND visible = 1";
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}