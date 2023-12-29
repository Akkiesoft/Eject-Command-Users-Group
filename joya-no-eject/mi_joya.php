<?php
//   除夜のEject     Akkiesoft
// -----------------------------------------------------------------------------

require_once("common.php");
session_start();

$scheme = (isset($_SESSION['scheme'])) ? $_SESSION['scheme'] : "";
$domain = (isset($_SESSION['domain'])) ? $_SESSION['domain'] : "";

if ($domain == "") {
	include 'htmlhead.inc.php';
	print <<<EOM
もうおわかりだろう！ドメンメがないのである！！！
EOM;
	include 'htmlfoot.inc.php';
	exit();
}

if (isset($_GET['session'])) {
    $session_id = $_GET['session'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
	curl_setopt($ch, CURLOPT_URL, $scheme."://".$domain."/api/miauth/".$session_id."/check");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$result = json_decode(curl_exec($ch));
	curl_close($ch);
	$_SESSION['token'] = $result->token;
	$_SESSION['user'] = $result->user;
}

$ejectcount = intval(file_get_contents('count.dat'));
if ($ejectcount > $limit - 1) {
	include 'htmlhead.inc.php';
	print <<<EOM
<h2>終了してしまった模様</h2>
<p>108回鐘をつかれたため、終了しました。惜しい。</p>
<p style="margin-top:2em;"><a href="javascript:void(0)" onClick="window.close('joya')">このウィンドウを閉じる</a></p>
EOM;
	include 'htmlfoot.inc.php';
	exit();
}

if (isset($_POST['send'])) {
	/* Misskeyに投稿 */
	$countstr = sprintf('%03d', $ejectcount);
	$note = json_encode(array(
		'i' => $_SESSION['token'],
		'text' => '[除夜のEject'.$countstr.'回目]'.$_POST['note'].' #EJUG'
	));
	try {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $scheme."://".$domain."/api/notes/create");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $note);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result = json_decode(curl_exec($ch));
		curl_close($ch);
	} catch (Exception $e) {
		/* Error */
		include 'htmlhead.inc.php';
		print <<<EOM
<h2>投稿エラー</h2>
<p style="color:red;">なんででしょう。とりあえずログインからやりなおしてみる？</p>
<p style="margin-top:2em;"><a href="javascript:void(0)" onClick="window.close('joya')">このウィンドウを閉じる</a></p>
EOM;
		include 'htmlfoot.inc.php';
		exit();
	}
	$ejectcount++;
	file_put_contents('count.dat', $ejectcount, LOCK_EX);
	file_get_contents($eject_url);
	include 'htmlhead.inc.php';
	print <<<EOM
<h2>鐘をつきました</h2>
<p>{$kuru_year}年が良い年になりますように！</p>
<p style="margin-top:2em;"><a href="javascript:void(0)" onClick="window.close('joya')">このウィンドウを閉じる</a></p>
EOM;
	include 'htmlfoot.inc.php';
	$_SESSION = '';
	session_destroy();
	exit;
}

include "htmlhead.inc.php"
?>

		<h2>ノートしてEjectを実行する</h2>
		<div class="block">
			今年一年への想いや来年に向けての抱負をノートに込めて、Ejectしてください(495文字以内)。
			<script type="text/javascript">
function countStr(event){
	var note = document.getElementById("tweet");
	var send = document.getElementById("send");
	var cnt  = document.getElementById("cnt");
	noteCnt  = note.value.length;
	evType   = event.type;

	cnt.innerHTML = 495 - noteCnt;
	if (noteCnt > 495) {
		send.disabled = true;
		cnt.style.color = 'red';
	} else {
		send.disabled = false;
		cnt.style.color = 'black';
	}
}
			</script>

			<form action="mi_joya.php" method="post">
				<textarea name="note" id="tweet" onchange="countStr(event)" onkeyup="countStr(event)"></textarea><br>
				のこり文字数: <span id="cnt">495</span><br>
				<input type="submit" name="send" id="send" value="ノートしてEjectする" />
			</form>
			<p>※Ejectはすぐに実行されるので、ライブ画面を予め表示した状態で実行してください。</p>
		</div>
<?php include "htmlfoot.inc.php"; ?>
