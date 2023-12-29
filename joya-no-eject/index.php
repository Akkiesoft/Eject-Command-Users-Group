<?php
//   除夜のEject     Akkiesoft
// -----------------------------------------------------------------------------
include "common.php";
include "htmlhead.inc.php";
session_start();

if ($_POST['login']) {
    $_SESSION['scheme'] = $_POST['scheme'];
    $_SESSION['domain'] = $_POST['domain'];
    if ($_SESSION['domain'] == "") {
        include("htmlhead.inc.php");
        print "<h2>エラー</h2><p>なんか入れて</p>";
        include("htmlfoot.inc.php");
        exit();
    }

    $ap_type = $_POST['type'];
    if ($ap_type == "mastodon") {
        $app = create_don_app($_SESSION['scheme'], $_SESSION['domain']);
        $data = http_build_query(array(
            'response_type' => "code",
            'redirect_uri'  => $don_callback, 
            'scope'         => $don_scope,
            'client_id'     => $app->client_id
	));
        header("Location: ".$_SESSION['scheme']."://".$_SESSION['domain']."/oauth/authorize?".$data);
    } else if ($ap_type == "misskey") {
        $session_id = session_id();
        $data = http_build_query(array(
            'name'       => "除夜のEject for Misskey",
            'callback'   => $mi_callback,
            'permission' => $mi_scope
        ));
      header("Location: ".$_SESSION['scheme']."://".$_SESSION['domain']."/miauth/".$session_id."?".$data);
    }
}
?>
		<h2>なんぞこれ</h2>
		<div class="block">
			来る12月31日の大晦日に、Ejectコマンドを使って108回鐘を鳴らすという壮絶にどうでもいい企画です。<br>
			除夜のEjectの様子は<a href="<?php print $live_url; ?>" target="_blank" rel="noreferrer">Youtubeで配信中です</a>。Ejectが大好きな方はふるってご参加ください。
		</div>

		<h2>ライブ</h2>
		<div class="block">
			<p id="count" style="margin-bottom:20px;"></p>
			<center>
				<iframe width="560" height="315" src="<?php print $live_embed_url; ?>" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>
			</center>
		</div>

		<h2>参加する</h2>
		<div class="block">
<?php if ($kaisai == 0) { ?>
			12月31日の<?php print $start_time; ?>時ぐらいからスタート予定です。忘れっぽい方はスケジューラへの登録をおすすめします。<br>
			<a href="https://calendar.google.com/calendar/u/0/r/eventedit?text=%E9%99%A4%E5%A4%9C%E3%81%AEEject<?php print $yuku_year; ?>&details=https://eject.kokuda.org/joya&location=+&dates=<?php print $yuku_year; ?>1231T120000Z/<?php print $yuku_year; ?>1231T140000Z&trp=false&sprop=name:%E9%99%A4%E5%A4%9C%E3%81%AEEject<?php print $yuku_year; ?>&sf=true" target="_blank" rel="nooperner">Googleカレンダーに追加する</a>
			<br>なお、今年からTwitterからの参加はできません。本年からはMisskeyに対応予定です。お手数ですが、MastodonもしくはMisskeyのどこかのサーバーにアカウントをご用意ください。
<?php } else {
  $cnt = intval(file_get_contents('count.dat'));
  if ($cnt < 0) {
?>
                        <p>準備中です。</p>
<?php
  } else if ($cnt == $limit) {
?>
			<p><?php print $limit; ?>回鐘をついたため終了しました。みなさまにとって<?php print $kuru_year; ?>年が良い年でありますように！</p>
<?php
  } else { ?>
			<h3>Mastodonから参加</h3>
			<form id="login_don" action="index.php" method="post" onsubmit="return false();">
				サーバーのURL: <input size="6" name="scheme" value="https">://<input class="domain" name="domain"><br>
				<input name="login" type="submit" value="ログイン" onClick="openWindow(this.form);">
				<input type="hidden" name="type" value="mastodon">
			</form>

			<h3>Misskeyから参加</h3>
			<form id="login_mi" action="index.php" method="post" onsubmit="return false();">
				サーバーのURL: <input size="6" name="scheme" value="https">://<input class="domain" name="domain"><br>
				<input name="login" type="submit" value="ログイン" onClick="openWindow(this.form);">
				<input type="hidden" name="type" value="misskey">
			</form>

			<script type="text/javascript">
				function openWindow(f) {
					f.target = 'joya';
					var w = window.open("about:blank", f.target, 'width=850, height=500, menubar=no, toolbar=no, scrollbars=yes');
					w.focus();
					f.submit();
				}
			</script>
<?php
    }
  }
?>
		</div>
<?php include "htmlfoot.inc.php"; ?>
