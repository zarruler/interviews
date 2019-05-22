#!/bin/bash


if [ -x "$(command -v docker)" ]; then
  echo -e '\e[32mdocker detected.\e[0m stopping containers.' >&2
  docker stop $(docker ps -a -q)
else
  echo 'installing docker' >&2
  
  sudo apt-get update
  sudo apt-get install \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg-agent \
    software-properties-common

  curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -

  sudo add-apt-repository \
   "deb [arch=amd64] https://download.docker.com/linux/ubuntu \
   $(lsb_release -cs) \
   stable"

  sudo apt-get update

  sudo apt-get install docker-ce docker-ce-cli containerd.io
fi