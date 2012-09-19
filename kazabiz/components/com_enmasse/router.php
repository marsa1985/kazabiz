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


// no direct access
defined('_JEXEC') or die('Restricted access');
/*
 * Function to convert a system URL to a SEF URL
 */

function EnmasseBuildRoute(&$query)
{
	$segments = array();
	
	if(isset($query['controller'])) // NON-Menu-item
	{
		$segments[] = $query['controller'];
		 
		// After you remove the Itemid, it will display the component variable e.g. index.php/component/enmasse/....
		unset( $query['Itemid']);
		// To put back the current Itemid of the selected menu, so that it will not display the component variable in the Link
		// but instead, it will display the menu name that the user is in now
		
		if(isset($query['task']))
		{
			$segments[] = $query['task'];
			
			if($query['controller']=="deal" && $query['task']=="view" && trim($query['id']))
			{
				$segments[] = $query['id'];
				$segments[] = $query['slug_name'];
				
				unset( $query['id'] );
				unset( $query['slug_name'] );
			}
			elseif($query['controller']=="deal" && $query['task']=="comment" && trim($query['id']))
			{
				$segments[] = $query['id'];
				unset( $query['id'] );
			}
			elseif($query['controller']=="shopping" && $query['task']=="addToCart" && trim($query['dealId']))
			{
				$segments[] = $query['dealId'];
				$segments[] = $query['slug_name'];
				unset( $query['dealId'] );
				unset( $query['slug_name'] );
			}
			unset( $query['task'] );
		}
		unset( $query['controller'] );
	}
	
	// Menu item links will be handled by the "include/router.php" instead
	
	return $segments;
}

/*
 * Function to convert a SEF URL back to a system URL
 */

function EnmasseParseRoute($segments)
{
	$vars = array();
	if(isset($segments[0]))
		$vars['controller'] = $segments[0];
	if(isset($segments[1]))
	{
		$vars['task'] = $segments[1];
		$oMenu = JFactory::getApplication()->getMenu();
		
		if($vars['controller']=="deal")
		{
			switch ($vars['task']){
				case 'today':
					$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=dealtoday',true);
                    break;
				case 'comment':
                    $vars['id'] = $segments[2];
					$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=dealtoday',true);                    
					break;
				case 'listing':
					$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=deallisting',true);
					//print_r($oItem);die;
					break;
				case 'upcoming':
					$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=dealupcoming',true);
					break;
				case 'expiredlisting':
					$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=expireddeallisting',true);
					break;
				case 'view':
					$vars['id'] = $segments[2];
					$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=dealtoday',true);
					break;
				default:
					$oItem = $oMenu->getDefault();
			}
			
			//set active menu item
			$oMenu->setActive($oItem->id);    	
        	$vars['Itemid'] = $oItem->id;
		}
		elseif($vars['controller']=="shopping" )
		{
			switch ($vars['task']){
				case 'addToCart':
					$vars['dealId'] = $segments[2];
					$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=dealtoday',true);
					
				default:
					$oItem = $oMenu->getDefault();
			}
						
        	$oMenu->setActive($oItem->id);
        	$vars['Itemid'] = $oItem->id;
		}
		elseif($vars['controller']=="salesPerson" )
		{
			switch ($vars['task']){
				case 'dealShow':
					$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=salesperson',true);
					
				default:
					$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=salesperson',true);
			}
						
        	$oMenu->setActive($oItem->id);
        	$vars['Itemid'] = $oItem->id;
		}
		elseif($vars['controller']=="merchant" )
		{
			switch ($vars['task']){
				case 'dealCouponMgmt':
					$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=merchant',true);
					
				default:
					$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=merchant',true);
			}
						
        	$oMenu->setActive($oItem->id);
        	$vars['Itemid'] = $oItem->id;
		}
	}
	return $vars;
}

?>