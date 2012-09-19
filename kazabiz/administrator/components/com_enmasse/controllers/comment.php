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
jimport('joomla.application.component.controller');
JTable::addIncludePath('components'.DS.'com_enmasse'.DS.'tables');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."EnmasseHelper.class.php");

class EnmasseControllerComment extends JController
{
	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'comment');
		JRequest::setVar('layout', 'show');
		parent::display();
	}
       
    function publish()
    {
        $aCid = JRequest::getVar('cid', array(), '', 'array');
        JArrayHelper::toInteger($aCid);
        $sCommentIds = implode(',', $aCid);
        $bResult = JModel::getInstance('Comment', 'EnmasseModel')->changeCommentStatus($sCommentIds, 2);
        if($bResult)
        {
            $sMessage = JText::_('COMMENT_PUBLISHED_SUCCESSFULLY');            
        }
        else
        {
            $sMessage = JText::_('COMMENT_PUBLISHED_FAILED'); 
        }
        $this->setRedirect('index.php?option=com_enmasse&controller=comment', $sMessage);
    }
    
    function unpublish()
    {
        $aCid = JRequest::getVar('cid', array(), '', 'array');
        JArrayHelper::toInteger($aCid);
        $sCommentIds = implode(',', $aCid);
        $bResult = JModel::getInstance('Comment', 'EnmasseModel')->changeCommentStatus($sCommentIds, 1);
        if($bResult)
        {
            $sMessage = JText::_('COMMENT_UNPUBLISHED_SUCCESSFULLY');            
        }
        else
        {
            $sMessage = JText::_('COMMENT_UNPUBLISHED_FAILED'); 
        }
        $this->setRedirect('index.php?option=com_enmasse&controller=comment', $sMessage);
    }
        
    function mark_user_as_spammer()
    {
        $aCid = JRequest::getVar('cid', array(), '', 'array');
        $nCid = $aCid[0];
        EnmasseHelper::markUserAsSpammer($nCid);
    }    
    
	function remove()
	{
		$aCid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$model = JModel::getInstance('comment','enmasseModel');
		if($model->deleteList($aCid))
		{
			$sMessage = JText::_('DELETE_SUCCESS_MSG');
			$this->setRedirect('index.php?option=com_enmasse&controller=comment', $sMessage);
		}
		else
		{ 
			$sMessage = JText::_('DELETE_ERROR_MSG') .": " . $model->getError();
			$this->setRedirect('index.php?option=com_enmasse&controller=comment', $sMessage, 'error');
		}
	}
    
	function control()
	{
		$this->setRedirect('index.php?option=com_enmasse');
	}
    
}
?>