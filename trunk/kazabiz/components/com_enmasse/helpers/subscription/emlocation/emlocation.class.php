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
class emlocation
{
	 function integration($data,$key)
	 {
	 	return true;
	 }
	 
	 function getViewData($params)
	 {
	 	$data->locationList = JModel::getInstance('location','enmasseModel')->listAllPublished();
	 	return $data;
	 }
     function addMenu()
	 {
	 	 return true;
	 }
	 
}
?>