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
 
class EnmasseViewComment extends JView
{
    function display($tpl = null)
    {
    	$nDealId = JRequest::getVar('id', 0);		
        $oDeal = JModel::getInstance('deal','enmasseModel')->viewDeal($nDealId);        
        //we must check $deal->id because $deal was loaded from JTable so it alway not null.
        if(empty($oDeal->id))
        {
        	$sLink = JRoute::_("index.php?option=com_enmasse&controller=deal&task=listing", false);
			$sMessage = JText::_('DEAL_NOT_FOUND');
			JFactory::getApplication()->redirect($sLink, $sMessage, 'error');
        }
        $this->assignRef('objDeal', $oDeal);
        $aComments = JModel::getInstance('comment', 'enmasseModel')->getCommentByDealId($nDealId);
        $this->assignRef('aComments', $aComments);
        $this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
        $this->_layout="comment";
        parent::display($tpl);
    }
}
?>