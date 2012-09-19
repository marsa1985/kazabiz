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

class TOOLBAR_enmasse
{
	public static function _PAY_GTY_NEW() {
		$task 	= JRequest::getCmd( 'task');
		JToolBarHelper::title( JText::_( 'T_PAYMENT_GATEWAY_MANAGEMENT').' : ['.$task.']' ,
                                           'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}
	
	public static function _COUPON() {
		$task 	= JRequest::getCmd( 'task');
		JToolBarHelper::title( JText::_( 'T_COUPON_MANAGEMENT' ),
                                           'generic.png' );
		//JToolBarHelper::custom( 'addElement', 'new.png', 'new.png', 'New Element', false,  false );
		//JToolBarHelper::spacer();
		//JToolBarHelper::divider();
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'save', 'save.png', 'save.png', 'T_COUPON_MANAGEMENT_SAVE', false,  false );
		JToolBarHelper::cancel();
	}
	
	public static function _COUPONELEMENT_NEW() {
		$task 	= JRequest::getCmd( 'task');
		JToolBarHelper::title(  JText::_( 'T_COUPON_MANAGEMENT').': ['.$task.']' ,
                                           'generic.png' );
		JToolBarHelper::custom( 'saveElement', 'save.png', 'save.png', 'T_COUPON_MANAGEMENT_SAVE_ELEMENTS', false,  false );
		JToolBarHelper::custom( 'cancelElement', 'cancel.png', 'cancel.png', 'T_COUPON_MANAGEMENT_CANCEL_ELEMENTS', false,  false );
	}
	
	public static function _EHELP() {
		JToolBarHelper::title( JText::_( 'T_HELP' ),
                                           'generic.png' );
        JToolBarHelper::back( 'T_MAIN', 'index.php?option=com_enmasse');
	}

	public static function _SMENU() {
		JSubMenuHelper::addEntry(JText::_('S_SETTING'), 'index.php?option=com_enmasse&controller=setting&cid=1');
		JSubMenuHelper::addEntry(JText::_('S_CATEGORY'), 'index.php?option=com_enmasse&controller=category');
		JSubMenuHelper::addEntry(JText::_('S_LOCATION'), 'index.php?option=com_enmasse&controller=location');
		//JSubMenuHelper::addEntry(JText::_('Tax'), 'index.php?option=com_enmasse&controller=tax');
		JSubMenuHelper::addEntry(JText::_('S_PAY_GATEWAY'), 'index.php?option=com_enmasse&controller=payGty');
		JSubMenuHelper::addEntry(JText::_('S_COUPON_EDITOR'), 'index.php?option=com_enmasse&controller=coupon');
		JSubMenuHelper::addEntry(JText::_('S_EMAIL_TEMPLATE'), 'index.php?option=com_enmasse&controller=emailTemplate');
		JSubMenuHelper::addEntry(JText::_('S_SALE_PERSON'), 'index.php?option=com_enmasse&controller=salesPerson');
		JSubMenuHelper::addEntry(JText::_('S_MERCHANT'), 'index.php?option=com_enmasse&controller=merchant');
		JSubMenuHelper::addEntry(JText::_('S_DEAL'), 'index.php?option=com_enmasse&controller=deal');
		JSubMenuHelper::addEntry(JText::_('S_ORDER'), 'index.php?option=com_enmasse&controller=order');
		JSubMenuHelper::addEntry(JText::_('S_PARTIAL_ORDER'), 'index.php?option=com_enmasse&controller=partialOrder');
		JSubMenuHelper::addEntry(JText::_('S_REPORT'), 'index.php?option=com_enmasse&controller=report');
		JSubMenuHelper::addEntry(JText::_('S_MERCHANT_SETTLEMENT'), 'index.php?option=com_enmasse&controller=merchantSettlement');
		JSubMenuHelper::addEntry(JText::_('S_BILL_TEMPLATE'), 'index.php?option=com_enmasse&controller=billTemplate');
		JSubMenuHelper::addEntry(JText::_('S_HELP'), 'index.php?option=com_enmasse&controller=help');
		JSubMenuHelper::addEntry(JText::_('S_COMMENT'), 'index.php?option=com_enmasse&controller=comment');
		JSubMenuHelper::addEntry(JText::_('S_COMMENT_SPAMMER'), 'index.php?option=com_enmasse&controller=commentSpammer');            
		JSubMenuHelper::addEntry(JText::_('S_SALE_REPORTS'), 'index.php?option=com_enmasse&controller=saleReports');
	}


	public static function _PAY_GTY() {
		JToolBarHelper::title( JText::_( 'T_PAYMENT_GATEWAY_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList(JText::_('T_PAYMENT_GATEWAY_DELETE_CONFIRM_MSG'));
		JToolBarHelper::addNew();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}
	
	public static function _PAY_GTY_EMPTY() {
		JToolBarHelper::title( JText::_( 'T_PAYMENT_GATEWAY_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::addNew();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}	

	public static function _CATEGORY_NEW() {
		$task 	= JRequest::getCmd( 'task');
		JToolBarHelper::title( JText::_( 'T_CATEGORY_MANAGEMENT').' : ['.$task.']' ,
                                           'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}
    
	public static function _COMMENT() {
		JToolBarHelper::title( JText::_( 'T_COMMENT_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
        JToolBarHelper::custom( 'mark_user_as_spammer', 'back.png', 'back.png', 'T_COMMENT_USER_SPAMMER', true );
		JToolBarHelper::deleteList(JText::_('T_COMMENT_DELETE_CONFIRM_MSG'));
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	} 
    
	public static function _COMMENT_EMPTY() {
		JToolBarHelper::title( JText::_( 'T_COMMENT_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}
    
	public static function _COMMENT_SPAMMER() {
		JToolBarHelper::title( JText::_( 'T_COMMENT_SPAMMER_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::deleteList(JText::_('T_COMMENT_DELETE_CONFIRM_MSG'));
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
        
	} 
    
	public static function _COMMENT_SPAMMER_EMPTY() {
		JToolBarHelper::title( JText::_( 'T_COMMENT_SPAMMER_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}	      

	public static function _CATEGORY() {
		JToolBarHelper::title( JText::_( 'T_CATEGORY_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList(JText::_('T_CATEGORY_DELETE_CONFIRM_MSG'));
		JToolBarHelper::addNew();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}
	
	public static function _CATEGORY_EMPTY() {
		JToolBarHelper::title( JText::_( 'T_CATEGORY_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::addNew();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}	

	public static function _SALESPERSON() {
		JToolBarHelper::title( JText::_( 'T_SALES_PERSON_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList(JText::_('T_SALES_PERSON_DELETE_CONFIRM_MSG'));
		JToolBarHelper::addNew();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}

	public static function _SALESPERSON_EMPTY() {
		JToolBarHelper::title( JText::_( 'T_SALES_PERSON_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::addNew();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}
	
	public static function _SALESPERSON_NEW() {
		$task 	= JRequest::getCmd( 'task');
		JToolBarHelper::title( JText::_( 'T_SALES_PERSON_MANAGEMENT'). ' : ['.$task.']' ,
                                           'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	public static function _LOCATION_NEW() {
		$task   = JRequest::getCmd( 'task');
		JToolBarHelper::title( JText::_( 'T_LOCATION_MANAGEMENT').' : ['.$task.']' ,
                                           'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	public static function _LOCATION() {
		JToolBarHelper::title( JText::_( 'T_LOCATION_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList(JText::_('T_LOCATION_DELETE_CONFIRM_MSG'));
		JToolBarHelper::addNew();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}
	
	public static function _LOCATION_EMPTY() {
		JToolBarHelper::title( JText::_( 'T_LOCATION_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::addNew();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}	

	public static function _MERCHANT() {
		JToolBarHelper::title( JText::_( 'T_MERCHANT_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList(JText::_('T_MERCHANT_DELETE_CONFIRM_MSG'));
		JToolBarHelper::addNew();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}
	
	public static function _MERCHANT_EMPTY() {
		JToolBarHelper::title( JText::_( 'T_MERCHANT_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::addNew();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}

	public static function _MERCHANT_NEW() {
		$task 	= JRequest::getCmd( 'task');
		JToolBarHelper::title( JText::_( 'T_MERCHANT_MANAGEMENT').' : ['.$task.']' ,
                                           'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	public static function _DEAL() {
		JToolBarHelper::title( JText::_( 'T_DEAL_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::custom( 'approveDeal', 'apply.png', 'apply.png', 'T_DEAL_APPROVE_PENDING', false,  false );
		JToolBarHelper::custom( 'voidDeal', 'cancel.png', 'cancel.png', 'T_DEAL_VOID_DEAL', false,  false );
		JToolBarHelper::custom( 'confirmDeal', 'upload.png', 'upload.png', 'T_DEAL_CONFIRM_DEAL', false,  false );
		JToolBarHelper::spacer();
		JToolBarHelper::divider();
		JToolBarHelper::spacer();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList(JText::_('T_DEAL_DELETE_CONFIRM_MSG'));
		JToolBarHelper::addNew();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}
	
	public static function _DEAL_DETAIL($sStatus)
	{
		$task 	= JRequest::getCmd( 'task');
		JToolBarHelper::title( JText::_( 'T_DEAL_MANAGEMENT').' : ['.$task.']' ,'generic.png' );
		$bar = JToolBar::getInstance('toolbar');
		switch ($sStatus)
		{
			case EnmasseHelper::$DEAL_STATUS_LIST['Pending'] :
				$bar->appendButton('Confirm', JText::_('T_DEAL_APPROVE_CONFIRM_MSG'), 'apply', 'T_DEAL_APPROVE_PENDING', 'approveDeal', false);
				break;
			case EnmasseHelper::$DEAL_STATUS_LIST['On Sales'] :
				$bar->appendButton('Confirm', JText::_('T_DEAL_CONFIRMDEAL_CONFIRM_MSG'), 'upload', 'T_DEAL_CONFIRM_DEAL', 'confirmDeal', false);
				$bar->appendButton('Confirm', JText::_('T_DEAL_VOID_CONFIRM_MSG'), 'cancel', 'T_DEAL_VOID_DEAL', 'voidDeal', false);
				break;
			
		}
		JToolBarHelper::spacer();
		JToolBarHelper::divider();
		JToolBarHelper::spacer();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}
	
	public static function _DEAL_EMPTY() {
		JToolBarHelper::title( JText::_( 'T_DEAL_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::addNew();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}	

	public static function _DEAL_NEW() {
		$task 	= JRequest::getCmd( 'task');
		JToolBarHelper::title( JText::_( 'T_DEAL_MANAGEMENT').' : ['.$task.']' ,
                                           'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}
	
	public static function _MERCHANT_SETTLEMENT() {
		$task 	= JRequest::getCmd( 'task');
		//JToolBarHelper::title( JText::_( 'T_MERCHANT_SETTLEMENT_MANAGEMENT').' : ['.$task.']' ,'generic.png' );
		JToolBarHelper::title( JText::_( 'T_MERCHANT_SETTLEMENT_MANAGEMENT'),'generic.png' );
		/*$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Confirm', JText::_('T_MERCHANT_SETTLEMENT_PAID_OUT_CONFIRM_MSG'), 'apply', 'T_MERCHANT_SETTLEMENT_PAID_OUT', 'paidOut', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();*/
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}

	public static function _TAXES() {
		JToolBarHelper::title( JText::_( 'TAX MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList(JText::_('T_TAX_DELETE_CONFIRM_MSG'));
		JToolBarHelper::addNew();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}

	public static function _TAXES_NEW() {
		$task 	= JRequest::getCmd( 'task');
		JToolBarHelper::title( JText::_( 'TAX MANAGEMENT : ['.$task.']' ),
                                           'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	public static function _SETTING() {
		JToolBarHelper::title( JText::_( 'T_SYSTEM_SETTING' ),
                                           'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}

	public static function _ORDER() {
		JToolBarHelper::title( JText::_( 'T_ORDER_MANAGEMENT' ),
                                           'generic.png' );
		
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}
	
	public static function _PARTIAL_ORDER() {
		JToolBarHelper::title( JText::_( 'T_PARTIAL_ORDER_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::custom( 'assignOrder', 'apply.png', 'apply.png', 'T_PARTIAL_ORDER_ASSIGN_TO', true );
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}

	public static function _ORDER_NEW() {
		$task 	= JRequest::getCmd( 'task');
		JRequest::setVar('hidemainmenu', true);
		JToolBarHelper::title( JText::_( 'T_ORDER_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::save();		
		JToolBarHelper::custom( 'back', 'back.png', 'back.png', 'Back', false,  false );
	}

	public static function _EMAILTEMPLATE() {
		JToolBarHelper::title( JText::_( 'T_EMAIL_TEMPLATE_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::editList();
		//JToolBarHelper::deleteList();
		//JToolBarHelper::addNew();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}

	public static function _EMAILTEMPLATE_NEW() {
		$task   = JRequest::getCmd( 'task');
		JToolBarHelper::title( JText::_( 'T_EMAIL_TEMPLATE_MANAGEMENT').' : ['.$task.']' ,
                                           'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}
	
	public static function _BILLTEMPLATE() {
		JToolBarHelper::title( JText::_( 'T_BILL_TEMPLATE_MANAGEMENT' ),
                                           'generic.png' );
		JToolBarHelper::editList();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}

	public static function _BILLTEMPLATE_EDIT() {
		$task   = JRequest::getCmd( 'task');
		JToolBarHelper::title( JText::_( 'T_BILL_TEMPLATE_MANAGEMENT').' : ['.$task.']' ,
                                           'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}
	
	public static function _REPORT() {
		JToolBarHelper::title( JText::_( 'T_DEAL_REPORT' ), 'article.png' );
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}


	public static function _DEFAULT() {
		JToolBarHelper::title( JText::_( 'DASH_BOARD' ),
                                           'article.png' );
		JToolBarHelper::preferences('com_enmasse');
        JToolBarHelper::divider();
	}

	public static function _HELP() {
		JToolBarHelper::title( JText::_( 'EN MASSE HELP' ),
                                           'generic.png' );
		//	JToolBarHelper::editList();
		//    JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'Main', false,  false );
	}
	public static function _SALE_REPORTS() {
		$task 	= JRequest::getCmd( 'task');
		JToolBarHelper::title(  JText::_( 'T_REPORT'),  'generic.png' );
		
		//JToolBarHelper::custom( 'cancel', 'cancel.png', 'cancel.png', 'T_COUPON_MANAGEMENT_CANCEL_ELEMENTS', false,  false );
		JToolBarHelper::custom( 'printPdf', 'print.png', 'print.png', 'T_COUPON_MANAGEMENT_PRINT_ELEMENTS', false,  false );
		JToolBarHelper::custom( 'pdf', 'pdf.png', 'pdf.png', 'T_COUPON_MANAGEMENT_PDF_ELEMENTS', false,  false );
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'T_MAIN', false,  false );
	}
	
	
}
?>