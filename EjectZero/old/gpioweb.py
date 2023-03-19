#!/usr/bin/env python
import RPi.GPIO as GPIO
import time
from http.server import HTTPServer, SimpleHTTPRequestHandler

GPIO.setmode(GPIO.BCM)
GPIO.setup(17, GPIO.OUT)

def do_eject():
	GPIO.output(17, True)
	time.sleep(0.1)
	GPIO.output(17, False)

class Handler(SimpleHTTPRequestHandler):
	def do_GET(self):
		if self.path == "/eject":
			do_eject()
			code = 200
			body = b"ok"
		else:
			code = 404
			body = b"invalid request"
		self.send_response(code)
		self.send_header('Content-type', 'text/html; charset=utf-8')
		self.send_header('Content-length', len(body))
		self.end_headers()
		self.wfile.write(body)

host = ''
port = 8000
httpd = HTTPServer((host, port), Handler)
try: 
	httpd.serve_forever()
except KeyboardInterrupt:
	GPIO.cleanup()
