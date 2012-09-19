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

JFactory::getDocument()->addScript("components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js");
JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict()');

$theme =  EnmasseHelper::getThemeFromSetting();
$oDeal = $this->objDeal;
$aComments = $this->aComments;
$app = JFactory::getApplication();
$app->setUserState('staticTitle', JText::_('COMMENT'));
?>
<script type="text/javascript" src="components/com_enmasse/theme/js/rating.js"></script>
<div class="row">	

	<div class="main_deal">
		<div class="deal_left">
			<div id="price_tag">
				<div id="price_tag_cont">
					
					
<?php 

if(strtotime($oDeal->end_at) < time()):
	$text = JText::_('BUY_DEAL_EXPIRED');
	elseif(strtotime($oDeal->start_at) > time()):
	$text =  JText::_('BUY_DEAL_UPCOMING');
	elseif ($oDeal->status == 'Voided'):
	$text =  JText::_('BUY_DEAL_VOIDED');
	elseif($oDeal->max_coupon_qty != "-1" && $oDeal->cur_sold_qty >= $oDeal->max_coupon_qty):
	$text = JText::_('BUY_DEAL_SOLD_OUT');
	else :
	$text = JText::_('Buy');
	$link ='index.php?option=com_enmasse&controller=shopping&task=addToCart&dealId='.$oDeal->id .'&slug_name=' .$oDeal->slug_name;
endif;?>
					<div class="row">
					 	<h4 class="comment_deal_name"><?php echo $oDeal->name; ?></h4>
					</div>
					<div align="center" class="row ">
					
						<span style="float:left"> <?php echo EnmasseHelper::displayCurrency($oDeal->price)?></span>
						
						<?php if(!empty($link)){?>
							<a style="float:right" href="<?php echo empty($link)?'#':$link;?>"><?php echo $text;?></a>
						<?php }else{ ?> 
							<span style="float:right;cursor:text;" ><?php echo $text;?> <?php echo JText::_('DEAL');?></span>
						<?php } ?>
						<div style="clear:both"></div>
					</div>
					
					
                </div>
			</div>
        </div>

            <a href="<?php echo JRoute::_('index.php?option=com_enmasse&controller=deal&task=view&id=' . $oDeal->id ."&slug_name=" .$oDeal->slug_name); ?>"><?php echo JText::_('RETURN_TO_DEAL'); ?></a>

    </div>
	<div class="deal_bottom">
	</div>
</div>

<div class="row">	

        <?php foreach($aComments as $aComment): ?>
        <?php
        $user = JFactory::getUser($aComment['user_id']);
        ?>
        <div class="comment_area">
            <p class="comment_content"><?php echo nl2br($aComment['comment']); ?></p>
            <p class="comment_details">
                <span class="rating disabled">
                    <?php for($i=1; $i<=$aComment['rating']; $i++): ?>
                        <span class="ratingStar filled">&nbsp;</span>
                    <?php endfor;?>
                    <?php for($i=$aComment['rating']; $i<5; $i++): ?>
                        <span class="ratingStar">&nbsp;</span>
                    <?php endfor;?>                            
                </span>                    
                <br />
                <span class="author"><?php echo $user->name; ?></span>
                <span>-</span>
                <span class="timestamp"><?php echo $aComment['created_at']; ?></span>
            </p>
        </div>
        <?php endforeach; ?>
        <?php if(JFactory::getUser()->get("guest")):
            $sRedirectUrl = base64_encode('index.php?option=com_enmasse&controller=deal&task=comment&id=' . $oDeal->id);
			$sLoginLink = JRoute::_("index.php?option=com_users&view=login&return=" . $sRedirectUrl, false);
        ?>
        <a class="sign_in_to_review" href="<?php echo $sLoginLink; ?>"><?php echo JText::_('SIGN_IN_TO_REVIEW'); ?></a> 
        <?php else: ?>
        <div class="post_review">
            <h4><?php echo JText::_('POST_REVIEW_TITLE'); ?></h4>
            <p class="rate_this_deal">
                <?php echo JText::_('RATE_THIS_DEAL'); ?>
                <a class="rating" href="#">
                    <span class="ratingStar">&nbsp;</span>
                    <span class="ratingStar">&nbsp;</span>
                    <span class="ratingStar">&nbsp;</span>
                    <span class="ratingStar">&nbsp;</span>
                    <span class="ratingStar">&nbsp;</span>
                </a>               
            </p>              
            <form id="review" name="review" method="post" action="index.php">
                <input type="hidden" name="option" value="com_enmasse" />
                <input type="hidden" name="controller" value="comment" />
                <input type="hidden" name="task" value="submit_review" />
                <input type="hidden" name="nDealId" value="<?php echo $oDeal->id; ?>" />
                <input type="hidden" id="nRating" name="nRating" value="" />
                <div class="review_form">
                    <textarea rows="3" id="sReviewBody" name="sReviewBody" cols="35" style="width:100%"></textarea>
                </div>
                
                <div id="review_errors" class="review_errors"></div>
               
                <input type="button" class="button_big" onclick="submit_form();" value="<?php echo JText::_('POST_REVIEW_BUTTON');?>"></input>
            </form>
        </div>
        <?php endif; ?>
  
	<div class="deal_bottom">
	</div>
</div>
<script type="text/javascript">
function submit_form()
{
    var form = document.review;
    if(form.nRating.value == '' || (form.nRating.value <= 0 && form.nRating.value > 5))
    {
        document.getElementById("review_errors").innerHTML = "<?php echo JText::_('PLEASE_RATE'); ?>";
        return false;
    }
    if(form.sReviewBody.value == '')
    {
        document.getElementById("review_errors").innerHTML = "<?php echo JText::_('PLEASE_ENTER_REVIEW'); ?>";
        return false;
    }
    form.submit();
}
</script>