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
//-------------------
// to re-define the link of server root

$temp_uri_arr =explode ('/',$_SERVER['REQUEST_URI'])  ;
$link_server = "";
 for($count = 0; $count < count($temp_uri_arr)-1; $count++)
 {
 	
 	if($temp_uri_arr[$count] == 'administrator' )
 	{
 		break ;
 	}
 	else if($temp_uri_arr[$count]!= '')
 	{
 	$link_server.= '/';
 	$link_server.=$temp_uri_arr[$count];	
 	}
 }
?>
<head>
	<style>
	#header-wrap{ display:none;}
	.menubar{ display:none;}
	#rightcol{ display:none;}
	#mainbody-right-only {width: 500px;background: none;}
	.bottom-right-only {display: none}
	.main-top-right-only {display: none}
	</style>
	<?php 
        $version = new JVersion;
        $joomla = $version->getShortVersion();
        //echo substr($joomla,0,3);
        if(substr($joomla,0,3) >= '1.6'){
    ?>
    	<script type="text/javascript">
	 var limit=0;
		function insertURL(val)
		{
			this.parent.document.getElementById(val).value= document.getElementById('image').value;
			 window.parent.SqueezeBox.close();
		}
		function addInput(divName){

		      if(limit < 4)
		      {
			      limit+=1;
		          var newdiv = document.createElement('div');
		          newdiv.innerHTML = " <br><input type='file' name='Filedata[]'>";
		          document.getElementById(divName).appendChild(newdiv);
		      }
		      else if(limit == 4)
		      {
		    	  limit+=1;
		    	  var newdiv = document.createElement('div');
		          newdiv.innerHTML = "<b>Limmited Images Upload !</b>";
		          document.getElementById(divName).appendChild(newdiv);
			  }
	     
	}
	</script>
    <?php }else {?>
	<script type="text/javascript">
	 var limit=0;
		function insertURL(val)
		{
			this.parent.document.getElementById(val).value= document.getElementById('image').value;
			window.parent.document.getElementById( 'sbox-window' ).close();
		}
		function addInput(divName){

		      if(limit < 4)
		      {
			      limit+=1;
		          var newdiv = document.createElement('div');
		          newdiv.innerHTML = " <br><input type='file' name='Filedata[]'>";
		          document.getElementById(divName).appendChild(newdiv);
		      }
		      else if(limit == 4)
		      {
		    	  limit+=1;
		    	  var newdiv = document.createElement('div');
		          newdiv.innerHTML = "<b>Limmited Images Upload !</b>";
		          document.getElementById(divName).appendChild(newdiv);
			  }
	     
	}
	</script>
	<?php }?>
</head>
<body style="background: none;">
<script src="components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js"></script>
<script>
function submitForm()
{
	var invalid = false;
    var errorMessage = '';
    jQuery("input[name='Filedata[]']").each(function()
    {
    	var ext = $(this).val().split('.').pop().toLowerCase();
    	if($.inArray(ext, ['png','jpg','jpeg']) == -1) {
        	invalid = true;
            errorMessage = "<?php echo JText::_('INVALID_EXTENSION'); ?>";
    	}
        if($(this).val() == '') {
         	invalid = true;
            errorMessage = "<?php echo JText::_('PLEASE_CHOOSE_IMAGE'); ?>";           
        }
    });
    if(invalid==true)
    {
        alert(errorMessage);
        return false;
    }
    else
    {
        return true;
    }    
}
</script>
	<form action="<?php echo JURI::base(); ?>index.php?option=com_enmasse&controller=uploader&task=upload&tmpl=component" name ="uploadForm" id="uploadForm" method="post" enctype="multipart/form-data" onSubmit="return submitForm()">
						<fieldset>							
							<legend><?php echo JText::_( 'UPLOAD_FILE_TITLE' ); ?> </legend>
							<?php echo JText::_( 'ALLOWED_IMAGE_EXTENSION' ); ?>
							<fieldset class="actions">
							    <div id="dvFile">
									<input type="file" name="Filedata[]" />
								</div>
                                
								<?php if($this->parent == 'deal'): ?>
								<input type="button" value="<?php echo JTEXT::_('ADD_MORE_IMG');?>" onclick="addInput('dvFile');" />
                                <?php endif; ?>
								
								<input type="submit" id="file-upload-submit" value="<?php echo JText::_('UPLOAD_FILE_BUTTON'); ?>"/>
								<span id="upload-clear"></span>
							</fieldset>
							
							<ul class="upload-queue" id="upload-queue">
								<li style="display: none" />
							</ul>
							
							</fieldset>
							
						<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_enmasse&controller=uploader&task=display&tmpl=component'); ?>" />
						<input type="hidden" name="parentId" value="<?php echo $this->parentId;?>" />
							<legend><?php echo JText::_( 'Review' ); ?> </legend>
							<?php 
							 if(!empty($this->imageUrl))
							 {
							    $imagePathArr = unserialize(urldecode($this->imageUrl));
							 	for($i=0 ; $i< count($imagePathArr); $i++)
							 	{
							 	    
								 	$link='http://';
								 	$link.= $_SERVER["SERVER_NAME"].$link_server.DS;
								 	$link.=$imagePathArr[$i];
								 	$link =str_replace("\\","/",$link);
								 	$imageLinkArr[$i] = $link;
							 	}
							 	
							 	?>
							 	
							 	<input type="text"  size ="80" name="image" id='image' value="<?php echo urlencode($this->imageUrl);?>"/>
							 	<input type="button" onclick="insertURL('<?php echo $this->parentId; ?>');" value="OK" />
                                <br/>
							 	<?php 
							 	for($z=0 ; $z < count($imageLinkArr); $z++)
							 	{
							 		echo '<img width="200" hieght="100" src="'.$imageLinkArr[$z].'"/>&nbsp;';
							 		if( $z%2 == 1)
							 		{echo '<br><br>';}
							 	}
							 	
							 	
							 	
							 }
							?>
	
	</form>
</body>
