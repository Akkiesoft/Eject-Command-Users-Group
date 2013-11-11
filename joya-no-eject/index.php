<?php
//   除夜のEject     (c) 2012-2013 Akkiesoft.
// -----------------------------------------------------------------------------

include 'common.php';
session_start();

$consumer->getRequestToken('https://twitter.com/oauth/request_token', $callback);

$_SESSION['request_token'] = $consumer->getToken();
$_SESSION['request_token_secret'] = $consumer->getTokenSecret();

$authUrl = $consumer->getAuthorizeUrl('https://twitter.com/oauth/authenticate');

include "htmlhead.inc.php";
?>
		<h2>なんぞこれ</h2>
		<div class="block">
			来る12月31日の大晦日に、Ejectコマンドを使って108回鐘を鳴らすという壮絶にどうでもいい企画です。<br>
			除夜のEjectの様子は<a href="http://www.ustream.tv/channel/eject-ug" target="_blank">Ustreamで配信予定です</a>。Ejectが大好きな方はふるってご参加ください。
		</div>

		<h2>参加する</h2>
		<div class="block">
			12月31日の夕方ぐらいからスタート予定です。開始はTwitterハッシュタグ<a href="https://twitter.com/search?q=%23EJUG">#EJUG</a>にて告知します。お楽しみに。
<?php /*
			<p>参加はこちら。<br><a href="<?php print $authUrl; ?>"><img src="Sign-in-with-Twitter.png" alt="Sign in with Twitter"></a></p>
*/ ?>
		</div>
<?php include "htmlfoot.inc.php"; ?>
