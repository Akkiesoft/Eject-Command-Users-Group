<?php
//   除夜のEject     Akkiesoft
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
			除夜のEjectの様子は<a href="<?php print $live_url; ?>" target="_blank">Youtubeで配信中です</a>。Ejectが大好きな方はふるってご参加ください。
		</div>

		<h2>ライブ</h2>
		<div class="block">
<?php if ($kaisai == 1) { ?>
			<p style="margin-bottom:20px;">いまのところ、<iframe src="chkcount.php" scrolling="no" style="display:inline;width:40px;height:20px;border:0;"></iframe> 人が鐘をつきました！</p>
<?php } ?>
			<center>
<iframe width="560" height="315" src="<?php print $live_embed_url; ?>" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>
			</center>
		</div>

		<h2>Twitterでの参加はこちら</h2>
		<div class="block">
<?php if ($kaisai == 0) { ?>
			12月31日の<?php print $start_time; ?>時ぐらいからスタート予定です。開始はTwitterハッシュタグ<a href="https://twitter.com/search?q=%23EJUG">#EJUG</a>にて告知します。お楽しみに。
<?php } else {
  $cnt = intval(file_get_contents('count.dat'));
  if ($cnt < 1) {
?>
                        <p>準備中です。</p>
<?php
  } else if ($cnt == $limit) {
?>
			<p><?php print $limit - 1; ?>回鐘をついたため終了しました。みなさまにとって<?php print $kuru_year; ?>年が良い年でありますように！</p>
<?php
    } else { ?>
			<p id="login"><a href="javascript:void(0)" onClick="openWindow('<?php print $authUrl; ?>')"><img src="Sign-in-with-Twitter.png" alt="Sign in with Twitter"></a></p>
			<script type="text/javascript">
function openWindow(url){
	window.open(url, 'joya', 'width=850, height=500, menubar=no, toolbar=no, scrollbars=yes');
	document.getElementById('login').innerHTML = 'ポップアップウィンドウが表示されます。<br>ウィンドウを開き直す>ときはリロードしてください。';
}
			</script>
<?php
    }
  }
?>
			<p style="margin-top:30px;"><a href="./don.php">Mastodonはこちら</a></p>
		</div>
<?php include "htmlfoot.inc.php"; ?>
