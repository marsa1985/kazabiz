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

//--------- add stylesheet and javascript
JFactory::getDocument()->addStyleSheet("components/com_enmasse/theme/".$theme ."/css/blinds.css");
JFactory::getDocument()->addStyleSheet('components/com_enmasse/theme/' . $theme . '/css/screen.css');
JFactory::getDocument()->addScript("components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js");
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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" >DD_roundies.addRule ('.descrip', '2px', true );</script>
<?php if(count($merchant->branches)>0):?>
	<?php JFactory::getDocument()->addScript("http://maps.google.com/maps/api/js?sensor=true");?>
	<script language="javascript">
		jQuery(document).ready( function(){
			initialize();
		})
	</script>
<?php endif;?>

<div class="deal">	
	<div class="main_deal">
		<div id="rss"><a href="components/com_enmasse/views/rss/today/"><img src="components/com_enmasse/theme/<?php echo $theme?>/images/rss.gif" alt="RSS Feed" title="RSS Feed"/></a></div>		
		<h2><?php echo $deal->name;?></h2>
		<h3></h3>
		<!-- Deal left begin -->
		<div class="deal_left">
			<div id="price_tag">
				<div id="price_tag_cont">
					<div id="amount"><?php echo EnmasseHelper::displayCurrency($deal->price)?></div>
					<?php if(strtotime($deal->end_at)   < strtotime(DatetimeWrapper::getDatetimeOfNow())):?>
						<div class="buy"><?php echo JText::_('BUY_DEAL_EXPIRED')?></div>
					<?php elseif(strtotime($deal->start_at) > strtotime(DatetimeWrapper::getDatetimeOfNow())):?>
						<div class="buy"><?php echo JText::_('BUY_DEAL_UPCOMING')?></div>
					<?php elseif ($deal->status == 'Voided'):?>
						<div class="buy"><?php echo JText::_('BUY_DEAL_VOIDED')?></div>
					<?php elseif($deal->max_coupon_qty != "-1" && $deal->cur_sold_qty >= $deal->max_coupon_qty):?>
						<div class="buy"><?php echo JText::_('BUY_DEAL_SOLD_OUT')?></div>
					<?php else :?>
                    	<a href="index.php?option=com_enmasse&controller=shopping&task=addToCart&dealId=<?php echo $deal->id .'&slug_name=' .$deal->slug_name;?>&referralid=<?php echo JRequest::getVar('referralid'); ?>" class="buy"><?php echo JText::_('Buy')?></a>
                    <?php endif;?> 				
                </div>
			</div>
			<div id="deal_discount">
				<dl>
					<dt><?php echo JText::_('DEAL_VALUE');?></dt>
					<dd><?php echo EnmasseHelper::displayCurrency($deal->origin_price)?></dd>
				</dl>
				<dl class="discount">
					<dt><?php echo JText::_('DEAL_DISCOUNT');?></dt>
					<dd><?php echo empty($deal->origin_price)? 100 : (100 - intval($deal->price/$deal->origin_price*100))?>%</dd>
 				</dl>
				<dl class="save">
					<dt><?php echo JText::_('DEAL_SAVE');?></dt>
 					<dd><?php echo EnmasseHelper::displayCurrency($deal->origin_price - $deal->price)?></dd>
				</dl>
			</div>
			<div id="for_a_friend">
				<a class="icon_gift" href="index.php?option=com_enmasse&controller=shopping&task=addToCart&dealId=<?php echo $deal->id ."&buy4friend=1&slug_name=" .$deal->slug_name;?>"><?php echo JText::_('BUY_IT_FOR_FRIEND')?></a>
			</div>
			<div id="remaining_time">
				<div class="countdown">
					<ul>
 						<li ><?php echo JText::_("DEAL_TIME_LEFT")?></li>
						<li class="counter">
							<span id="cday">00
							</span>
							<span id="chour">00
							</span>
							<span id="cmin">00
							</span>
							<span id="csec">00
							</span>
						</li>
					</ul>
				</div>
			</div>
			<div id="number_sold" >
				<?php if (isset($this->upcoming)):?>
					<h3><?php echo $deal->cur_sold_qty ." " .JText::_('DEAL_BOUGHT')?></h3>
					<div><?php echo JText::_('DEAL_IS_UPCOMMING')?></div>
				<?php elseif ($deal->status == 'Voided'):?>
					<h3><?php echo $deal->cur_sold_qty ." " .JText::_('DEAL_BOUGHT')?></h3>
					<div><?php echo JText::_('DEAL_IS_CANCEL')?></div>
				<?php else:?>
					<?php if($bDealOn = (($deal->min_needed_qty - $deal->cur_sold_qty) <= 0) ):?>
						<h3><?php echo JText::_('DEAL_IS_ON'); ?></h3>
					<?php endif;?>
					<?php if( !$bDealOn || ($deal->max_coupon_qty!="-1" && $deal->cur_sold_qty!=$deal->max_coupon_qty) ):?>
						<h3><?php echo $deal->cur_sold_qty?> <?php echo JText::_('DEAL_BOUGHT');?></h3>
						<div class="point">
							<div class="leftpoint"></div>
							<div class="centerpoint">
								<div class="leftvote"></div>
								<div style="width: <?php echo $bDealOn? 159 * ($deal->cur_sold_qty/$deal->max_coupon_qty) : 159 * ($deal->cur_sold_qty/$deal->min_needed_qty); ?>px;" class="centervote"></div>
			 					<div class="rightvote"></div>
		                    </div>
		                    <div class="rightpoint"></div>
						</div>
						<div class="point">
							<div class="leftnumber">0</div>
							<div class="rightnumber"><?php echo ($bDealOn? $deal->max_coupon_qty : $deal->min_needed_qty);?></div>
						</div>
					<?php endif;?>
					<?php if(!$bDealOn):?>
						<div><?php echo $deal->min_needed_qty - $deal->cur_sold_qty?> <?php echo JText::_('DEAL_NEED_MORE');?></div>
					<?php elseif($deal->max_coupon_qty!="-1" && $deal->cur_sold_qty!=$deal->max_coupon_qty):?>
						<div><?php echo $deal->cur_sold_qty . " " . JText::_('OF') . " " . $deal->max_coupon_qty . " " . JText::_('BOUGHT') . " - " . ($deal->max_coupon_qty - $deal->cur_sold_qty) . " " . JText::_('REMAINING')?></div>
					<?php elseif($deal->cur_sold_qty == $deal->max_coupon_qty):?>
						<div><?php echo JText::_('NO_COUPON_LEFT')?></div>
					<?php endif?>
				<?php endif;?>
			</div>
			<!-- Facebook and Twitter Post Buttons Start -->
			<div class="sharing">
				<h3><?php echo JText::_('SHARE_BOX_TITLE');?></h3>
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
			<!-- Facebook and Twitter Post Buttons End -->
		</div>
		<!-- Deal left end -->
		<div class="deal_right">
			<div class="slide">
				<?php if($deal->pic_dir):?>
					<?php if(count($imageUrlArr) == 1):?>
						<img width="<?php echo $image_width;?>" height="<?php echo $image_height;?>" src="<?php echo str_replace("\\","/",$imageUrlArr[0]);?>" alt="" />
					<?php else :?>
						<script type="text/javascript">
							var stop = false;
							var imgIndex = 0;
							window.setTimeout("beginSlide()", 4000);
							jQuery(document).ready(function () {
									// start the slideshow
									jQuery('.slideshow').blinds();
									jQuery('.slideshow').blinds_change(0);
									jQuery('.slide').mouseenter( function(){
										stop = true;
									});
									jQuery('.slide').mouseleave( function(){
										stop = false;
										beginSlide();
									});
									
							 })
							 function beginSlide()
							{
								if(!stop)
								{
									jQuery('.slideshow').blinds_change(imgIndex);
									imgIndex = (imgIndex + 1)% jQuery('.slideshow ul li').length;
									itv = window.setTimeout("beginSlide()", 4000);
								}
							}
							jQuery(window).focus(function (){ stop=false;beginSlide();});
							jQuery(window).blur(function (){ itv = clearTimeout(itv); stop=true;});
						</script>
						<div class="slideshow">
							<ul>
								<?php for ($i=0; $i<count($imageUrlArr);$i++):?>
									<li><img src="<?php echo str_replace("\\","/",$imageUrlArr[$i])?>" height="<?php echo $image_height?>" width="<?php echo $image_width?>" /></li>
								<?php endfor;?>
							</ul>
						</div>
						<div>
							<?php for ($i=0; $i<count($imageUrlArr);$i++):?>
								<a style="cursor:pointer" class="change_link" onclick="jQuery('.slideshow').blinds_change(<?php echo $i?>)"><?php echo $i+1;?></a>
							<?php endfor;?>
						</div>
					<?php endif;?>
				<?php endif;?>
			</div>
			<div class="digest">
				<span class="highlight"><?php echo strtok($deal->short_desc, ' ')?></span>
				<?php echo nl2br(strtok(''))?>
			</div>
		</div>
		
	</div>
	<div class="deal_bottom">
	</div>
