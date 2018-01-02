<?php
//   除夜のEject     Akkiesoft
// -----------------------------------------------------------------------------

require_once("don_common.php");
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
$app = json_decode(file_get_contents("instances/".$domain));

if (isset($_GET['code'])) {
	$data = array(
		"grant_type"    => "authorization_code",
		"redirect_uri"  => $callback,
		"client_id"     => $app->client_id,
		"client_secret" => $app->client_secret,
		"code"          => $_GET['code']
	);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($ch, CURLOPT_URL, $scheme."://".$domain."/oauth/token");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$result = json_decode(curl_exec($ch));
	curl_close($ch);
	$_SESSION['token'] = $result->access_token;
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
	/* Mastodonに投稿 */
	$countstr = sprintf('%03d', $ejectcount);
	$toot = array('status' => '[除夜のEject'.$countstr.'回目]'.$_POST['toot'].' #EJUG');
	try {
		$header = ['Authorization: Bearer '.$_SESSION['token']];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $scheme."://".$domain."/api/v1/statuses");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($toot));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_exec($ch);
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

		<h2>トゥートしてEjectを実行する</h2>
		<div class="block">
			今年一年への想いや来年に向けての抱負をトゥートに込めて、Ejectしてください(495文字以内)。
			<script type="text/javascript">
function countStr(event){
	var toot = document.getElementById("tweet");
	var send = document.getElementById("send");
	var cnt  = document.getElementById("cnt");
	tootCnt  = toot.value.length;
	evType   = event.type;

	cnt.innerHTML = 495 - tootCnt;
	if (tootCnt > 495) {
		send.disabled = true;
		cnt.style.color = 'red';
	} else {
		send.disabled = false;
		cnt.style.color = 'black';
	}
}
			</script>

			<form action="don_joya.php" method="post">
				<textarea name="toot" id="tweet" onchange="countStr(event)" onkeyup="countStr(event)"></textarea><br>
				のこり文字数: <span id="cnt">495</span><br>
				<input type="submit" name="send" id="send" value="トゥートしてEjectする" />
			</form>
			<p>※Ejectはすぐに実行されるので、Ustream画面を予め表示した状態で実行してください。</p>
		</div>

<?php include "htmlfoot.inc.php"; ?>
