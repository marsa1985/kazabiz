<script src="components/com_enmasse/script/jquery.js"></script>
/*------------------------------------------------------------------------
# En Masse - Social Buying Extension 2010
# ------------------------------------------------------------------------
# By Matamko.com
# Copyright (C) 2010 Matamko.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.matamko.com
# Technical Support:  Visit our forum at www.matamko.com
-------------------------------------------------------------------------*/

$option = 'com_enmasse';
$row = $this->salesPerson;
$version = new JVersion;
$joomla = $version->getShortVersion();
if(substr($joomla,0,3) >= '1.6'){
	?>
        <!--
        Joomla.submitbutton = function(pressbutton)
<?php
}else{
?>
    <script src="components/com_enmasse/script/jquery.js"></script>

	    
        <!--
        submitbutton = function(pressbutton)
<?php
}
?>
        {
    	    function validateEmail($email)
    	    {
    	    	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    	    	if( !emailReg.test( $email ) ) {
    	    		return false;
    	    	} else {
    	    		return true;
    	    	}
    	    }
    	    
            var form = document.adminForm;
            if (pressbutton == 'cancel')
            {
                submitform( pressbutton );
                return;
            }
            sName = jQuery.trim(form.name.value.replace(/(<.*?>)/ig,""));
            // do field validation
            if (sName == "")
            {
                alert( "<?php echo JText::_( 'INVALID_NAME', true ); ?>" );
            }
            else if (form.phone.value != "" && isNaN(form.phone.value))
            {
            	alert( "<?php echo JText::_( 'NUMBERIC_PHONE', true ); ?>" );
            }
            else if (form.email.value != "" && validateEmail(form.email.value) == false)
            {
            	alert( "<?php echo JText::_( 'INVALID_EMAIL', true ); ?>" );
            }            
            else
            {
           	 jQuery.post("index.php?option=com_enmasse&tmpl=component&controller=salesPerson&task=checkDuplicatedName", { saleName : sName },function(data) {
                   }
                	   else
                	   {
                		  submitform( pressbutton );
                    	}
              	   
                 });
            }
        }

        function checkValidUser()
		{
        	var form = document.adminForm;
        	var ob = document.getElementById('invalid_msg');
        	 jQuery.post("index.php?option=com_enmasse&tmpl=component&controller=salesPerson&task=checkUserName&userName="+form.user_name.value,function(data) {
        		    if(data == 'invalid')
		  			{
		  				form.user_name.focus();
		  				document.getElementById('duplicated_msg').style.display = "none";
		  			    ob.style.display = "block";
		  			}
        		    else if(data == 'duplicated' && form.user_name.value!=form.temp_user_name.value)
        		    {
        		    	form.user_name.focus();
        		    	ob.style.display = "none";
        		    	document.getElementById('duplicated_msg').style.display = "block";
            		}
		  			else
		  			{
		  				ob.style.display = "none";
		  				document.getElementById('duplicated_msg').style.display = "none";
       		  	    }
         	   
            });
		}
        //-->
        </script>
					if ($row->published == null)
					{
						echo JHTML::_('select.booleanlist', 'published',
								'class="inputbox"', 1);
					}
					else
					{
					?>
							'', JTEXT::_('SALE_PERSON_USER_NAME'));?>