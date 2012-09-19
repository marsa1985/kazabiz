<?php
function getLocation(){
		$db = JFactory::getDBO();
    	$query = "SELECT
    					distinct loc.id, loc.name
    	          FROM 
    	               `#__enmasse_location` loc 
    	          WHERE 
    	               loc.published = 1";
    	$db->setQuery($query);
    	//$names = $db->loadResultArray();
    	$names = $db->loadObjectList();
    	//print_r($names);
    	return $names;
}