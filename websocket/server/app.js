// app.js (server)

var fs = require('fs');
var http = require('http');
var server = http.createServer();

server.on('request', function(req, res) {
  var stream = fs.createReadStream('index.html');
  res.writeHead(200, {'Content-Type': 'text/html'});
  stream.pipe(res);
});
var io = require('socket.io').listen(server);
server.listen(3980);
var bot = "";
io.sockets.on('connection', function(socket) {
  // Botの接続
  socket.on('regbot', function (data) {
    if (data == "ejectzero") { bot = socket.id; }
  });

  // Botステータスの確認
  socket.on('checkbot', function (data) {
    if (bot) { io.to(socket.id).emit('checkbot_ret', "Ready"); }
    else { io.to(socket.id).emit('checkbot_ret', "Not ready"); }
  });

  // WebからServerにeject要求が来た
  socket.on('web2srv_eject', function () {
    // ServerからBotにEject要求を投げる
    if (bot) { socket.to(bot).emit('srv2bot_eject'); }
  });

  // BotからServerに返答が来た
  socket.on('bot2srv_msg', function (data) {
    // ServerからBot以外の全体に返答を投げる
    io.sockets.emit('srv2web_msg', data);
  });

  // Disconnectの検知(botだったらbotのIDを消す)
  socket.on('disconnect', function () {
    if (socket.id == bot) { bot = ""; }
  });
});
