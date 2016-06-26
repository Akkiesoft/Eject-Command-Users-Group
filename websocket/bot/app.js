// app.js (bot)

var io = require('socket.io-client');
var socket = io.connect('http://d250g2.com:3980');

// GPIOの用意
var pin = 11;
var gpio = require('rpi-gpio');
gpio.setup(pin, gpio.DIR_OUT, function(err){
  if (err) { console.log(err); }
});

// 接続・再接続（サーバーにBOTとして登録）
socket.on('connect', function(data) {
  socket.emit('regbot', "ejectzero");
});
socket.on('reconnect', function(data) {
  socket.emit('regbot', "ejectzero");
});

// サーバーからEjectの指示がきた
socket.on('srv2bot_eject', function() {
  cycle();
  // 1秒後にもう一時実行することでトレイを往復させる
  setTimeout(function(){cycle();}, 1000);
});

// Ejectの実行
function cycle() {
  // GPIOピンをショートさせる
  gpio.write(pin, true);
  // 0.2秒後にショートを解除
  setTimeout(function(){ gpio.write(pin, false); }, 200);
}
