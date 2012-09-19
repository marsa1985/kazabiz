<?php
/* ------------------------------------------------------------------------
  # En Masse - Social Buying Extension 2010
  # ------------------------------------------------------------------------
  # By Matamko.com
  # Copyright (C) 2010 Matamko.com. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.matamko.com
  # Technical Support:  Visit our forum at www.matamko.com
  ------------------------------------------------------------------------- */

// create list location for combobox
$locationJOptList = array();
$emptyJOpt = JHTML::_('select.option', '', JText::_('SUBSCR_CHOOSE_ONCE_LOCATION'));
array_push($locationJOptList, $emptyJOpt);
//$data = $this->data;
foreach ($data->locationList as $item) {
    $var = JHTML::_('select.option', $item->id, JText::_($item->name));
    array_push($locationJOptList, $var);
}

?>
<script language="javascript" type="text/javascript" src="components/com_enmasse/theme/<?php echo EnmasseHelper::getThemeFromSetting();?>/script/jquery-1.4.1.js"></script>
<div id='subscription_choose_list'>
    <form action='index.php' name="frmSubsc" id="frmSubsc">
        <input type="hidden" name="option" value="com_enmasse" />
        <input type="hidden" name='controller' value="deal" />
        <input type="hidden" name="task" value="dealSetLocationCookie" />
        <input type="hidden" name='hidSel' value="" id="hidSel"/>
        <?php echo JHTML::_('select.genericList', $locationJOptList, 'locationId', 'onchange=on_change(this);', 'value', 'text', ''); ?>
    </form>
</div>
<div class="bottom_subscription_page">    
    <input id="btnNext" style="width:100px;float:right;margin:10px 20px 10px 10px;cursor:pointer" class="cartbutton" value='<?php echo JText::_('SUBSCR_MOVE_NEXT');?>' onclick="subscriptLocation();" />
    <input id="btnSkip" style="width:100px;float:right;margin:10px 20px 10px 10px;cursor:pointer" class="cartbutton" value='<?php echo JText::_("SKIP_THIS_STEP"); ?>' onclick="return on_skip();" />
</div>
<script lang="javascript">

// Fix bug for no selected items.
function subscriptLocation(){
    
    var index = $('#hidSel').val();
    if(index==0 || index == undefined || index=='' || index==null){
        window.location.href='index.php?option=com_enmasse&view=dealtoday';
    }
    
    var frm = document.getElementById('frmSubsc');
    frm.submit();
}

function on_skip(){
        window.location.href='index.php?option=com_enmasse&view=dealtoday';
}

function on_change(selectobj){
   var hidSel = selectobj.options[selectobj.selectedIndex].value;
   $('#hidSel').val(hidSel);
   return false;        
}
 
$(document).ready(function(){        
    $('#choose_location_title').html('<?php echo JText::_('MSG_CONFIRM_YOUR_LOCATION');?>');
});

</script>