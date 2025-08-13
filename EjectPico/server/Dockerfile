FROM python:3.12-slim

WORKDIR /usr/src/app

RUN pip install websockets
COPY app.py .

EXPOSE 3981
CMD [ "python3", "app.py" ]
