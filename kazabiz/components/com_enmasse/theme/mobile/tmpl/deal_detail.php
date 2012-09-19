<?php
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");

$theme =  EnmasseHelper::getThemeFromSetting();//getThemeFromSetting();

//--------- add stylesheet and javascript
JFactory::getDocument()->addStyleSheet("components/com_enmasse/theme/".$theme ."/css/blinds.css");
JFactory::getDocument()->addScript("components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js");
JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict()');
JFactory::getDocument()->addScript("components/com_enmasse/theme/js/DD_roundies_0.0.2a-min.js");
JFactory::getDocument()->addScript("components/com_enmasse/theme/js/jquery/jquery.blinds-0.9.js");

//set default timezone
DatetimeWrapper::setTimezone(DatetimeWrapper::getTimezone());

$deal = $this->deal;
$merchant = $deal->merchant;
//------- to set the meta data and page title for SEO
$document = JFactory::getDocument();
$document->setMetadata('Keywords',  $deal->name);

$version = new JVersion;
$joomla = $version->getShortVersion();
if(substr($joomla,0,3) >= 1.6){
    $document   = JFactory::getDocument();
    $document->setTitle( $deal->name );
}else{
    $mainframe->setPageTitle($deal->name);
      
}

// load the deal image size
$dealImageSize = EnmasseHelper::getDealImageSize();
if(!empty($dealImageSize))
{
	$image_height = $dealImageSize->image_height;
	$image_width = $dealImageSize->image_width;
}
else
{
	$image_height = 252 ;
	$image_width = 400;
}

//contruct deal image url 
$imageUrlArr = array();
if(EnmasseHelper::is_urlEncoded($deal->pic_dir)){
	$imageUrlArr = unserialize(urldecode($deal->pic_dir));
}else{
	$imageUrlArr[0] = $deal->pic_dir;
}

//contruct data for social network sharing
$oMenu = JFactory::getApplication()->getMenu();
$oItem = $oMenu->getItems('link','index.php?option=com_enmasse&view=dealtoday',true);
$user = JFactory::getUser();
$userID = $user->get('id');
$shareName = $deal->name;
$shareUrl = JURI::base() . 'index.php?option=com_enmasse&controller=deal&task=view&id='. $deal->id . '&slug_name=' . $deal->slug_name . '&Itemid=' . $oItem->id;
if($userID!='0')
{
	$shareUrl .= '&referralid='.$userID;
}
$shareShortDesc = str_replace("\"", "'", $deal->short_desc);
$shareImages = JURI::base(). str_replace("\\","/",$imageUrlArr[0]);

?>
<div  class="row">
	<span class="deal_title"><?php echo $shareName;?></span>
</div>

<div  class="row">
<strong><?php echo JText::_('PRICE');?>: <?php echo EnmasseHelper::displayCurrency($deal->price)?></strong><br />
<strong><?php echo JText::_('DEAL_DISCOUNT');?> <?php echo empty($deal->origin_price)? 100 : (100 - intval($deal->price/$deal->origin_price*100))?>%</strong>. 
<strong><?php echo JText::_('DEAL_SAVE');?> <?php echo EnmasseHelper::displayCurrency($deal->origin_price - $deal->price)?></strong>
</div>

<div align="center" class="row">
		<div class="slide">
				<?php if($deal->pic_dir):?>
					
						<img width="300" src="<?php echo $shareImages?>" alt="" />
					
				<?php endif;?>
			</div>
</div>
<?php if(strtotime($deal->end_at)  < strtotime(DatetimeWrapper::getDatetimeOfNow())):
	$text = JText::_('BUY_DEAL_EXPIRED');
	elseif(strtotime($deal->start_at) > strtotime(DatetimeWrapper::getDatetimeOfNow())):
	$text =  JText::_('BUY_DEAL_UPCOMING');
	elseif ($deal->status == 'Voided'):
	$text =  JText::_('BUY_DEAL_VOIDED');
	elseif($deal->max_coupon_qty != "-1" && $deal->cur_sold_qty >= $deal->max_coupon_qty):
	$text = JText::_('BUY_DEAL_SOLD_OUT');
	else :
	$text = JText::_('Buy');
	$canbuy = 1;
	$link ='index.php?option=com_enmasse&controller=shopping&task=addToCart&dealId='.$deal->id .'&slug_name=' .$deal->slug_name;
endif;?>

 <div align="center" class="row">

    <div class="row">
	<img class="img_middle" src="components/com_enmasse/theme/<?php echo $theme?>/images/home_btn_checked.png" />
	<?php if( empty($bDealOn) || ($deal->max_coupon_qty!="-1" && $deal->cur_sold_qty!=$deal->max_coupon_qty) ):?>
		<strong><?php echo $deal->cur_sold_qty?> <?php echo JText::_('DEAL_BOUGHT');?></strong>
		<span class="counter" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<span id="cday">00</span> <?php echo JTEXT::_('DAYS');?> <span id="chour">00</span>:<span id="cmin">00</span>:<span id="csec">00</span> <span><?php echo JTEXT::_('LEFT');?></span>
		</span>
		
	<?php endif;?>	
	</div>
</div>
			
<div align="center" class="row btn_orange">
	<?php if(!empty($link)){?>
		<a class="deal_status" href="<?php echo empty($link)?'#':$link;?>"><?php echo $text;?></a>
	<?php }else{ ?> 
		<a class="deal_status deal_status_inactive" ><?php echo $text;?></a>
	<?php } ?>
</div>

<?php if(!empty($canbuy)){?>
<div class="row">
	<strong><a href="index.php?option=com_enmasse&controller=shopping&task=addToCart&dealId=<?php echo $deal->id ."&buy4friend=1&slug_name=" .$deal->slug_name;?>"><?php echo JText::_('BUY_IT_FOR_FRIEND')?></a></strong>
</div>
<?php } ?>

