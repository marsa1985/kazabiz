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

class EnmasseControllerCommentSpammer extends JController
{
	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'commentSpammer');
		JRequest::setVar('layout', 'show');
		parent::display();
	}
       
	function remove()
	{
		$aCid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$model = JModel::getInstance('CommentSpammer','EnmasseModel');
		if($model->deleteList($aCid))
		{
			$sMessage = JText::_('DELETE_SUCCESS_MSG');
			$this->setRedirect('index.php?option=com_enmasse&controller=commentSpammer', $sMessage);
		}
		else
		{ 
			$sMessage = JText::_('DELETE_ERROR_MSG') .": " . $model->getError();
			$this->setRedirect('index.php?option=com_enmasse&controller=commentSpammer', $sMessage, 'error');
		}
	}
    
	function control()
	{
		$this->setRedirect('index.php?option=com_enmasse');
	}
    
}
?>