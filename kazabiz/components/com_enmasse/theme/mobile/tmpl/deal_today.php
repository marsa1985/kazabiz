<?php
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");

$theme =  EnmasseHelper::getThemeFromSetting();

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
 //$locationJOptList = JHTML::_('select.genericList', $locationJOptList, 'locationId', $_COOKIE['CS_SESSION_LOCATIONID'] , 'value', 'text', $_COOKIE['CS_SESSION_LOCATIONID']);
?>
<div  class="row">
	<span class="deal_title"> <a href="<?php echo JRoute::_('index.php?option=com_enmasse&controller=deal&task=view&id=' . $deal->id ."&slug_name=" .$deal->slug_name); ?>"><?php echo $shareName;?></a></span>
</div>

<div  class="row">
<strong><?php echo JText::_('PRICE');?>: <?php echo EnmasseHelper::displayCurrency($deal->price)?></strong><br />
<strong><?php echo JText::_('DEAL_DISCOUNT');?> <?php echo empty($deal->origin_price)? 100 : (100 - intval($deal->price/$deal->origin_price*100))?>%</strong>
<strong><?php echo JText::_('DEAL_SAVE');?> <?php echo EnmasseHelper::displayCurrency($deal->origin_price - $deal->price)?></strong>
</div>

<div class="sharing">
				<strong><?php echo JText::_('SHARE_BOX_TITLE');?></strong> &nbsp;&nbsp;
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

<div class="counter row white" >
	<span id="cday">00</span> <?php echo JTEXT::_('DAYS');?>     <span id="chour">00</span> <?php echo JTEXT::_('HOURS');?> <span id="cmin">00</span> <?php echo JTEXT::_('MINUTES');?> <span id="csec">00</span> <?php echo JTEXT::_('SECONDS');?> <span><?php echo JTEXT::_('LEFT_TO_BUY');?></span>
</div>

<div align="center" class="row">
	<div class="slide">
				<?php if($deal->pic_dir):?>
					
						<img width="300" src="<?php echo $shareImages?>" alt="" />
					
				<?php endif;?>
			</div>
	
</div>


<?php if(strtotime($deal->end_at) < strtotime(DatetimeWrapper::getDatetimeOfNow())):
	$text =  JText::_('BUY_DEAL_EXPIRED');
	elseif(strtotime($deal->start_at) > strtotime(DatetimeWrapper::getDatetimeOfNow())):
	$text =  JText::_('BUY_DEAL_UPCOMING');
	elseif ($deal->status == 'Voided'):
	$text =  JText::_('BUY_DEAL_VOIDED');
	elseif($deal->max_coupon_qty != "-1" && $deal->cur_sold_qty >= $deal->max_coupon_qty):
	$text = JText::_('BUY_DEAL_SOLD_OUT');
	else :
	$canbuy = 1;
	$text = JText::_('Buy');
	$link ='index.php?option=com_enmasse&controller=shopping&task=addToCart&dealId='.$deal->id .'&slug_name=' .$deal->slug_name;
endif;?>


<div align="center" class="row">
	<img class="img_middle" src="components/com_enmasse/theme/<?php echo $theme?>/images/home_btn_checked.png" />
	<?php if( empty($bDealOn) || ($deal->max_coupon_qty!="-1" && $deal->cur_sold_qty!=$deal->max_coupon_qty) ):?>
		<strong><?php echo $deal->cur_sold_qty?> <?php echo JText::_('DEAL_BOUGHT');?> . <?php echo JTEXT::_('DEAL_IS_ON');?> 
		<?php if(!empty($canbuy)){?>
			&nbsp;&nbsp;<a href="index.php?option=com_enmasse&controller=shopping&task=addToCart&dealId=<?php echo $deal->id ."&buy4friend=1&slug_name=" .$deal->slug_name;?>"><?php echo JText::_('BUY_IT_FOR_FRIEND')?></a></strong>
		<?php }?>
	<?php endif;?>
	<strong></strong>
</div>

<div align="center" class="row btn_orange">
	<a class="deal_status" href="<?php echo empty($link)?'#':$link;?>"><?php echo $text;?></a>
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
<?php 
include_once 'functions.php';
$localist = getLocation();
$locationJOptList = array();
$emptyJOpt = JHTML::_('select.option', '', JText::_('') );
array_push($locationJOptList, $emptyJOpt);
$i=1;
$sLocationList = '';
foreach ($localist as $item)
{
	$sMyclass = ($i%2==0?'green_bg':'white_bg');
	$sLocationList .= '<div align="center" class="'.$sMyclass.'"><a href="Javascript:submit_form('.$item->id.')">'.$item->name.'</a></div>';
	$i++;
}
?>
<link rel="stylesheet" href="components/com_enmasse/theme/<?php echo $theme;?>/js/engine/css/vlightbox1.css" type="text/css" />
<link rel="stylesheet" href="components/com_enmasse/theme/<?php echo $theme;?>/js/engine/css/visuallightbox.css" type="text/css" />
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#closeLink').click(function(){
		jQuery('#overlay, #lightbox').css({'display':'none'});
	});

	jQuery('.btn_localtion a').click(function(){
		jQuery('#overlay, #lightbox').css({'display':'block'});
	});
	
	submit_form=function($id){
		jQuery(document.form_location.locationId).val($id);
		jQuery(document.form_location).submit();
	}
	
});
</script>

<div id="cover_list_location">

<div style="height:auto 100%; left: 0px; opacity: 0.7;display:none" id="overlay"></div>
<div style="left: 0px; top: 0px; width: 100%; height: 100%; overflow: hidden;display:none" id="lightbox">
<table cellspacing="0" style="opacity: 1;width:100%" id="outerImageContainer">
	<tbody>
		<tr>
			<td id="lightboxFrameBody">
			<div style="display: block;" class="clearfix" id="imageDataContainer">
			<div id="imageData">
			<div id="imageDetails">
				<form id="form_location" name="form_location"  action="index.php" method="post">
				<?php echo $sLocationList;?>
				 <input type="hidden" name="option" value="com_enmasse" />
			     <input type="hidden" name='controller' value="deal" />
			     <input type="hidden" name="task" value="dealSetLocationCookie" />
			     <input type="hidden" name="locationId" id="locationId" value="" />
				</form>
			</div>
			<div id="close"><a href="javascript:void(0);" id="closeLink"></a></div>
			</div>
			</div>
			</td>
		</tr>
	</tbody>
</table>
</div>
</div>