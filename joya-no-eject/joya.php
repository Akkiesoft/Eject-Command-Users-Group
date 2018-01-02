<?php
//   除夜のEject     Akkiesoft
// -----------------------------------------------------------------------------

include 'common.php';
session_start();

if (isset($_GET['op']) && $_GET['op'] == 'logout') {
	$_SESSION = '';
	session_destroy();
	header('Loction:./');
	exit();
}

/* Twitterからのコールバック */
if (isset($_GET['oauth_verifier'])) {
	$verifier = $_GET['oauth_verifier'];
	try {
		$consumer->setToken($_SESSION['request_token']);
		$consumer->setTokenSecret($_SESSION['request_token_secret']);
		$consumer->getAccessToken('https://twitter.com/oauth/access_token', $verifier);
		$_SESSION['accessToken']       = $consumer->getToken();
		$_SESSION['accessTokenSecret'] = $consumer->getTokenSecret();
	} catch (Exception $e) {
		$err = $e->getMessage();
		session_destroy();
		include 'htmlhead.inc.php';
		print <<<EOM
<h2>エラーが発生したようです</h2><p style="color:red;">$err</p>
<p style="margin-top:2em;"><a href="javascript:void(0)" onClick="window.close('joya')">このウィンドウを閉じる</a></p>
EOM;
		include 'htmlfoot.inc.php';
		exit();
	}
	header('Location:./joya.php');
	exit();
}
else if (! isset($_POST['submit']) && isset($_POST['trans_settings'])) {
	session_destroy();
	$result = (isset($_GET['denied'])) ? 'Twitterの認証で拒否されたようです。' : 'エラーが発生したか失敗したようです。';
	include 'htmlhead.inc.php';
	print <<<EOM
<h2>登録失敗</h2>
<p style="color:red;">$result</p>
<p style="margin-top:2em;"><a href="javascript:void(0)" onClick="window.close('joya')">このウィンドウを閉じる</a></p>
EOM;
	include 'htmlfoot.inc.php';
	exit();
}


/* セッションの有効性チェック */
if (! isset($_SESSION['accessToken'])) {
	include 'htmlhead.inc.php';
	print <<<EOM
<h2>セッション切れ</h2>
<p style="color:red;">セッションが無効です。</p>
<p style="margin-top:2em;"><a href="javascript:void(0)" onClick="window.close('joya')">このウィンドウを閉じる</a></p>
EOM;
	include 'htmlfoot.inc.php';
	exit();
} else {
	$accessToken       = $_SESSION['accessToken'];
	$accessTokenSecret = $_SESSION['accessTokenSecret'];
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
	/* Twitterに投稿 */
	$countstr = sprintf('%03d', $ejectcount);
	$tweet = '[除夜のEject'.$countstr.'回目]'.$_POST['tweet'].' #EJUG';
	try {
		$consumer->setToken($accessToken);
		$consumer->setTokenSecret($accessTokenSecret);
		$response = $consumer->sendRequest('https://api.twitter.com/1.1/statuses/update.json', array('status' => $tweet), 'POST');
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

		<h2>ツイートしてEjectを実行する</h2>
		<div class="block">
			今年一年への想いや来年に向けての抱負をツイートに込めて、Ejectしてください(115文字以内)。
			<script type="text/javascript">
function countStr(event){
	var tweet = document.getElementById("tweet");
	var send  = document.getElementById("send");
	var cnt   = document.getElementById("cnt");
	tweetCnt  = tweet.value.length;
	evType    = event.type;

	cnt.innerHTML = 115 - tweetCnt;
	if (tweetCnt > 115) {
		send.disabled = true;
		cnt.style.color = 'red';
	} else {
		send.disabled = false;
		cnt.style.color = 'black';
	}
}
			</script>

			<form action="joya.php" method="post">
				<textarea name="tweet" id="tweet" onchange="countStr(event)" onkeyup="countStr(event)"></textarea><br>
				のこり文字数: <span id="cnt">115</span><br>
				<input type="submit" name="send" id="send" value="ツイートしてEjectする" />
			</form>
			<p>※Ejectはすぐに実行されるので、ライブ画面を予め表示した状態で実行してください。</p>
		</div>

<?php include "htmlfoot.inc.php"; ?>
