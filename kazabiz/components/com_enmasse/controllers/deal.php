<?php

/* ------------------------------------------------------------------------
  # En Masse - Social Buying Extension 2010
  # ------------------------------------------------------------------------
  # By Matamko.com
  # Copyright (C) 2010 Matamko.com. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.matamko.com
  # Technical Support:  Visit our forum at www.matamko.com
  ------------------------------------------------------------------------- */

jimport('joomla.application.component.controller');

require_once( JPATH_ADMINISTRATOR . DS . "components" . DS . "com_enmasse" . DS . "helpers" . DS . "EnmasseHelper.class.php");

class EnmasseControllerDeal extends JController {
    const CS_SESSION_LOCATIONID = 'CS_SESSION_LOCATIONID';

    public function display($cachable = false, $urlparams = false) {
    	
        JRequest::setVar('view', 'deallisting');
        parent::display();
    }

    function listing() {
        JRequest::setVar('view', 'deallisting');
        parent::display();
    }

    function expiredlisting() {
        JRequest::setVar('view', 'expireddeallisting');
        parent::display();
    }

    function today() {

        JRequest::setVar('view', 'dealtoday');
        parent::display();
    }

    function upcoming() {
        JRequest::setVar('view', 'dealupcoming');
        parent::display();
    }

    function view() {
        JRequest::setVar('view', 'dealdetail');
        parent::display();
    }
    
    function comment() {
        JRequest::setVar('view', 'comment');
        parent::display();
    }    

    function dealSetLocationCookie() {
               
        $locationId = JRequest::getInt('locationId', null);
        $email = JRequest::getVar('email');
        $oMenu = JFactory::getApplication()->getMenu();
        $oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=dealtoday',true);
        
        ////////// Integration with ACY Mailing //////////
        $integrationClass = EnmasseHelper::getSubscriptionClassFromSetting();
        $acy_path = JPATH_SITE . DS . 'components' . DS . 'com_acymailing';
        if (file_exists($acy_path)) {
            if ($integrationClass == 'acystarter' || $integrationClass == 'acyenterprise') {
                require_once(JPATH_SITE . DS . "components" . DS . "com_enmasse" . DS . "helpers" . DS . "subscription" . DS . $integrationClass . DS . $integrationClass . ".class.php");
                if ($integrationClass == 'acystarter')
                    $acy = new acystarter();
                elseif ($integrationClass == 'acyenterprise')
                    $acy = new acyenterprise();
                $acy->updateSubscriptionList($locationId, $email);
            }
        }
        /////////////////////////////////////////////////

        if (!empty($locationId)) {
            //set cookie locationId with lifetime is 365 days
            $dtLifeTime = time() + 365*24*60*60;
            setcookie(self::CS_SESSION_LOCATIONID, $locationId, $dtLifeTime, '/');

            $oDeal = JModel::getInstance('deal', 'enmasseModel')->getDealMaxSoldQtyFromLocation($locationId);
            if ($oDeal->id)
            {
            	JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_enmasse&controller=deal&task=view&id=' . $oDeal->id . '&Itemid=' . $oItem->id, false));
			}
        }
       
        $link = JRoute::_('index.php?option=com_enmasse&controller=deal&task=today&Itemid=' . $oItem->id, false);
        JFactory::getApplication()->redirect($link);
          
    }

    function checkDuplicatedDeal() {
        $dealName = JRequest::getVar("dealName");
        $dealObj = JModel::getInstance('deal', 'enmasseModel')->getDealByName($dealName);
        if($dealObj != null)
            echo 'true';
        else
            echo 'false';
        
        exit;
        
    }
    
}

?>
