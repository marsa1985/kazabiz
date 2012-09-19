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

require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."EnmasseHelper.class.php");

class EnmasseControllerMail extends JController
{
	function __construct()
	{
		parent::__construct();
	}

	function display($cachable = false, $urlparams = false) {
		
	}
	function sendMail()
	{
		$post = JRequest::get('post');
		$share_url = JURI::base().'index.php?option=com_enmasse&controller=deal&task=view&id='.$post['dealid'];
        if($post['userid']!='0')
        {
			$share_url .= '&referralid='.$post['userid'];	                	
		}
        $share_url .= '&Itemid='.$post['itemid'];
		$content = $post['content'] . "<br/>" . JText::_('DEAL_LINK') . ": " . $share_url;		
		if(EnmasseHelper::sendMail($post['recipient'], $post['subject'], $content))
		{
			$this->setRedirect('index.php?option=com_enmasse&controller=mail&task=mailForm&tmpl=component&success=1');
		}
		else
		{
			$this->setRedirect('index.php?option=com_enmasse&controller=mail&task=mailForm&tmpl=component');
		}
	}

	function mailForm()
	{
		JRequest::setVar('view', 'mail');
		parent::display();
	}
}
?>