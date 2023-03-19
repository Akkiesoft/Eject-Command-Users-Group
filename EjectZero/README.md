# EjectZero

CD-ROMドライブをRaspberry PiのGPIOレベルでいじるやつです。フォトカプラを使用してEjectボタンをハックします。

WebSocketのBotも同時に動きます。

## 別途必要

Sketch_Block.ttf: http://www.dafont.com/sketch-block.font (←これほんとにフリーなのか？)

## インストール

1. Sketch_Block.ttf(別途用意)をfilesに配置する
2. files/configは適宜修正
3. Ansibleでサクッと導入

```
$  ansible-playbook -i hosts ejectzero.yml
```

## ふるいほう

WebSocketがいらないときに使えます。

1. gpioweb.pyをうごかしておく
2. files/defaultの設定を使ってnginxを導入する
3. index.htmlとmugiko.pngとSketch_Block.ttf(別途用意)を/var/www/htmlに配置する
4. アクセスする