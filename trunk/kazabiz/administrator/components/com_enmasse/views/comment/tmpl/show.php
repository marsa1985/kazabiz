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

$rows = $this->commentList;
$option = 'com_enmasse';
JHTML::_( 'behavior.modal' );
?>
<div>
<form action="index.php" method="post" name="adminForm">
<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th width="10%"><?php  echo JText::_('MERCHANT_USER_NAME'); ?></th>
			<th><?php echo JText::_('MERCHANT_SETTLEMENT_DEAL_NAME')?></th>
            <th width="50%"><?php echo JText::_('COMMENT_CONTENT')?></th>
            <th width="1%"><?php echo JText::_('COMMENT_RATING')?></th>
            <th><?php echo JText::_('COMMENT_STATUS')?></th>
            <th width="15%"><?php  echo JHTML::_( 'grid.sort', JText::_('CREATED_AT'), 'created_at', $this->order['order_dir'], $this->order['order']); ?></th>
		</tr>
	</thead>
	<?php
	for ($i=0; $i < count( $rows ); $i++)
	{
		$k = $i % 2;
		$row = &$rows[$i];
		$checked = JHTML::_('grid.id', $i, $row->id );
        $user = JFactory::getUser($row->user_id);
        $sDealName = JModel::getInstance('Deal', 'EnmasseModel')->getById($row->deal_id)->name;
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td><?php echo $checked; ?></td>
		<td><?php echo $user->name; ?></td>
		<td><?php echo $sDealName; ?></td>
        <td><?php echo nl2br($row->comment); ?></td>
        <td><?php echo $row->rating; ?>/5</td>
        <td>
            <?php
            switch($row->status)
            {
                case '0':
                    echo JText::_('COMMENT_PENDING_STATUS');
                    break;
                case '1':
                    echo JText::_('COMMENT_UNPUBLISHED_STATUS');
                    break;                
                case '2':
                    echo JText::_('COMMENT_PUBLISHED_STATUS');
                    break;
                case '3':
                    echo JText::_('COMMENT_SPAMMED_STATUS');
                    break;
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
      <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  </tfoot>
</table>
<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="controller" value="comment" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->order['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->order['order_dir']; ?>" />
</form>
</div>
