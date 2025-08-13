import board
from digitalio import DigitalInOut, Direction
from time import sleep
from akkie_wifi import akkie_wifi as network
from akkie_wifi_config import ap_list
import websockets
from json import dumps, loads

url = "wss://eject.kokuda.org/pico/ws"
token = ""
connect_msg = dumps({"message": "reg_drive", "token": token})

led = DigitalInOut(board.LED)
led.direction = Direction.OUTPUT

optocoupler = DigitalInOut(board.GP2)
optocoupler.direction = Direction.OUTPUT
optocoupler.value = 0

try:
    network = network(ap_list)
    network.connect()

    wsession = websockets.Session(network.pool, ssl=network.ssl_context)
    with wsession.client(url) as ws:
        led.value = 1
        ws.send(connect_msg)
        while True:
            message = ws.recv()
            data = loads(message)
            print(data["message"])
            if data["code"] == 1 or data["code"] == 2:
                break
            elif data["code"] == 3:
                # ここでEjectを実行
                print("Received eject command from server.")
                for i in range(0, 2):
                    optocoupler.value = 1
                    sleep(0.2)
                    optocoupler.value = 0
                    sleep(5)
except:
    for i in range(0, 20):
        led.value = i % 2
        sleep(0.25)
    import microcontroller
    microcontroller.reset()