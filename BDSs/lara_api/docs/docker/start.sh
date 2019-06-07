#!/bin/bash

echo "stopping currently rrunning containers"
docker stop $(docker ps -a -q)
echo "executing: # docker-compose up -d"
echo ""
USER=www-data docker-compose up -d