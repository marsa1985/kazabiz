jQuery(function() {
    
    // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
    jQuery( "#dialog:ui-dialog" ).dialog( "destroy" );
    
    // modified text fields
    jQuery('#user_name_formAcymailing1').attr('size', 50);
    jQuery("#user_email_formAcymailing1").attr('size', 50)
    
    var name = jQuery("#user_name_formAcymailing1"),
    email = jQuery("#user_email_formAcymailing1"),
    location = jQuery("div#acymailing-container select#locationId"),
    allFields = jQuery( [] ).add( name ).add( email ).add( location ),
    tips = jQuery( ".validateTips" );
    
    function updateTips( t ) {
        tips
        .text( t )
        .addClass( "ui-state-highlight" );
        setTimeout(function() {
            tips.removeClass( "ui-state-highlight", 1500 );
        }, 500 );
    }

    function checkLength( o, n, min, max ) {
    	if ( o.val().length > max || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            updateTips( "Length of " + n + " must be between " +
                min + " and " + max + "." );
            return false;
        } else {
            return true;
        }
    }
    
    function checkRequiredField(o, n, hint){
    	if(o.val()== hint || o.val=='')
    	{
    		 o.addClass( "ui-state-error" );
             updateTips( "Field " + n + " is required ");
             return false;
    	}
    	return true;
    }

    function checkRegexp( o, regexp, n ) {
        if ( !( regexp.test( o.val() ) ) ) {
            o.addClass( "ui-state-error" );
            updateTips( n );
            return false;
        } else {
            return true;
        }
    }
    
    jQuery( "#dialog-form" ).dialog({
        autoOpen: false,
        height: 300,
        width: 350,
        modal: true,
        resizable: false,
        buttons: {
            "Submit Your Location": function() {
                var bValid = true;
                allFields.removeClass( "ui-state-error" );
                
                bValid = bValid && checkRequiredField (name, 'name', acymailing['NAMECAPTION']);//notice acymailing['NAMECAPTION'] was define in javascript of acymailing module
                bValid = bValid && checkLength( name, "name", 3, 16 );
                bValid = bValid && checkLength( email, "email", 6, 80 );
                
                //bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );
                // From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
                bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "Invalid email address" );
                //bValid = bValid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );
                
                // getting selectedIndex of the Selection and check validate
                
                bValid = bValid && checkLength( location, "location", 1, 10 );                    
                if ( bValid ) {
                    //@since: 20110711       
                    //@todo submit formdata for subscription
                    submit_multiform(location.val(), email.val());                
                }
            },
            Cancel: function() {
                jQuery( this ).dialog( "close" );
                window.location.href = "index.php?option=com_enmasse&view=dealtoday";
            }
        },
        close: function() {
            allFields.val( "" ).removeClass( "ui-state-error" );
            window.location.href = "index.php?option=com_enmasse&view=dealtoday";
        }
    });    
});

function on_change(selectobj){
    var hidSel = selectobj.options[selectobj.selectedIndex].value;
    jQuery('#hidSel').val(hidSel);
    return false;        
}

function submit_multiform(nlocationID, sEmail)
{
    var numberForms = document.forms.length;                                               
    var formIndex;
    for (formIndex = 0; formIndex < numberForms; formIndex++)
    {      
        formName = document.forms[formIndex].name.toString(); 
        if(formName.substring(0,14) == 'formAcymailing')
        {
            document.forms[formIndex].redirect.value = 'index.php?option=com_enmasse&controller=deal&task=dealSetLocationCookie&locationId='+ nlocationID + '&email=' + sEmail;
            try{
                return submitacymailingform('optin',formName);
            }catch(err){
                alert('The form could not be submitted');
                return false;
            }
        }
    }
}