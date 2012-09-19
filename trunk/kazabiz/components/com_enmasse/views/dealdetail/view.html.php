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

require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php"); 
 
class EnmasseViewDealDetail extends JView
{
    function display($tpl = null)
    { 
    	
    	$id = JRequest::getVar('id', 0);
    	$bFlag = JRequest::getVar('sideDealFlag', false);
    	$upcoming = JRequest::getVar('upcoming');
 		
        $deal 			= JModel::getInstance('deal','enmasseModel')->viewDeal($id);
        
        //we must check $deal->id because $deal was loaded from JTable so it alway not null.
        if(empty($deal->id))
        {
        	$link = JRoute::_("index.php?option=com_enmasse&controller=deal&task=listing", false);
			$msg = JText::_('DEAL_NOT_FOUND');

			JFactory::getApplication()->redirect($link, $msg, 'error');
        }
                
        $deal->merchant = JModel::getInstance('merchant','enmasseModel')->getById($deal->merchant_id);
        $deal->merchant->branches = json_decode($deal->merchant->branches, true);
        $this->assignRef( 'deal', $deal );
        $this->assignRef( 'sideDealFlag', $bFlag);
        
        //Referral ID
        $referralId = JRequest::getVar('referralid');
        $this->assignRef( 'referralId', $referralId );
        
        if($upcoming)
        {
        	$this->assignRef('upcoming',$upcoming);
        }
             	 
        $this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
    	$this->_layout="deal_detail";
        parent::display($tpl);
    }

}
?>