import asyncio
from websockets.asyncio.server import serve, broadcast
from websockets.exceptions import ConnectionClosedOK, ConnectionClosedError
import json

# References:
# https://websockets.readthedocs.io/en/stable/
# https://websockets.readthedocs.io/en/stable/intro/tutorial2.html

drive_passphrase = ""
drive = []
clients = []

# ドライブの接続処理
async def register_drive(websocket, token):
    global drive

    # トークンとは何か
    if token != drive_passphrase:
        await websocket.send('{"code": 1, "message": "Invalid token."}')
        return

    # ないとは思うけど一応
    if len(drive):
        await websocket.send('{"code": 2, "message": "Drive is already registered."}')
        return

    drive = [websocket]
    try:
        print("Drive connected.")
        message = '{"code": 0, "message": "Drive registered.", "drive_status": true}'
        await websocket.send(message)
        if len(clients):
            # クライアントがいるならドライブの状態を通知
            broadcast(clients, message)

        # ドライブから追加でメッセージを受け取らないので、
        # wait_closed()でまたせて、こちらから一方的に送るだけにする
        # 何か受け取りたくなったらejectzero()を使う
        # await ejectzero(websocket)
        await websocket.wait_closed();
    finally:
        # 切断処理
        print("Drive disconnected.")
        drive = []
        if len(clients):
            # ドライブ切断をクライアントに通知
            broadcast(clients, '{"code": 0, "message": "Drive unregistered.", "drive_status": false}')

"""
# ドライブからサーバーに結果を返すとかしない限りいらないやつ
async def ejectzero(websocket):
    while True:
        try:
            message = await websocket.recv()
            data = json.loads(message)
            if data["message"] == "eject":
                print("Eject")
        except ConnectionClosedOK:
            break
"""

# クライアント（Web）の処理
async def add_client(websocket):
    global clients
    try:
        print("Client connected.")
        # ドライブが接続中かどうかの情報を含んだ返答の作成
        drive_status = True if len(drive) else False
        message = json.dumps({"code": 0, "message": "Client registered.", "drive_status": drive_status})
        await websocket.send(message)
        # クライアントリストに追加
        clients.append(websocket)
        # クライアント向けのメインループを実行
        await webclient(websocket)
    finally:
        # 切断処理。クライアントリストから削除する
        print("Client disconnected.")
        clients.remove(websocket)

# クライアントのメインループ
async def webclient(websocket):
    async for message in websocket:
        data = json.loads(message)
        # Ejectボタンが押された
        if data["message"] == "eject":
            print("An eject command has been emitted from web client!")
            # ドライブがあれば、ドライブに対してメッセージを送信
            # 結果を受け取ったりとかをしないため、ドライブがないときは特になにもしない。
            if len(drive):
                await drive[0].send('{"code": 3, "message": "Eject!"}')

# 最初の接続受付
async def handler(websocket):
    while True:
        try:
            message = await websocket.recv()
            data = json.loads(message)
            # ドライブ向け
            if data["message"] == "reg_drive":
                await register_drive(websocket, data["token"])
            # クライアント向け
            if data["message"] == "client":
                await add_client(websocket)
        except ConnectionClosedOK:
            break
        except ConnectionClosedError:
            break

async def main():
    async with serve(handler, "", 3981) as server:
        await server.serve_forever()

if __name__ == "__main__":
    asyncio.run(main())
