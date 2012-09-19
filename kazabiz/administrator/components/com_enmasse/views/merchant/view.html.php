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
jimport( 'joomla.application.component.view');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."toolbar.enmasse.html.php");

class EnmasseViewMerchant extends JView
{
	function display($tpl = null)
	{
		$task = JRequest::getWord('task');
		if($task == 'edit' || $task == 'add')
		{
			JRequest::setVar('hidemainmenu', true);
			TOOLBAR_enmasse::_MERCHANT_NEW();
			
			$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
			$merchant = JTable::getInstance('merchant','Table');
			
			if(!$merchant->load($cid['0']))
			{
				JError::raiseError( 500, $merchant->getError() );
				return;
			}
			
			//populate pre-value was save in session if the data exist
			$data = JFactory::getApplication()->getUserState('merchant.add.data');
			$merchant->bind($data);
			
			$this->merchant = $merchant;
			
			$salesPersonList 	= JModel::getInstance('salesPerson','enmasseModel')->listAllPublished();
			$this->salesPersonList =  $salesPersonList ;
			
		}
		else
		{
			TOOLBAR_enmasse::_SMENU();
			$nNumberOfMerchants = JModel::getInstance('merchant','enmasseModel')->countAll();
			if($nNumberOfMerchants==0)
			{
				TOOLBAR_enmasse::_MERCHANT_EMPTY();
			}
			else
			{
				TOOLBAR_enmasse::_MERCHANT();
			}
			
			$filter = JRequest::getVar('filter');
			
			$merchantList = JModel::getInstance('merchant','enmasseModel')->search($filter['name']);
			/// load pagination
			$pagination = $this->get( 'pagination' );
			$state = $this->get( 'state' );
			// get order values
			$order['order_dir'] = $state->get( 'filter_order_dir' );
            $order['order']     = $state->get( 'filter_order' );
            
			for($count=0; $count < count($merchantList); $count++)
			{
				$salesPerson = JModel::getInstance('salesPerson','enmasseModel')->getById($merchantList[$count]->sales_person_id);
				$merchantList[$count]->sales_person_name = $salesPerson->name;
			}
			$this->assignRef( 'filter', $filter );
			$this->assignRef( 'merchantList', $merchantList );
			$this->assignRef('pagination', $pagination);
			$this->assignRef( 'order', $order );
		}
		parent::display($tpl);
	}

}
?>