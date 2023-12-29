<?php 
// 開催中かどうか
$kaisai = 0;

// ゆく年くる年
$yuku_year = 2023;
$kuru_year = $yuku_year + 1;

// limit
$limit = 108;

// Start time
$start_time = "21";

// Eject API URL
$eject_url = "";

// Live URL
$live_url = "https://youtu.be/9uwH-LxYLA8";
// 2021 :: $live_url = "https://youtu.be/VCRL4t2-kmo";
// 2020 :: $live_url = "https://youtu.be/8-aqmNDgEec";
// 2019 :: $live_url = "https://youtu.be/EJAG4lYKgEM";
// 2018 Cont:: $live_url = "https://www.youtube.com/watch?v=VyONl0Eri7A";
// 2018 :: $live_url = "https://www.youtube.com/watch?v=EQTDg6PAYAY";
// 2017 :: $live_url = "https://www.youtube.com/watch?v=my0MopPsw8w";

// Live embed URL
$live_embed_url = "https://www.youtube.com/embed/9uwH-LxYLA8";
// 2021 :: $live_embed_url = "https://www.youtube.com/embed/VCRL4t2-kmo";
// 2020 :: $live_embed_url = "https://www.youtube.com/embed/8-aqmNDgEec";
// 2019 :: $live_embed_url = "https://www.youtube.com/embed/EJAG4lYKgEM";
// 2018 :: $live_embed_url = "https://www.youtube.com/embed/EQTDg6PAYAY";
// 2017 :: $live_url = "https://www.youtube.com/embed/my0MopPsw8w";


/* Mastodon Application info */
$don_scope = "read write";
$don_callback = "https://eject.kokuda.org/joya/don_joya.php";

/* Misskey Application info */
$mi_scope = "read:account,write:notes";
$mi_callback = "https://eject.kokuda.org/joya/mi_joya.php";

/* Create mastodon application*/
function create_don_app($scheme, $domain) {
  global $don_scope, $don_callback;
  # check exists known servers
  if (file_exists("don_servers/".$domain) === FALSE) {
    # unknown instance, create app
    $data = array(
      'client_name'   => "除夜のEject for Mastodon",
      'redirect_uris' => $don_callback,
      'scopes'        => $don_scope
    );
    $fp = fopen("don_servers/".$domain, "w");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $scheme."://".$domain."/api/v1/apps");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
  }
  return json_decode(file_get_contents("don_servers/".$domain));
}
