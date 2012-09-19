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
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");

class EnmasseViewMerchantSettlement extends JView
{
	public function display($tpl = null)
	{
		TOOLBAR_enmasse::_SMENU();
		TOOLBAR_enmasse::_MERCHANT_SETTLEMENT();
			
		$arMerStm = $this->getModel()->search();
			
		// load pagination
		$pagination = $this->get( 'pagination' );
			
		// get order values
		$state = $this->get( 'state' );
		$order['order_dir'] = $state->get( 'filter_order_dir' );
		$order['order']     = $state->get( 'filter_order' );

		$filter = JFactory::getApplication()->getUserStateFromRequest( 'com_enmasse.filter', 'filter', '', 'array' );
		
		$this->arMer = JModel::getInstance('merchant', 'enmasseModel')->listAllPublished();
		$this->arDeal = JModel::getInstance('deal','enmasseModel')->listConfirmed();
		$this->arMerStm = $arMerStm;
		$this->arMerStmStatus = EnmasseHelper::$MERCHANT_SETTLEMENT_STATUS_LIST;
		$this->filter = $filter ;
		$this->pagination = $pagination;
		$this->order = $order ;
		
		parent::display($tpl);
	}

}
?>