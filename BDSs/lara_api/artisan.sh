#!/bin/bash

docker exec -it -u $(id -u):$(id -g) web_server_api /var/www/html/artisan $1 $2 $3 $4