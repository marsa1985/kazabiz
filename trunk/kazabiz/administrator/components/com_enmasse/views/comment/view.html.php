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

class EnmasseViewComment extends JView
{
	function display($tpl = null)
	{
        TOOLBAR_enmasse::_SMENU();
        $nNumberOfComments = JModel::getInstance('comment','enmasseModel')->countAll();
        if($nNumberOfComments==0)
        {
            TOOLBAR_enmasse::_COMMENT_EMPTY();
        }
        else
        {
            TOOLBAR_enmasse::_COMMENT();
        }
        /// load pagination
        $pagination = $this->get('Pagination');
        $state = $this->get( 'state' );
        // get order values
        $order['order_dir'] = $state->get( 'filter_order_dir' );
        $order['order']     = $state->get( 'filter_order' );
        
        $commentList = JModel::getInstance('comment','enmasseModel')->search();

        $this->assignRef('commentList', $commentList);
        $this->assignRef('pagination', $pagination);
        $this->assignRef('order', $order);

        parent::display($tpl);
	}

}
?>