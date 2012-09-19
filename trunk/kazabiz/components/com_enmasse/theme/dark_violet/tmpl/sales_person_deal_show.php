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
	
?>
<?php
//load submenu for sale person
$oldTpl = $this->setLayout('sales_person_sub_menu');
echo $this->loadTemplate();
$this->setLayout($oldTpl); 
?>
	<br/>
	<form action="<?php JRoute::_('index.php?option=com_enmasse$controller=salesPerson&task=dealShow')?>">
	
	<table class="noBorder">
		<tr>
			<td>
				<b><?php echo JText::_('DEAL_CODE');?>: </b>
				<input type="text" name="filter[code]" value="<?php echo $filter['code']; ?>" />
				<b><?php echo JText::_( 'DEAL_SEARCH_NAME' ); ?>: </b>
				<input type="text" name="filter[name]" value="<?php echo $filter['name']; ?>" />
				<b><?php echo JText::_( 'PUBLISHED' ); ?>: </b>
				<?php echo JHTML::_('select.genericList', $publishedJOptList, 'filter[published]', null , 'value', 'text', $filter['published']);?>
				<b><?php echo JText::_( 'STATUS' ); ?>: </b>
				<?php echo JHTML::_('select.genericList', $statusJOptList, 'filter[status]', null , 'value', 'text', $filter['status']);?>
	
				<input type="submit" value="<?php echo JTEXT::_('SALE_SEARCH_BUTTON');?>" /> 
				<input type="button" value="<?php echo JTEXT::_('SALE_RESET_BUTTON');?>" onClick="location.href='<?php echo JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=dealShow')?>'" />
			</td>
		</tr>
	</table>
	</form>
	
	
	<form action="index.php" method="post" name="adminForm" class="adminForm"><br />
	
	<table class="adminlist" width="97%">
		<thead>
			<tr>
				<th width="10"><?php echo '#' ; ?></th>
				<th width="10"><?php echo JText::_( 'DEAL_CODE' ); ?></th>
				<th width="250"><?php echo JText::_( 'DEAL_SEARCH_NAME' ); ?></th>
				<th width="5"><?php echo JText::_( 'MIN_QTY' ); ?></th>
				<th width="5"><?php echo JText::_( 'DEAL_BOUGHT' ); ?></th>
				<th width="5"><?php echo JText::_( 'PUBLISHED' ); ?></th>
				<th width="100"><?php echo JText::_( 'STATUS' ); ?></th>
				<th width="100"><?php echo JText::_( 'DEAL_LIST_OPTION_END_DATE' ); ?></th>
				<th width="80"><?php echo JText::_( 'CREATED_AT' ); ?></th>
				<th width="80"><?php echo JText::_( 'UPDATED_AT' ); ?></th>
			</tr>
		</thead>
		<?php
		for ($i=0; $i < $n=count( $rows ); $i++)
		{
			$k = $i % 2;
			
			$row = &$rows[$i];
			//$checked = JHTML::_('grid.id', $i, $row->id );
			$published = JHTML::_('grid.published', $row, $i );
			$link =  JRoute::_('index.php?option=' .$option .'&controller=salesPerson'.'&task=dealEdit&cid[]='. $row->id) ;
		?>
	
		<tr class="<?php echo "row$k"; ?>">
			<td name="number"><?php echo $i+1; ?></td>
			<td name="id"><a href="<?php echo $link; ?>"><?php echo $row->deal_code; ?></a></td>
			<td name="name"><a href="<?php echo $link; ?>"><?php echo $row->name; ?></a></td>
			<td name="min_needed_qty"><?php echo $row->min_needed_qty; ?></td>
			<td name="max_qty"><?php echo $row->cur_sold_qty; ?></td>
			<td align="center" name="published"><?php if($row->published){ echo "Published"; }else{ echo "Not Published";}; ?></td>
			<td name="status" align="center" ><?php echo JText::_('DEAL_'.str_replace(' ','_',$row->status)) ; ?></td>
			<td name="end_at"><?php echo DatetimeWrapper::getDisplayDatetime($row->end_at); ?></td>
			<td name="date"><?php echo DatetimeWrapper::getDisplayDatetime($row->created_at); ?></td>
			<td name="date"><?php echo DatetimeWrapper::getDisplayDatetime($row->updated_at); ?></td>
		</tr>
		<?php
		}
		?>
	</table>
	<input type="hidden" name="option" value="com_enmasse" /> 
	<input type="hidden" name="controller" value="salesPerson" /> 
	<input type="hidden" name="task" value="dealShow" />
</form>
