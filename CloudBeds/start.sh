#!/bin/bash

echo "stopping currently running containers"
docker stop $(docker ps -a -q)
echo "executing: # docker-compose up -d"
echo ""
USER=www-data docker-compose up -d