#!/usr/bin/env python3

import os
from time import sleep
import threading
import RPi.GPIO as GPIO
from socketio import Client
from http.server import HTTPServer, SimpleHTTPRequestHandler
from config import socketio_uri, api_port, api_ip_restrict_list

GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
GPIO.setup(17, GPIO.OUT)

def do_eject():
    GPIO.output(17, True)
    sleep(0.3)
    GPIO.output(17, False)

io = Client()
# 接続・再接続
@io.event
def connect():
    io.emit('regbot', "ejectzero")
@io.event
def reconnect():
    io.emit('regbot', "ejectzero")
@io.event
def srv2bot_eject():
    do_eject()
    sleep(1)
    do_eject()

def socketio_thread():
    io.connect(socketio_uri)
    io.wait()

class Handler(SimpleHTTPRequestHandler):
  def do_GET(self):
    if not [ip for ip in api_ip_restrict_list if ip in self.client_address[0]]:
      code = 403
      body = b"Forbidden"
    elif self.path == "/eject":
      do_eject()
      sleep(0.8)
      do_eject()
      sleep(0.8)
      code = 200
      body = b"ok"
    else:
      code = 404
      body = b"invalid request"
    self.send_response(code)
    self.send_header('Content-type', 'text/plain; charset=utf-8')
    self.send_header('Content-length', len(body))
    self.end_headers()
    self.wfile.write(body)

thread1 = threading.Thread(target = socketio_thread)
thread1.setDaemon(True)
thread1.start()

httpd = HTTPServer(('', api_port), Handler)
try: 
  httpd.serve_forever()
except KeyboardInterrupt:
  GPIO.cleanup()
