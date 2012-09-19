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

define( '_JEXEC', 1 );
define('JPATH_BASE', dirname(__FILE__).'/../../../../../');
define( 'DS', DIRECTORY_SEPARATOR );

require_once (JPATH_BASE . DS . 'includes' . DS . 'defines.php');
require_once (JPATH_BASE . DS . 'includes' . DS . 'framework.php');

$mainframe = JFactory::getApplication('site');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>

<style type="text/css">
body {
	font: normal 13px Arial, Heltical, sans-serif;
	margin: 0;
	text-align: center;
}

#page {
	width: 600px;
	margin: 0 auto;
}

table {
	font: normal 13px Arial, Heltical, sans-serif;
}

h2 {
	font: bold 22px Verdana, Heltical, sans-serif;
	color: #a69;
	margin: 20px;
}

#wait,#error,#successful,#warn {
	font-size: 16px;
}

#error {
	color: red;
	font: normal 13px Arial, Heltical, sans-serif;
}

#successful {
	color: green;
}

#warn {
	color: orange;
}

#copyright {
	margin-top: 20px;
}
a {
	text-decoration: none;
	color: #123456;
	font-weight: bold;
}
</style>
</head>
<?php
// prepare data
    $id =  JRequest::getVar('artId');
    $db = JFactory::getDBO();
    $query = "SELECT * FROM #__content WHERE
              id = $id";
    $db->setQuery( $query ); 
    $row = $db->loadObject();
?>
<body>

<center>

<table width='100%' border='0' cellpadding="5" cellspacing="5">
    <tr>
        <td align="left"><b><h3><?php echo $row->title; ?></h3></b></th>
    </tr>
    <tr>
        <td >
        	<?php
        		$sPattern = '/(<img\s+src=")(.*)(")/i';
				$sReplace = '$1/$2$3';
				$sTmpl = preg_replace($sPattern, $sReplace, $row->introtext);
         		echo $sTmpl; 
         	?>
        </td>
    </tr>
   
</table>
</center>
</body>
</html>
