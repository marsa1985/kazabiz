<body>
<form action='<?php echo "http://".$_SERVER['HTTP_HOST'];?>/~traderpo/new/discounts/index.php?option=com_enmasse&controller=payment&task=notifyUrl&payClass=realex' method='post' name='returnURLForm'>
<?php
//<form action='index.php?option=com_enmasse&controller=payment&task=notifyUrl&payClass=realex' method='post' name='returnURLForm'>
foreach ($_POST as $a => $b) {
	print("<input type='hidden' name='".$a."' value='".$b."'/>");
}
foreach ($_GET as $a => $b) {
	print("<input type='hidden' name='".$a."' value='".$b."'/>");
} 
?>
</form>
<script language="JavaScript">
document.returnURLForm.submit();
</script>
</body>
