# EjectPico

WebSocketで実装された、Raspberry Pi Pico 2W搭載CD-ROMドライブ向けのリモートEject実行環境です。

## サーバー

* Pythonとwebsocketsライブラリで実装しています
* Dockerなどで実行してください（Dockerfileを用意してあります）
* token（とは名ばかりのパスフレーズ的なもの）に適当な文字列を設定してください

## CD-ROMドライブ用プログラム

* CircuitPythonで実装しています
* サーバーに設定したtokenをスクリプトに記述してください

## Web画面

* HTMLとJavaScriptで実装しています
* HTMLは任意の場所に設置します
* web/README.md の例（Apache）に従って、サーバープログラムのリバースプロキシを設定します