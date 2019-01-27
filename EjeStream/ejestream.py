#!/usr/bin/env python3
# Remote Eject server with Raspberry Pi camera live streaming
# Based: http://picamera.readthedocs.io/en/latest/recipes2.html#web-streaming

import io
import picamera
import logging
import socketserver
from threading import Condition
from http import server
import subprocess

PAGE="""\
<html lang="ja">
<head>
<meta charset="utf-8">
<title>OSC2019 Osaka Ejectコマンドユーザー会リモートブース</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<style>
*{margin:0;padding:0;font-family:sans-serif;}
h1{margin:10px;font-size:16pt;}
#stream{margin:10px 0;}
#stream img{width:100%;}
#eject{width:100%;height:50px;font-size:20pt;}
p{margin:auto 10px;font-size:12pt;}
</style>
<script>
function eject(){
var btn = document.getElementById('eject');
btn.disabled = true;
fetch('/eject').then(function(){
btn.disabled = false;
});
return false;
}
</script>
</head>
<body>
<h1>OSC2019 Osaka Ejectコマンドユーザー会リモートブース</h1>
<div id="stream"><img src="stream.mjpg"></div>
<input type="button" id="eject" onclick="eject();return false;" value="(☝ ՞ਊ ՞)☝">
<p>映像は多少遅延します。<br>ボタンは連打しないでください。</p>
</body>
</html>
"""

class StreamingOutput(object):
    def __init__(self):
        self.frame = None
        self.buffer = io.BytesIO()
        self.condition = Condition()

    def write(self, buf):
        if buf.startswith(b'\xff\xd8'):
            # New frame, copy the existing buffer's content and notify all
            # clients it's available
            self.buffer.truncate()
            with self.condition:
                self.frame = self.buffer.getvalue()
                self.condition.notify_all()
            self.buffer.seek(0)
        return self.buffer.write(buf)

class StreamingHandler(server.BaseHTTPRequestHandler):
    def do_HEAD(self):
        self.send_response(400)

    def do_GET(self):
        if self.path == '/':
            self.send_response(301)
            self.send_header('Location', '/index.html')
            self.end_headers()
        elif self.path.startswith("/?fbclid="):
            self.send_response(301)
            self.send_header('Location', '/index.html')
            self.end_headers()
        elif self.path == '/eject':
            cmd = "/usr/bin/eject -T"
            subprocess.call(cmd, shell=True)
            self.send_response(200)
        elif self.path == '/index.html':
            content = PAGE.encode('utf-8')
            self.send_response(200)
            self.send_header('Content-Type', 'text/html')
            self.send_header('Content-Length', len(content))
            self.end_headers()
            self.wfile.write(content)
        elif self.path == '/stream.mjpg':
            self.send_response(200)
            self.send_header('Age', 0)
            self.send_header('Cache-Control', 'no-cache, private')
            self.send_header('Pragma', 'no-cache')
            self.send_header('Content-Type', 'multipart/x-mixed-replace; boundary=FRAME')
            self.end_headers()
            try:
                while True:
                    with output.condition:
                        output.condition.wait()
                        frame = output.frame
                    self.wfile.write(b'--FRAME\r\n')
                    self.send_header('Content-Type', 'image/jpeg')
                    self.send_header('Content-Length', len(frame))
                    self.end_headers()
                    self.wfile.write(frame)
                    self.wfile.write(b'\r\n')
            except Exception as e:
                logging.warning(
                    'Removed streaming client %s: %s',
                    self.client_address, str(e))
        else:
            self.send_error(404)
            self.end_headers()

class StreamingServer(socketserver.ThreadingMixIn, server.HTTPServer):
    allow_reuse_address = True
    daemon_threads = True

with picamera.PiCamera(resolution='480x360', framerate=24) as camera:
    output = StreamingOutput()
    #Uncomment the next line to change your Pi's Camera rotation (in degrees)
    #camera.rotation = 90
    camera.start_recording(output, format='mjpeg')
    try:
        address = ('', 8000)
        server = StreamingServer(address, StreamingHandler)
        server.serve_forever()
    finally:
        camera.stop_recording()
