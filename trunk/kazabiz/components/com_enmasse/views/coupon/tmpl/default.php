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
$varList 		= $this->varList;
$elementList 	= $this->elementList;
if(!EnmasseHelper::is_urlEncoded($this->bgImageUrl))
 {
 	$bgImageUrl = $this->bgImageUrl;
 }
 else
 {
	$imageUrlArr= unserialize(urldecode($this->bgImageUrl));
	$bgImageUrl = str_replace("\\","/",$imageUrlArr[0]);
 }
 
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
<style type="text/css">

div#couponImage 
{
	z-index:-100px;
	width: 100%;
	height: 100%;   
    position: relative;
    overflow: hidden;
}
div#couponImage div#couponInfo 
{
	position:absolute;
	left:0px;
	top:0px;
	width: 100%;
	height: 100%;
	z-index:100px;
}
div#button{margin-top: 30px}
</style>
<script type="text/javascript" src="<?php echo JURI::base();?>/media/system/js/mootools-core.js"></script>
<script type="text/javascript" src="<?php echo JURI::base();?>/media/system/js/mootools-more.js"></script>
<div id="couponImage">
    <div class="floatBg">
    <?php
        if($bgImageUrl!= "")
            echo '<img src="' . JURI::base().'/'.$bgImageUrl . '" />';
    ?>        
    </div>
    <div id="couponInfo">
    <?php
    $body = "";
    for($i=0 ; $i < count($elementList); $i++)
    {
        if(!isset($varList[$elementList[$i]->name]))
        {
            if($elementList[$i]->name== 'serial')
            {
                if($varList[$elementList[$i]->name] == '' || $varList[$elementList[$i]->name] == null)
                {
                    $num = 'SERIAL';
                }
                else
                {
                    $num = 	$varList[$elementList[$i]->name];
                }
                $body.='<div id="'.$elementList[$i]->id.'" name="'.$elementList[$i]->name.'" style="position: absolute; left:' .$elementList[$i]->x.'px; top:'.$elementList[$i]->y.'px; font-size:'.$elementList[$i]->font_size.'px; width:'.$elementList[$i]->width.'px; height:'.$elementList[$i]->height.'px; overflow:hidden;">';
                    $body .= '<img src="'.JURI::base() .'index.php?option=com_enmasse&controller=barcode&task=generateBarcode&num='.$num.'"/>';
                $body.='</div>';	
            }
            else{

            $body.='<div id="'.$elementList[$i]->id.'" name="'.$elementList[$i]->name.'" style="border: red 2px dashed; position: absolute; left:' .$elementList[$i]->x.'px; top:'.$elementList[$i]->y.'px; font-size:'.$elementList[$i]->font_size.'px; width:'.$elementList[$i]->width.'px; height:'.$elementList[$i]->height.'px">';
                $body .= "[".strtoupper($elementList[$i]->name)."]";
            $body.='</div>';
            }
        }
        elseif($elementList[$i]->name== 'serial')
        {
            if($varList[$elementList[$i]->name] == '' || $varList[$elementList[$i]->name] == null)
            {
                $num = 'SERIAL';
            }
            else
            {
            $num = 	$varList[$elementList[$i]->name];
            }
            $body.='<div id="'.$elementList[$i]->id.'" name="'.$elementList[$i]->name.'" style="position: absolute; left:' .$elementList[$i]->x.'px; top:'.$elementList[$i]->y.'px; font-size:'.$elementList[$i]->font_size.'px; width:'.$elementList[$i]->width.'px; height:'.$elementList[$i]->height.'px;overflow:hidden;">';
                $body .= '<img src="'.JURI::base() .'index.php?option=com_enmasse&controller=barcode&task=generateBarcode&num='.$num.'"/>';
            $body.='</div>';	
        }
        else
        {
        $body.='<div id="'.$elementList[$i]->id.'" name="'.$elementList[$i]->name.'" style="position: absolute; left:' .$elementList[$i]->x.'px; top:'.$elementList[$i]->y.'px; font-size:'.$elementList[$i]->font_size.'px; width:'.$elementList[$i]->width.'px; height:'.$elementList[$i]->height.'px;overflow:hidden;">';
            $body .= $varList[$elementList[$i]->name];
        $body.='</div>';
        }
        
        /** phuocndt
         * Begin QR Code
         */
        if($elementList[$i]->name == 'qr_code')
        {
            if($varList[$elementList[$i]->name] == '' || $varList[$elementList[$i]->name] == null)
            {
                $val = 'COUPON_SERIAL';
            }
            else
            {
                $val = 	$varList[$elementList[$i]->name];
            }
            $body.='<div id="'.$elementList[$i]->id.'" name="'.$elementList[$i]->name.'" style="position: absolute; left:' .$elementList[$i]->x.'px; top:'.$elementList[$i]->y.'px; font-size:'.$elementList[$i]->font_size.'px; width:'.$elementList[$i]->width.'px; height:'.$elementList[$i]->height.'px;overflow:hidden;">';
                $body .= '<img src="'.JURI::base() .'index.php?option=com_enmasse&controller=qrcode&task=generateQrcode&val='.$val.'"/>';
            $body.='</div>';	
        }
    }
    echo $body;
    ?>
    </div>
