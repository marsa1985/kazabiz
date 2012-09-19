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

jimport('joomla.application.component.controller');

require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");

require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");

class EnmasseControllerComment extends JController
{
    function __construct()
    {
    parent::__construct();
    }

    function submit_review() 
    {
        $nDealId = JRequest::getVar('nDealId');
        $nRating = JRequest::getVar('nRating');
        $sReviewBody = JRequest::getVar('sReviewBody');

        // Be sure this is a valid deal id
        if($nDealId > 0)
        {
            // Check for a valid rating number and review content
            // User has to select his/her rating (the number is from 1 to 5)
            // and has enter his/her review
            if($nRating <= 0 || $nRating > 5)
            {
                $sMessage = JText::_('PLEASE_RATE');
                $sRedirectUrl = JRoute::_('index.php?option=com_enmasse&controller=deal&task=comment&id=' . $nDealId, false);
            }
            elseif($sReviewBody == '')
            {
                $sMessage = JText::_('PLEASE_ENTER_REVIEW');
                $sRedirectUrl = JRoute::_('index.php?option=com_enmasse&controller=deal&task=comment&id=' . $nDealId, false);
            }
            else // Everything is fine, now start storing the review
            {
                if(EnmasseHelper::checkSpammer(JFactory::getUser()->get('id')))
                {
                    // If this user is a spammer, lie to him/her that the review is submitted but actually we store nothing
                    $sMessage = JText::_('REVIEW_SUBMITTED_SUCCESSFULLY');
                    $sRedirectUrl = JRoute::_('index.php?option=com_enmasse&controller=deal&task=comment&id=' . $nDealId, false);
                }
                else
                {
                    $aComment = array();
                    $aComment['deal_id'] = $nDealId;
                    $aComment['user_id'] = JFactory::getUser()->get('id');
                    $aComment['comment'] = $sReviewBody;
                    $aComment['rating'] = $nRating;
                    $aComment['created_at'] = DatetimeWrapper::getDatetimeOfNow();
                    $aComment['status'] = 0;
                    $oRow = JModel::getInstance('comment','enmasseModel')->store($aComment);
                    if($oRow->success)
                    {
                        $sMessage = JText::_('REVIEW_SUBMITTED_SUCCESSFULLY');
                    }
                    else
                    {
                        $sMessage = JText::_('SAVE_REVIEW_FAILED');
                    }
                    $sRedirectUrl = JRoute::_('index.php?option=com_enmasse&controller=deal&task=comment&id=' . $nDealId, false);
                }
            }
        }
        else
        {
            $sMessage = JText::_('SAVE_REVIEW_FAILED');
            $sRedirectUrl = JRoute::_('index.php?option=com_enmasse&view=dealtoday', false);
        }
        $this->setRedirect($sRedirectUrl, $sMessage);
    }
}
?>