<div class="sharing">
				<strong style="display:block"><?php echo JText::_('SHARE_BOX_TITLE');?></strong>
				<a href="http://www.facebook.com/share.php?u=<?php echo urlencode($shareUrl); ?>" target="blank"><img src="components/com_enmasse/images/social_media/facebook.png"></a>
				<a href="http://twitter.com/share?url=<?php echo urlencode($shareUrl); ?>" class="" data-url="" data-text="<?php echo $shareShortDesc; ?>" data-count="none" data-via="<?php echo $shareName; ?>" target="_blank"><img src="components/com_enmasse/images/social_media/twitter.png"></a>
				<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>					
				<script language="JavaScript" type="text/javascript">
					function mailToFriend() {
						window.open ("index.php?option=com_enmasse&controller=mail&task=mailForm&tmpl=component&dealid=<?php echo $deal->id; ?>&userid=<?php echo $userID; ?>&itemid=<?php echo $oItem->id; ?>", "mywindow","location=0,status=0,scrollbars=0, width=500,height=400");
					}
				</script>                    
				<a href="javascript:void mailToFriend()"><img src="components/com_enmasse/images/social_media/email.png"></a>
	</div>

<?php if($merchant){?>
<div class="row">
				<h4><?php echo $merchant->name?></h4>
				<?php if(!empty($merchant->description)):?>
					<div><?php echo nl2br($merchant->description)?></div>
				<?php endif;?>
				<?php if(!empty($merchant->web_url)):?>
					<a href="http://<?php echo str_replace('http://', '',$merchant->web_url)?>"><?php echo $merchant->web_url?></a>
				<?php endif;?>
				
			<br />
			<br />
</div>
<?php }?>

<div  class="row">
 <?php echo $deal->description?>
 </div>
 <div  class="row">
	<strong><?php echo JText::_('DEAL_HIGHLIGHT');?></strong>
	<?php echo $deal->highlight?>
</div>
<div  class="row">
	<strong><?php echo JText::_('DEAL_TERM_CONDITION');?></strong>
	<?php echo $deal->terms?>
</div>
<div  class="row">
            <?php // ==== Start Rating and Review ==== //
			$nItemId = JFactory::getApplication()->getMenu()->getItems('link','index.php?option=com_enmasse&view=dealtoday',true)->id;
            $sCommentLink = JRoute::_('index.php?option=com_enmasse&controller=deal&task=comment&id=' . $deal->id . '&Itemid=' . $nItemId, false);
            ?>
            <?php
            $nRating = EnmasseHelper::countDealRatingByDealId($deal->id);
            $nRating = round($nRating);
            ?>
            <p class="average_rating"><?php echo JText::_('AVERAGE_RATING');?>
            <span class="rating">                
                <?php for($i = 1; $i <= $nRating; $i++): ?>
                    <span class="ratingStar filled">&nbsp;</span>
                <?php endfor;?>
                <?php for($i = $nRating; $i < 5; $i++): ?>
                    <span class="ratingStar">&nbsp;</span>
                <?php endfor;?>
            </span> 
            </p>
            <div class="comment_button">
            	<br />
                <button class="button_big"  onclick="window.location='<?php echo $sCommentLink; ?>'" style="font-size:100%;"><?php echo JText::_('COMMENT_BUTTON');?></button>
			</div>

</div>



<script language="JavaScript">
	TargetDate = "<?php echo date('Y/m/d H:i:s', strtotime($deal->end_at));?>";
    CurrentDate = "<?php echo date('Y/m/d H:i:s', strtotime(DatetimeWrapper::getDatetimeOfNow())); ?>";
	CountActive = true;
	CountStepper = -1;
	LeadingZero = true;
	
	function calcage(secs, num1, num2) {
	  s = ((Math.floor(secs/num1))%num2).toString();
	  if (LeadingZero && s.length < 2)
	    s = "0" + s;
	  return  s ;
	}
	
	function CountBack(secs) {
	  if (secs < 0) {
	    return;
	  }
	  document.getElementById("cday").innerHTML = calcage(secs,86400,100000)+" <?php echo JTEXT::_('DAY');?>";
	  document.getElementById("chour").innerHTML = calcage(secs,3600,24)+" <?php echo JTEXT::_(':');?>";
	  document.getElementById("cmin").innerHTML = calcage(secs,60,60)+" <?php echo JTEXT::_(':');?>";
	  document.getElementById("csec").innerHTML = calcage(secs,1,60);
	
	  if (CountActive)
	    setTimeout("CountBack(" + (secs+CountStepper) + ")", SetTimeOutPeriod);
	}
	
	if (typeof(TargetDate)=="undefined")
	  TargetDate = "12/31/2020 5:00 AM";
	if (typeof(CountActive)=="undefined")
	  CountActive = true;
	if (typeof(FinishMessage)=="undefined")
	  FinishMessage = "";
	if (typeof(CountStepper)!="number")
	  CountStepper = -1;
	if (typeof(LeadingZero)=="undefined")
	  LeadingZero = true;
	
	
	CountStepper = Math.ceil(CountStepper);
	if (CountStepper == 0)
	  CountActive = false;
	var SetTimeOutPeriod = (Math.abs(CountStepper)-1)*1000 + 990;
	
	var dthen = new Date(TargetDate);
	var dnow = new Date(CurrentDate);
	
	if(CountStepper>0)
	  ddiff = new Date(dnow-dthen);
	else
	  ddiff = new Date(dthen-dnow);
	
	gsecs = Math.floor(ddiff.valueOf()/1000);
	
	CountBack(gsecs);
</script>
