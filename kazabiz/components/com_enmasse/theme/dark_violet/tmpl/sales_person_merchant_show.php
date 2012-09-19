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
require_once(JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");

 	
$theme =  EnmasseHelper::getThemeFromSetting();
JFactory::getDocument()->addStyleSheet('components/com_enmasse/theme/' . $theme . '/css/screen.css');

$rows = $this->merchantList;

$option = 'com_enmasse';

$publishedJOptList = array();
array_push($publishedJOptList, JHTML::_('select.option', 1, JText::_('PUBLISHED') ));
array_push($publishedJOptList, JHTML::_('select.option', 0, JText::_('NOT_PUBLISHED') ));

//load submenu for sale person
$oldTpl = $this->setLayout('sales_person_sub_menu');
echo $this->loadTemplate();
$this->setLayout($oldTpl);
?>
<br/>
	
<form action="<?php echo JRoute::_('index.php')?>">
	
	<table class="noBorder">
		<tr>
			<td>
				<form action="index.php" name="filterForm">
					<input type="hidden" name="option" value="com_enmasse" />
					<input type="hidden" name="controller" value="salesPerson" />
					<input type="hidden" name="task" value = "merchantList"/>
					<b><?php echo JText::_('MERCHANT_NAME');?> : </b> <input type="text" name="filter[name]" value="<?php echo isset($this->filter['name'])? $this->filter['name'] : ""; ?>" />
					<input type="submit" value="<?php echo JText::_('MERCHANT_SEARCH');?>" />
					<input type="button" value="<?php echo JText::_('MERCHANT_RESET');?>" onClick="location.href='<?php echo JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=merchantList&filter[name]=&filter[status]=')?>'" />
				</form>
			</td>
		</tr>
	</table>
</form>
	
<form action="<?php echo JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=merchantList')?>" method="post" name="adminForm" class="adminForm">	
	<table class="adminlist">
		<thead>
			<tr>
				<th width="300"><?php  echo JHTML::_( 'grid.sort', JText::_('MERCHANT_NAME'), 'name', $this->order['order_dir'], $this->order['order']); ?></th>
				<th width="5" nowrap="nowrap"><?php echo JText::_('PUBLISHED');?></th>
				<th width="100"><?php  echo JHTML::_( 'grid.sort', JText::_('MERCHANT_USER_NAME'), 'user_name', $this->order['order_dir'], $this->order['order']); ?></th>
				<th width="100"><?php echo JText::_('MERCHANT_LOGO');?></th>
				<th><?php  echo JHTML::_( 'grid.sort', JText::_('CREATED_AT'), 'created_at', $this->order['order_dir'], $this->order['order']); ?></th>
			</tr>
		</thead>
		<?php
		for ($i=0; $i < $n=count( $rows ); $i++)
		{
			$k = $i % 2;
			$row = &$rows[$i];
			$checked = JHTML::_('grid.id', $i, $row->id );
			$published = JHTML::_('grid.published', $row, $i );
			$link =  JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=merchantEdit&cid[]='. $row->id) ;
			?>
		<tr class="<?php echo "row$k"; ?>">
			<td><a href="<?php echo $link?>"><?php echo $row->name; ?></a></td>
			<td align="center">
				<?php echo ($row->published == 1) ? JText::_('YES') : JText::_('NO')?>
			</td>
			<td><?php echo $row->user_name; ?></td>
			<td align="center">
			<?php 
				if(!empty($row->logo_url))
				{
					$imagePathArr = unserialize(urldecode($row->logo_url));
					$link =$imagePathArr[0];
					$link = JURI::base() . str_replace("\\","/",$link);
	               			
			?>
				<img height=40 src='<?php echo $link; ?>'/>
			<?php 
				}
			?>
			</td>
			<td><?php echo DatetimeWrapper::getDisplayDatetime($row->created_at); ?></td>
		</tr>
		<?php
		} 
		?>
		<tfoot>
	    <tr>
	      <td colspan="10" align="center"><?php echo $this->pagination->getListFooter(); ?></td>
	    </tr>
	  </tfoot>
	</table>
	<input type="hidden" name="filter_order" value="<?php echo $this->order['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->order['order_dir']; ?>" />
	<input type="hidden" name="filter[name]" value="<?php echo $this->filter['name']; ?>" />
</form>
