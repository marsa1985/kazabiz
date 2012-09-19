<?php

/**
 * @author phuocndt
 * @copyright 2011
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Script file of enmasse component
 */
class com_EnmasseInstallerScript
{
	private $version = '3.0.2';
	private $oldVersion = null;
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	public function install($parent)
	{
		// $parent is the class calling this method
		$parent->getParent()->setRedirectURL('index.php?option=com_enmasse');
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	public function uninstall($parent)
	{
		$db = JFactory::getDBO();
		$query = "DELETE FROM #__extensions WHERE name='com_enmasse';";
		$db->setQuery( $query );
		$db->query();
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_ENMASSE_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * method to update the component
	 * @param $parent is the class calling this method
	 * @return void
	 */
	public function update($parent)
	{
		if($this->oldVersion == $this->version)
		{
			return;
		}

		echo '<p>' . JText::_('COM_ENMASSE_UPDATE_TEXT') . ' To ' .$this->version .' </p>';
			
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	
	public function preflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		if($type == 'update')
		{
			$db = JFactory::getDBO();
			$query = "SELECT * FROM #__extensions WHERE name='com_enmasse'";
			$db->setQuery( $query );
			
			$oExt = $db->loadObject();
			$manifest = json_decode( $oExt->manifest_cache, true );
			$sVersion = $manifest['version'];
			
			//get old version
			$sVersion = substr($sVersion,0, 5); // version string format: 'x.x.x [stable,beta]", we just need 5 first character
			//set old version (for use in update())
			$this->oldVersion = $sVersion;
			
			$extId = $oExt->extension_id;
			$query = "INSERT INTO #__schemas (extension_id, version_id) VALUES ($extId, '$sVersion') ON DUPLICATE KEY UPDATE version_id= VALUES(version_id)";
			$db->setQuery( $query );
			$db->query();
		}
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	public function postflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		
    }
    
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	private function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery("SELECT manifest_cache FROM #__extensions WHERE name = 'com_enmasse'");
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
}

?>