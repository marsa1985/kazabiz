<?php
class alphauserpoints
{
	
	function integration($data,$key,$point=0)
	{
		$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php'; 
		if (file_exists($api_AUP)) 
		{		
			$user_id = $data;
			if($key=="confirmdeal")
		 	{
		 		alphauserpoints::confirmDeal($user_id);
		 	}
		 	elseif($key=="referralbonus")
		 	{
		 		alphauserpoints::addReferralBonus($user_id);
		 	}
			elseif($key=="paybypoint")
		 	{
		 		alphauserpoints::payByPoint($user_id,$point);
		 	}
			elseif($key=="refunddeal")
		 	{
		 		alphauserpoints::refundDeal($user_id,$point);
		 	}			 			 	
		}
	}	
	
	function confirmDeal($user_id)
	{
		$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
		require_once ($api_AUP); 
		$aupid = AlphaUserPointsHelper::getAnyUserReferreID( $user_id ); 
		if ( $aupid ) AlphaUserPointsHelper::newpoints( 'plgaup_com_enmasse_confirm_deal', $aupid);
	}
	
	function addReferralBonus($user_id)
	{
		$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
		require_once ($api_AUP); 
		$aupid = AlphaUserPointsHelper::getAnyUserReferreID( $user_id ); 
		if ( $aupid ) AlphaUserPointsHelper::newpoints( 'plgaup_com_enmasse_referral_bonus', $aupid);
	}

	function payByPoint($user_id,$point)
	{
		$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
		require_once ($api_AUP); 
		$point = 0 - $point;
		$aupid = AlphaUserPointsHelper::getAnyUserReferreID( $user_id ); 
		if ( $aupid ) AlphaUserPointsHelper::newpoints( 'plgaup_com_enmasse_pay_by_point', $aupid, '', '', $point);		
	}
	
	function refundDeal($user_id,$point)
	{
		$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
		require_once ($api_AUP); 
		$aupid = AlphaUserPointsHelper::getAnyUserReferreID( $user_id ); 
		if ( $aupid ) AlphaUserPointsHelper::newpoints( 'plgaup_com_enmasse_refund_by_point', $aupid, '', '', $point);
	}	
	
	function checkEnoughPoint($user_id, $neededPoint)
	{
		$db = JFactory::getDBO();
		$query = "SELECT points FROM #__alpha_userpoints WHERE userid='".$user_id."'";
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
		require_once(JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php'); 
		$profile = AlphaUserPointsHelper::getUserInfo ('',$user_id) ;
		$point = $profile->points;
		return $point;
	}    
}