</div>
<script type="text/javascript">
	function printContent(id){
		document.getElementById('button').style.display = 'none';
		window.print();
		document.getElementById('button').style.display = 'inline';
	}
</script>
<?php if(JRequest::getVar('editor')==true): ?>
<script type="text/javascript">
	function initCouponEditor(){
		var cont = $('couponInfo');
		if(!cont) return;
		
		//initialize
		var mdTop = 0;
		var mdLeft = 0;
		var mdWidth = 0;
		var mdHeight = 0;
		var curEl = null;
		//create resize icon
		var divResize = new Element('div', {
			'class': 'divResize',
			'styles': {
				'position': 'absolute',
				'bottom': 0,
				'right': 0,
				'width': 10,
				'height': 10,
				'cursor': 'se-resize'
			}
		});
		//inject resize icon
		var els = cont.getChildren();		
		els.each(function(el, index){
			var clone = divResize.clone();
			clone.inject(el);
			clone.addEvents({
				'mouseenter': function(){
				},
				'mouseleave': function(){
				},
				'mousedown': function(e){
					mdTop = e.client.y;
					mdLeft = e.client.x;
					mdWidth = this.getParent().getSize().x;
					mdHeight = this.getParent().getSize().y;
					this.getParent().resizing = true;
					curEl = this.getParent();
					curEl.setStyle('z-index', 1000);
					curEl.removeEvents();
				}				
			});
			el.setStyles({
				'cursor': 'move',
				'z-index': 999
			});
			el.makeDraggable();
		});
		cont.addEvents({
			'mousemove': function(e){
				if(curEl && curEl.resizing){
					var size = curEl.getSize();
					curEl.setStyles({
						'width': mdWidth + e.client.x - mdLeft,
						'height': mdHeight + e.client.y - mdTop
					});
				}
			},
			'mouseup': function(e){
				if(curEl){
					var size = curEl.getSize();
					curEl.setStyles({
						'width': mdWidth + e.client.x - mdLeft,
						'height': mdHeight + e.client.y - mdTop
					});
					curEl.resizing = false;
					curEl.removeEvents();
					curEl.makeDraggable();
					curEl.setStyle('z-index', 999);
					curEl = null;
				}
			}
		});
	}
	window.addEvent('domready', function(e){
		initCouponEditor();
	});
</script>
<?php endif; ?>
	<div id="button">
	<form>
		<input type="button" value="<?php echo JText::_('COUPON_PRINT_BUTTON');?>" onClick="printContent('content')">
	</form>
	</div>