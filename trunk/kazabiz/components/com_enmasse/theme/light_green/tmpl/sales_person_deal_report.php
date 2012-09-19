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
require_once(JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");
  	
$theme =  EnmasseHelper::getThemeFromSetting();
JFactory::getDocument()->addStyleSheet('components/com_enmasse/theme/' . $theme . '/css/screen.css');

$rows = $this->dealList;

$option = 'com_enmasse';
$filter = $this->filter;
$emptyJOpt = JHTML::_('select.option', '', JText::_('') );

// create list status for combobox
$statusJOptList = array();
array_push($statusJOptList, $emptyJOpt);
foreach ($this->statusList as $key=>$name)
{
	$var = JHTML::_('select.option', $key, JText::_('DEAL_'.str_replace(' ','_',$name)) );
	array_push($statusJOptList, $var);
}

$publishedJOptList = array();
array_push($publishedJOptList, $emptyJOpt);
array_push($publishedJOptList, JHTML::_('select.option', 1, JText::_('PUBLISHED') ));
array_push($publishedJOptList, JHTML::_('select.option', 0, JText::_('NOT_PUBLISHED') ));

// create list merchant for combobox
$merchantJOptList = array();
array_push($merchantJOptList, $emptyJOpt);
foreach ($this->merchantList as $item)
{
	$var = JHTML::_('select.option', $item->id, JText::_($item->name) );
	array_push($merchantJOptList, $var);
}

$itemId = JRequest::getVar('Itemid');

?>
	<h2 style="margin: 20px 0px 5px 20px; font-size: 28px;"><?php echo JText::_("SALE_REPORT");?></h2>
<script type="text/javascript">
	$.noConflict();
	jQuery(document).ready(function($){
		jQuery("#btnOk").click(function(){
			var filterName = jQuery("#filterName").val();
			var filterFromDate = jQuery("#filterFromDate").val();
			var filterToDate = jQuery("#filterToDate").val();
			//var isSubmit = false;
			
			if(filterFromDate == "" && filterToDate == ""){
				//isSubmit = true;
			} else if(filterFromDate > filterToDate){
				jQuery("#messageErrors").html("<font color='#55CCEE'><b style='padding-left:10px;'>The To Date should be greater than or equal to From Date</b></font>");
				jQuery("#filterFromDate").val("");
				jQuery("#filterToDate").val("");
				jQuery(".adminlist").hide();
				return false;
			} else if((filterFromDate !="" & filterToDate == "") || (filterFromDate =="" & filterToDate != "")){
				jQuery("#messageErrors").html("<font color='#55CCEE'><b style='padding-left:10px;'>The To Date or From Date should not be empty</b></font>");
				jQuery("#filterFromDate").val("");
				jQuery("#filterToDate").val("");
				jQuery(".adminlist").hide();
				return false;
			} else{
				//isSubmit = true;
			}
			
			if(filterName != ""){
				if(filterName.length < 8){
					jQuery("#messageErrors").html("<font color='#55CCEE'><b style='padding-left:10px;'>Search field should be at least 8 characters. Please try again</b></font>");
					jQuery(".adminlist").hide();
					return false;
				} else if(filterName.length > 255){
					jQuery("#messageErrors").html("<font color='#55CCEE'><b style='padding-left:10px;'>Search field should not be more than 255 characters. Please try again</b></font>");
					jQuery(".adminlist").hide();
					return false;
				} else {
					//isSubmit = true;					
				}
			} else{
				//isSubmit = true;
			}
			
		});
	});	
</script>
	<div id="messageErrors"></div>
	<br/>
<style type="text/css">
@media print {
	.header, .footer_full, .noBorder {display:none !important;}
	.maincol_full {border:0; border-radius:0;}
}
</style>	
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

	<form action="index.php" method="post" id="searchForm"> <!-- <?php echo JRoute::_('index.php?option=com_enmasse$controller=saleReports')?> -->
	
	<table class="noBorder">
		<tr>
			<td>
				<b><?php echo JText::_('DEAL_CODE');?>: </b>
				<input type="text" name="filter[code]" value="<?php echo $filter['code']; ?>" />
				
				<b><?php echo JText::_( 'DEAL_SEARCH_NAME' ); ?>: </b>
				<input type="text" name="filter[name]" id="filterName" value="<?php echo $filter['name']; ?>" />
				<b><?php echo JText::_('DEAL_MERCHANT');?>: </b>
				<?php echo JHTML::_('select.genericList', $merchantJOptList, 'filter[merchant_id]', null , 'value', 'text', $filter['merchant_id']);?>

				</br></br><b><?php echo JText::_( 'FROM_DATE' ); ?>: </b>
				
				<?php echo JHTML::_('calendar', '', "filter[fromdate]", "filterFromDate" , '%Y-%m-%d', 'readonly');?>
				<b><?php echo JText::_( 'TO_DATE' ); ?>: </b>
				<?php echo JHTML::_('calendar', '', "filter[todate]" , "filterToDate", '%Y-%m-%d', 'readonly');?>
	
				<input class="btn_style" type="submit" name="ok" id="btnOk" value="<?php echo JTEXT::_('SALE_SEARCH_BUTTON');?>" /> 
				<input class="btn_style" type="button" value="<?php echo JTEXT::_('SALE_RESET_BUTTON');?>" onClick="location.href='<?php echo JRoute::_('index.php?option=com_enmasse&controller=salereports&task=dealReport')?>'" />
		
			</td>

			<?php if(count($rows) >0) { ?>
			

			<td><input type="image" src="components/com_enmasse/theme/dark_blue/images/print.png" id="button" name="button" style="border:0" value="<?php echo JTEXT::_('Print Sale');?>" onClick="printContent('content')">

					<?php $filter = JRequest::getVar('filter'); ?>
					<b style=""><a href="index.php?option=com_enmasse&controller=salereports&task=createPdf&name=<?php echo $filter['name']; ?>&merchant_id=<?php echo $filter['merchant_id']; ?>&fromdate=<?php echo $filter['fromdate']; ?>&todate=<?php echo $filter['todate']; ?>&code=<?php echo $filter['code']; ?>" target="_blank">
					<img src="components/com_enmasse/theme/dark_blue/images/pdf.png"></a></b></td>
					<?php } ?>
		</tr>
	</table>
	<input type="hidden" name="option" value="com_enmasse" />
	<input type="hidden" name="controller" value="salereports" />
	<input type="hidden" name="task" value="dealReport" />
	<input type="hidden" name="Itemid" value="<?php echo $itemId;?>" />
	</form>	
	<form action="index.php" method="post" name="adminForm" class="adminForm"><br />

	<table class="adminlist" width="97%">
		<thead>
			<tr>
				<th width="10"><?php echo '#' ; ?></th>
				<th width="40"><?php echo JText::_( 'DEAL_CODE' ); ?></th>
				<th width="200"><?php echo JText::_( 'DEAL_SEARCH_NAME' ); ?></th>
				<th width="50"><?php echo JText::_( 'DEAL_MERCHANT' ); ?></th>
				<th width="40"><?php echo JText::_( 'DEAL_SOLD' ); ?></th>
				<th width="50"><?php echo JText::_( 'Unit Price' ); ?></th>
				<th width="40"><?php echo JText::_( 'TOTAL_SALES' ); ?></th>
			</tr>
		</thead>
<?php 
$total_amount = 0;

?>
		<?php
		
		for ($i=0; $i < $n=count( $rows ); $i++)
		{
			$k = $i % 2;
			
			$row = &$rows[$i];
			$merchant_name 	= JModel::getInstance('merchant','enmasseModel')->retrieveName($row->merchant_id);
			$total_sales = $row->price * $row->cur_sold_qty;
	
		?>
	
		<tr class="<?php echo "row$k"; ?>">
			<td align="center" name="number"><?php echo $i+1; ?></td>
			<td align="center" name="id"><?php echo $row->deal_code; ?></td>
			<td align="left" name="name"><?php echo $row->name; ?></td>
			<td align="center"><?php echo $merchant_name; ?></td>
			<td align="center" name="max_qty"><?php echo $row->cur_sold_qty; ?></td>
			<td align="center"><?php echo $this->currency_prefix.$row->price; ?></td>
			<td align="center" ><?php echo $this->currency_prefix.$total_sales; ?></td>
		</tr>

	<?php 
		$total_amount += $total_sales;
		}
	?>

		<tr>
			<td align="right" colspan="6" style="padding-right: 40px; border-bottom: 1px solid #FFFFFF !important; border-left: 1px solid #FFFFFF !important;text-align:right;"><b><?php echo JText::_( 'COMMISSION_TOTAL' ); ?></b></td>
			<td align="center" ><?php echo $this->currency_prefix.$total_amount; ?></td>
		</tr>

	</table>
	<input type="hidden" name="option" value="com_enmasse" /> 
	<input type="hidden" name="controller" value="salereports" /> 
	<input type="hidden" name="task" value="dealReport" /> 
	<input type="hidden" name="Itemid" value="<?php echo $itemId;?>" />
</form>
