<?php

$cid = JRequest::getVar('cid', array(), '', 'array');
JArrayHelper::toInteger($cid);

$salesPersonList = JModel::getInstance('salesPerson','enmasseModel')->listAllPublishedExcept($cid);
?>
<form action="index.php?option=com_enmasse&controller=salesPerson&task=unpublish" method="post" name="selectForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th class="left" colspan="2">
					<?php echo JText::_('SELECT_SALES_PERSON')?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo JText::_('SALE_PERSON_NAME')?></td>
				<td>
				<?php
				$id = "changeId";
				$attribs = "";
				$value = "id";
				$text = "name";
				$selected = $salesPersonList[0]->id;
				$select = JHTML::_('select.genericlist', $salesPersonList, $id, $attribs, $value, $text, $selected); 
				echo $select;
				?>
				</td>
			</tr>
		</tbody>
	</table>
	<div>
		<?php for ($i=0; $i<count($cid); $i++) {?>
			<input type="hidden" name="cid[]" value="<?php echo $cid[$i];?>"/>
		<?php }?>
		<input type="submit" value="Select" />
	</div>
</form>