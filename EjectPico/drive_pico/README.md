# EjectPico CD-ROMドライブ用プログラム

## 必要な環境

* CD-ROMドライブ
* Raspberry Pi Pico 2W
    * CircuitPythonを用意すること
    * ドライブに内蔵する前提

## 必要なライブラリ

* adafruit_connection_manager
* adafruit_logging
* [websockets-for-circuitpython](https://github.com/Neradoc/websockets-for-circuitpython)

## テストプログラム

PCなどでテストするときは ws_drive_test.py が使えます。CircuitPythonでやる必要がないときなどに使います。