# How to run

## build

docker build . -t ejectpico:0.1

## run

docker run -it -d --name ejectpico --restart unless-stopped -p 3981:3981 ejectpico:0.1
