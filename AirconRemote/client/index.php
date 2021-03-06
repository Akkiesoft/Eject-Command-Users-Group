<?php

session_start();

$trayurl	= 'http://192.168.1.11/cdtray.php?';
$redirectTo	= './';

if (isset($_POST['open'])) {
	$_SESSION['result'] = '<ul id="result"><li>Open ' . file_get_contents($trayurl . "api=1&tray=1") . '</li></ul>';
	header('Location:' . $redirectTo);
	exit();
}

if (isset($_POST['timerset'])) {
	$time = intval($_POST['time']);
	$message = '';
	if ($time) {
		$result = file_get_contents($trayurl . "api=1&timer=" . $time);
		$message = $time . '分後にボタンを押します。<br><small>詳細: ' . htmlspecialchars($result) . '</small>';
	}
	else { $message = '時間の指定が不正です。。'; }
	$_SESSION['resultTimer'] = '<ul id="result"><li>' . $message . '</li></ul>';
	header('Location:' . $redirectTo);
	exit();
}

$temp = file_get_contents($trayurl . "api=1&temp=1");
$tempicon = "accept.png";
if ($temp < 18) { $tempicon = "delete.png"; }
if (17 < $temp) { $tempicon = "error.png";  }
if (21 < $temp) { $tempicon = "accept.png"; }
if (28 < $temp) { $tempicon = "error.png";  }
if (30 < $temp) { $tempicon = "delete.png"; }

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta name="viewport" content="width=320px,user-scalable=no" />
	<title>エアコンのリモコン</title>
	<style type="text/css">
*	{font-family:'メイリオ',sans-serif;padding:0;margin:0;}
html{background:#ccc;}
body{margin:0 auto;width:320px;background:#fff;}
h1	{
	padding		:5px 0;
	background	:#ddeeff;
	text-align	:center;
}
section {
	padding:10px;
}

#temp	{
	margin-left	:0.5em;
	font-size	:20pt;
}

h2	{margin:0 0 10px;}

#aircon	{
	text-align	:center;
}
#aircon input	{
	font-size	:16pt;
	width		:100%;
	height		:40px;
}
#aircontimer input	{
	font-size	:16pt;
	height		:40px;
}

#result	{
	margin			:10px 0;
	border			:1px solid #22aa22;
	background		:#eeffef;
	list-style-image: url(accept.png);
}
#result li	{
	margin-left:2em;
	line-height:200%;
}
	</style>
</head>
<body>
  <h1>エアコンのリモコン</h1>

  <section>
	<h2>室温</h2>
	<p id="temp"><img src="<?php print $tempicon; ?>"> <?php print $temp; ?> &#08451;</p>
  </section>

  <section>
	<h2>エアコン操作</h2>
<?php
if (isset($_SESSION['result'])) {
	print $_SESSION['result'];
	$_SESSION['result'] = '';
}
?>
	<form action="<?php print $redirectTo; ?>" method="post" id="aircon">
		<input type="submit" name="open" value="電源ボタンを押す">
	</form>
  </section>

  <section>
	<h2>タイマー操作</h2>
<?php
if (isset($_SESSION['resultTimer'])) {
	print $_SESSION['resultTimer'];
	$_SESSION['resultTimer'] = '';
}
?>
	<form action="<?php print $redirectTo; ?>" method="post" id="aircontimer">
		<input type="number" name="time" style="width:3em;">分後にボタンを押す 
		<input type="submit" name="timerset" value="決定">
	</form>
  </section>

</body>
</html>
