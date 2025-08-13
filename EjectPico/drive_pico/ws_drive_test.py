import asyncio
from websockets.asyncio.client import connect
from json import dumps, loads

url = "ws://localhost:3981"
token = ""
connect_msg = dumps({"message": "reg_drive", "token": token})

async def ejesocket():
    async with connect(url) as websocket:
        await websocket.send(connect_msg)
        while True:
            message = await websocket.recv()
            data = loads(message)
            if data["code"] == 0:
                print("Connected to server.")
            elif data["code"] == 1:
                print("Invalid token.")
                break
            elif data["code"] == 3:
                # ここでEjectを実行
                print("Received eject command from server.")

if __name__ == "__main__":
    asyncio.run(ejesocket())
