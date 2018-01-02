<?php
//   除夜のEject     Akkiesoft
// -----------------------------------------------------------------------------

require_once("don_common.php");
session_start();

if ($_POST['login']) {
  $_SESSION['scheme'] = $_POST['scheme'];
  $_SESSION['domain'] = $_POST['domain'];
  if ($_SESSION['domain'] == "") {
    include("htmlhead.inc.php");
    print"<h2>エラー</h2><p>なんか入れて</p>";
    include("htmlfoot.inc.php");
    exit();
  }
  $app = create_app($scheme, $domain);

  $data = array(
    "response_type"    => "code",
    "redirect_uri"     => $callback,
    "scope"            => $scope,
    "client_id"        => $client_id
  );
  header("Location: ".$scheme."://".$domain."/oauth/authorize?".http_build_query($data));
}

function create_app($scheme, $domain) {
  global $scope, $callback;
  # check exists known instance
  if (file_exists("instances/".$domain) === FALSE) {
    # unknown instance, create app
    $data = array(
      'client_name'   => "除夜のEject for Mastodon",
      'redirect_uris' => $callback,
      'scopes'        => $scope
    );
    $fp = fopen("instances/".$domain, "w");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $scheme."://".$domain."/api/v1/apps");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
  }
  return json_decode(file_get_contents("instances/".$domain));
}

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
<iframe width="560" height="315" src="<?php print $live_embed_url; ?>" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>			</center>
		</div>

		<h2>Mastodonでの参加はこちら</h2>
		<div class="block">
<?php if ($kaisai == 0) { ?>
			12月31日の<?php print $start_time; ?>時ぐらいからスタート予定です。開始はハッシュタグ#EJUGにて告知します。お楽しみに。
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
  <form id="login" action="don.php" method="post" onsubmit="return false();">
    インスタンスのURL: <input class="don_instance" size="6" name="scheme" value="https">://<input class="don_instance" name="domain"><br>
    <input name="login" type="submit" value="ログイン" onClick="openWindowDon(this.form);">
  </form>
  <script type="text/javascript">
    function openWindowDon(f) {
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
			<p style="margin-top:30px;"><a href="./">Twitterはこちら</a></p>
		</div>
<?php include "htmlfoot.inc.php"; ?>
