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
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."EnmasseHelper.class.php");

JFactory::getDocument()->addScript("components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js");
JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict()');

$oDefault = new JObject();
$oDefault->name = '';
$oDefault->id   = '';
//add an empty select option for location and category list
array_unshift($this->locationList, $oDefault);
array_unshift($this->categoryList, $oDefault);
$dealList = $this->dealList;
$nItemId = JRequest::getVar('Itemid');

$app = JFactory::getApplication();
$app->setUserState('staticTitle', JText::_('EXPIRED_TPL_MOBILE'));
?>
	<?php if(!count($dealList)):?>
		<div  class="row"><span class="deal_title"><?php echo JText::_('DEAL_LIST_NO_DEAL_MESSAGE') ?></span></div>
	<?php else:?>
	<!-- list -->
		<?php
		foreach ($dealList as $oDeal):?>
			<?php
			
			//echo count($dealList);
			//$i++;
			$link = 'index.php?option=com_enmasse&controller=deal&task=view&id=' . $oDeal->id ."&slug_name=" .$oDeal->slug_name ."&Itemid=$nItemId";
            if (!EnmasseHelper::is_urlEncoded($oDeal->pic_dir)) {
                $imageUrl = $oDeal->pic_dir;
            } else {
                $imageUrlArr = unserialize(urldecode($oDeal->pic_dir));
                $imageUrl = str_replace("\\", "/", $imageUrlArr[0]);
            }
            $sDealName = $oDeal->name;
            if(strlen($sDealName) > 30)
            {
            	$sDealName = substr($sDealName, 0, 30) ."...";
            }
            $sDealPosition = implode(", ", EnmasseHelper::getDealLocationNames($oDeal->id));
			?>
				<div  class="row row_list item_deal" onclick="window.location.href='<?php echo $link;?>'">
					<a class="img_list" href="<?php echo $link;?>"><img src="<?php echo $imageUrl; ?>" /></a>
					<span><a href="<?php echo $link;?>"><?php echo substr(strip_tags(html_entity_decode($oDeal->name)), 0, 200);?></a></span>
					<br /><span><?php echo  $sDealPosition;?></span>
					<div class="discount"><span><?php echo empty($oDeal->origin_price)? 100 : (100 - intval($oDeal->price/$oDeal->origin_price*100))?>%<?php echo JTEXT::_('OFF');?></span></div>
					<a class="bigger_sign" href="<?php echo $link;?>"></a>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
		<?php endforeach;?>
	<?php endif;?>

