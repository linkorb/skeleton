#!/bin/sh

if [ ! -f composer.json ]; then
    echo "You need to run this command from project root."
    echo "Right from directory where composer.json placed."
    exit;
fi

PORT=${1:-'8080'}
php -S localhost:$PORT -t web web/index.php

# @todo Use next command after AssetsController.php deletion
# php -S localhost:$PORT -t web
