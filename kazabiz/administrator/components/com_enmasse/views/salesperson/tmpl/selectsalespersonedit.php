<?php

$id = JRequest::getVar('id', array(), '', 'array');
JArrayHelper::toInteger($id);

$data = $_POST;

$salesPersonList = JModel::getInstance('salesPerson','enmasseModel')->listAllPublishedExcept($id);
?>
<form action="index.php?option=com_enmasse&controller=salesPerson&task=unpublishEdit" method="post" name="selectForm">
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
		<?php foreach ($data as $key => $value) {
			if ($key != "task" && $key != "view" && $key != "layout") {
		?>
				<input type="hidden" name="<?php echo $key;?>" value="<?php echo$value;?>"/>
		<?php 
			}
		}?>
		<input type="hidden" name="task" value="unpublishEdit"/>
		<input type="submit" value="Select" />
	</div>
</form>
