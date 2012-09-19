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

class EnmasseControllerCoupon extends JController
{
  function __construct()
  {
    parent::__construct();
  }
  
  function listing() 
  {
    JRequest::setVar('view', 'couponlisting');
    parent::display();
  }
  
  function generate() 
  {
    JRequest::setVar('view', 'coupon');
    parent::display();
  }
}
?>