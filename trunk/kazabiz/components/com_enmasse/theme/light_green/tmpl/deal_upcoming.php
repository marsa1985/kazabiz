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
 
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");

$theme =  EnmasseHelper::getThemeFromSetting();
JFactory::getDocument()->addStyleSheet('components/com_enmasse/theme/' . $theme . '/css/screen.css');

//assign short name for variables
$dealList = $this->dealList;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<div class="maincol_full_header">
	<h2><?php echo JText::_('DEAL_LIST_UPCOMING_DEAL')?></h2>
</div>
<div class="maincol_full_content">
	
	<?php if(!count($dealList)):?>
		<div><h3><?php echo JText::_('DEAL_LIST_NO_DEAL_MESSAGE') ?></h3></div>
	<?php else:?>
		<?php
			$oDeal = array_shift($dealList);
			$nItemId = JFactory::getApplication()->getMenu()->getItems('link','index.php?option=com_enmasse&view=dealtoday',true)->id;
			
			$link = 'index.php?option=com_enmasse&controller=deal&task=view&id=' . $oDeal->id ."&slug_name=" .$oDeal->slug_name ."&Itemid=$nItemId";
            if (!EnmasseHelper::is_urlEncoded($oDeal->pic_dir)) {
                $imageUrl = $oDeal->pic_dir;
            } else {
                $imageUrlArr = unserialize(urldecode($oDeal->pic_dir));
                $imageUrl = str_replace("\\", "/", $imageUrlArr[0]);
            }
            
            
		?>
		<div class="deal">
			<div class="image">
				<div class="inner">
					<a title="" href="<?php echo JRoute::_($link);?>"><img src="<?php echo $imageUrl?>" width="426"/></a>
				</div>
			</div>
			<div class="info">
				<div class="title">
					<a href="<?php echo JRoute::_($link);?>"><?php echo $oDeal->name?></a>
				</div>
				<div class="subtitle"><?php echo implode(", ", EnmasseHelper::getDealLocationNames($oDeal->id));?></div>
				<div class="description">
					<?php echo $oDeal->short_desc?>
				</div>
				<div class="timer">
					<span><?php echo JText::_('START_AT');?> <?php echo DatetimeWrapper::getDisplayDatetime($oDeal->start_at)?></span>
				</div>
				<div class="line"></div>
				<input name="" type="button" class="button" value="<?php echo JText::_('DEAL_LIST_VIEW_THIS_DEAL')?>" onclick="window.location.href='<?php echo JRoute::_($link)?>'" />
			</div>
		</div>
		<?php foreach ($dealList as $oDeal):?>
			<?php
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
			?>
			<div class="deal_small">
				<div class="image">
					<div class="inner">
						<a title="" href="<?php echo JRoute::_($link);?>"><img src="<?php echo $imageUrl?>" /></a>
					</div>
				</div>
				<div class="info">
					<div class="title">
						<div class="price-tag"></div>
						<a href="<?php echo JRoute::_($link);?>"><?php echo $sDealName?></a>
					</div>
					<div class="subtitle">
						<div class="apollo_info"><?php echo JText::_('DEAL_VALUE'); ?>: <b><?php echo EnmasseHelper::displayCurrency($oDeal->origin_price) ?> </b></div>
						<div class="apollo_info"><?php echo JText::_('DEAL_PRICE'); ?>: <b><?php echo EnmasseHelper::displayCurrency($oDeal->price) ?> </b></div>
	                </div>
	                <div class="apollpo_discount">
	                	<b><?php echo (100 - intval($oDeal->price / $oDeal->origin_price * 100)) ?>%</b>
	                </div>
					<input name="" type="button" class="button" value="<?php echo JText::_('DEAL_LIST_VIEW_THIS_DEAL')?>" onclick="window.location.href='<?php echo JRoute::_($link)?>'" />
	                <div class="line"></div>
	 			</div>
 			</div>
		<?php endforeach;?>
	<?php endif;?>
</div>

