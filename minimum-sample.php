<?php
session_start();
if (isset($_POST['open'])) {
	$ret = exec('/usr/bin/eject -T /dev/sr0');
	$ret = ($ret == "") ? "Success." : "Failure.";
	$_SESSION['result'] = '<div id="result">Open ' . $ret . '</div>';
	header('Location:./');
	exit();
}
?>
<html>
<head>
	<meta charset="utf-8">
	<title>Web Eject</title>
</head>
<body>
	<h1>Web Eject Button</h1>
<?php
if (isset($_SESSION['result'])) {
	print $_SESSION['result'];
	$_SESSION['result'] = '';
}
?>
	<form action="./" method="post" id="aircon">
		<input type="submit" name="open" value="Ejectする">
	</form>
</body>
</html>

