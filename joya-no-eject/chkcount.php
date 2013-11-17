<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf=8">
<meta http-equiv="refresh" CONTENT="10;URL=./chkcount.php">
<title>count</title>
<style type="text/css">*{margin:0;padding:0;}</style>
</head>
<body>
<div style="text-align:right;">
<big><?php print intval(file_get_contents('count.dat')) - 1; ?></big>
</div></body></html>