</div>
<div class="deal_cont">
	<div class="deal_cont_main">
		<div id ="leftcol" class="deal_cont_main_left">
			<?php echo $deal->description?>
			<h3><?php echo JText::_('DEAL_HIGHLIGHT');?></h3>
			<?php echo $deal->highlight?>
			<h3><?php echo JText::_('DEAL_TERM_CONDITION');?></h3>
			<?php echo $deal->terms?>
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
                <input type="button" class="button" value="<?php echo JText::_('COMMENT_BUTTON');?>" onclick="window.location='<?php echo $sCommentLink; ?>'"></input>
			</div>
            <?php // ==== End Rating and Review ==== // ?>
        </div>
		<div id="rightcol" class="deal_cont_main_right">
			<?php if($merchant):?>
				<h4><?php echo $merchant->name?></h4>
				<?php if(!empty($merchant->description)):?>
					<div><?php echo nl2br($merchant->description)?></div>
				<?php endif;?>
				<?php if(!empty($merchant->web_url)):?>
					<a href="http://<?php echo str_replace('http://', '',$merchant->web_url)?>"><?php echo $merchant->web_url?></a>
				<?php endif;?>
				<?php if(!empty($merchant->branches) && count($merchant->branches) > 0):?>
					<div><?php echo JText::_('BRANCHES');?>:
						<select id="branchSelection" style="width: 150px; display:<?php echo count($merchant->branches)>1? "block" : "none"?>">
							<?php $count = 1; foreach ($merchant->branches as $branch):?>
								<option value="branch<?php echo $count?>" <?php echo ($count==1? " selected" : " ")?>><?php echo $branch['name']?></option>
								<?php $count++?>
							<?php endforeach;?>
						</select>
					</div>
					<!-- this script using for initialize google maps -->
					
					<script type="text/javascript">
					<?php
							//escape quote and carier return, otherwise it will cause javascript error at browser
							$pt = array('\\r\\n', '\\n', '\\', '\'');
							$rp = array( '', '','\\\\', '\\\'');
							$sJSON = str_replace($pt, $rp, json_encode($merchant->branches));
						?>
						var arMapDta = jQuery.parseJSON('<?php echo $sJSON?>');
						var arMap = new Array();
						var arMarker = new Array();
						var arPoint = new Array();
						function initialize()
						{
							
							var i = 0;
							for(o in arMapDta)
							{
								
								var oBranch = arMapDta[o];
								var iZoom = parseInt(oBranch.google_map_zoom);
								
								arPoint[i] = new google.maps.LatLng(parseFloat(oBranch.google_map_lat), parseFloat(oBranch.google_map_long));
								
								var oOption = { center: arPoint[i], zoom: iZoom, mapTypeId: google.maps.MapTypeId.ROADMAP };
								var sView = "map_canvas_" + (i+1);
								
								arMap[i] = new google.maps.Map( document.getElementById(sView), oOption);
								arMarker[i] = new google.maps.Marker({position: arPoint[i], map: arMap[i], title: oBranch.name});
					
								google.maps.event.addListener(arMap[i], 'zoom_changed', function() {
									var _this = this;
									setTimeout(function(){
										moveToDarwin(_this);	
									}, 3000);
									});	
								google.maps.event.addListener(arMarker[i], 'click', function() {
									  this.map.setZoom(this.map.zoom + 1);
									});
								i++;
							}
						}
					
						function moveToDarwin(map)
						{
							map.setCenter(arPoint[arMap.indexOf(map)]);
							
						}
					
						jQuery('document').ready(function() {
							var branchId = "";
							jQuery("select#branchSelection option:selected").each(function() {
								branchId = jQuery(this).val();
							});
							if(branchId != ""){	 
								jQuery("div#"+branchId).css("display","block");
							}
						});
						jQuery('#branchSelection').change(function() {
							var branchId = "";
							jQuery("select#branchSelection option:selected").each(function() {
								branchId = jQuery(this).val();
							});
							jQuery("div[id^='branch']").each(function(){
								jQuery(this).css("display","none");
							});
							if(branchId != ""){	 
								jQuery("div#"+branchId).css("display","block");
							}
						}).change();
					</script>
					
					<?php $count = 1;foreach ($merchant->branches as $branch):?>
						<div id="branch<?php echo $count?>" style="display:none">
							<div class="branch_title">
								<?php echo $branch['name']?>
							</div>
							<div class="branches">
								<?php if(isset($branch['description'])):?>
									<?php echo nl2br($branch['description'])?><br />
								<?php endif;?>
								<?php if(isset($branch['address'])):?>
									<?php echo nl2br($branch['address'])?><br />
								<?php endif;?>
								<?php if(isset($branch['telephone'])):?>
									<?php echo JTEXT::_('DEAL_MERCHANT_TEL') ?> : <?php echo $branch['telephone']?><br />
								<?php endif;?>
								<?php if(isset($branch['fax'])):?>
									<?php echo JTEXT::_('DEAL_MERCHANT_FAX') ?> : <?php echo $branch['fax']?><br />
								<?php endif;?>
								
							</div>
							<?php if($merchant->google_map_width!='' && $merchant->google_map_height!=''):?>
							<div id="map_canvas_<?php echo $count?>" align="center" style="width:<?php echo $merchant->google_map_width?>px; height:<?php echo $merchant->google_map_height?>px;"></div>
							<?php endif;?>
						</div>
						<?php $count++?>
					<?php endforeach;?>
				<?php endif;?>
			<?php endif;?>	
		</div>
	</div>
	<div  class="deal_cont_bottom">
	</div>
</div>

<!--Time Count Down Script--> 
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