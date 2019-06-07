#!/bin/bash

echo "for the laravel ide helper required to add code inside composer.json post-update-cmd section - read docs"
echo ""
echo "after the installation open browser there will be permission errors"
echo "manually run permissions command listed below '#find storage blabla'"
echo ""

mkdir $1
composer create-project laravel/laravel="5.7.*" $1
composer require -d $1 barryvdh/laravel-debugbar --dev
composer require -d $1 barryvdh/laravel-ide-helper --dev
find $1/storage \( -type d -exec chmod u+rwx,g+rwx,o+rwx {} \; -o -type f -exec chmod u+rw,g+rw,o+rw {} \; \)