<?php

/**
 * @author phuocndt
 * @copyright 2011
 */

class joomsocial{
    
    protected $api_AUP;
    
    function integration($user_id,$deal_id=0,$key,$point=0)
	{
	    $api_AUP = JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php';
        $link = "index.php?option=com_enmasse&controller=deal&task=view&id=".$deal_id;
        
	    if (file_exists($api_AUP))
		{	
            if($key=="confirmdeal")
		 	{
		 		joomsocial::confirmDeal($user_id,$link);
		 	}
		 	elseif($key=="referralbonus")
		 	{
		 		joomsocial::addReferralBonus($user_id);
		 	}
			elseif($key=="buydeal")
		 	{
		 		joomsocial::buyDeal($user_id,$link,$point);
		 	}
			elseif($key=="refunddeal")
		 	{
		 		joomsocial::refundDeal($user_id,$point);
		 	}			 			 	
		}
	}
    
    function confirmDeal($user_id,$link)
	{
		$api_AUP = JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php';
	    require_once ($api_AUP);
        
        CuserPoints::assignPoint('enmasse.deal.confirm', $user_id);
        
        $act = new stdClass();
        $act->cmd 	    = 'wall.write';
        $act->actor 	= $user_id;
        $act->target 	= 0;
        $act->title 	= JText::_('{actor} have just point for confirm <a href="'.$link.'">a Deal</a>');
        $act->content 	= '';
        $act->app 	    = 'wall';
        $act->cid 	    = 0;
        CFactory::load('libraries', 'activities');
        CActivityStream::add($act);
	}
	
	function addReferralBonus($user_id)
	{
		$api_AUP = JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php';
	    require_once ($api_AUP);
        
		CuserPoints::assignPoint('enmasse.referral.add', $user_id);
        
        $act = new stdClass();
        $act->cmd 	    = 'wall.write';
        $act->actor 	= $user_id;
        $act->target 	= 0;
        $act->title 	= JText::_('{actor} have just point for referral bonus');
        $act->content 	= '';
        $act->app 	    = 'wall';
        $act->cid 	    = 0;
        CFactory::load('libraries', 'activities');
        CActivityStream::add($act);
	}

	function buyDeal($user_id,$link,$point)
	{
		$api_AUP = JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php';
	    require_once ($api_AUP);
        
		$juser	=& JFactory::getUser($userId);		
		if( $juser->id != 0 )
		{
			$aid    = $juser->aid;
			
			if(is_null($aid))
			{
				$aid = 0;
				$acl 	=& JFactory::getACL();
				$grp 	= $acl->getAroGroup($juser->id);						
				$group	= 'USERS';
						
				if($acl->is_group_child_of( $grp->name, $group))
				{
					$aid	= 1;
					// Fudge Authors, Editors, Publishers and Super Administrators into the special access group
					if ($acl->is_group_child_of($grp->name, 'Registered') ||
					    $acl->is_group_child_of($grp->name, 'Public Backend'))    {
						$aid	= 2;
					}
				}
			}
            
			$user	= CFactory::getUser($userId);
			$points	= $user->getKarmaPoint();
			$point = $point/2;
            $user->_points = $points - $point;
            
            $user->save();
            
            $act = new stdClass();
            $act->cmd 	    = 'wall.write';
            $act->actor 	= $user_id;
            $act->target 	= 0;
            $act->title 	= JText::_('{actor} lost point for buy <a href="'.$link.'">a Deal</a>');
            $act->content 	= '';
            $act->app 	    = 'wall';
            $act->cid 	    = 0;
            CFactory::load('libraries', 'activities');
            CActivityStream::add($act);
		}		
	}
	
	function refundDeal($user_id,$point)
	{
		$api_AUP = JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php';
	    require_once ($api_AUP);
        
		$juser	=& JFactory::getUser($userId);		
		if( $juser->id != 0 )
		{
			$aid    = $juser->aid;
			
			if(is_null($aid))
			{
				$aid = 0;
				$acl 	=& JFactory::getACL();
				$grp 	= $acl->getAroGroup($juser->id);						
				$group	= 'USERS';
						
				if($acl->is_group_child_of( $grp->name, $group))
				{
					$aid	= 1;
					// Fudge Authors, Editors, Publishers and Super Administrators into the special access group
					if ($acl->is_group_child_of($grp->name, 'Registered') ||
					    $acl->is_group_child_of($grp->name, 'Public Backend'))    {
						$aid	= 2;
					}
				}
			}
            
			$user	= CFactory::getUser($userId);
			$points	= $user->getKarmaPoint();
			$point = $point/2;
            $user->_points = $points - $point;
            
            $user->save();
            
            $act = new stdClass();
            $act->cmd 	    = 'wall.write';
            $act->actor 	= $user_id;
            $act->target 	= 0;
            $act->title 	= JText::_('{actor} just refund a Deal');
            $act->content 	= '';
            $act->app 	    = 'wall';
            $act->cid 	    = 0;
            CFactory::load('libraries', 'activities');
            CActivityStream::add($act);
		}
	}	
	
	function checkEnoughPoint($user_id, $neededPoint)
	{
		$db =& JFactory::getDBO();
        
		$query = "SELECT points FROM #__community_users WHERE userid='".$user_id."'";
		$db->setQuery($query);
		$result = $db->loadObject();
        
		if($result->points >= $neededPoint)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
    
	function getPoint($user_id)
	{
		$jspath = JPATH_ROOT.DS.'components'.DS.'com_community';
		include_once($jspath.DS.'libraries'.DS.'core.php');
		// Get CUser object
		$user = CFactory::getUser($user_id);
		$point = $user->getKarmaPoint();
		return $point;
	}    
}

?>