#!/bin/sh

PARENT_DIR=$(dirname $(cd "$(dirname "$0")"; pwd))

cd $PARENT_DIR
rm -rf composer.lock vendor/bin vendor/.composer
rm -rf vendor/installed.json vendor/autoload*.php vendor/ClassLoader.php

clean_dependency () {
    find $1 -maxdepth 1 -not -wholename $1 -iname "*" -exec rm -rf '{}' \;
}

clean_dependency "vendor/doctrine/common/"
clean_dependency "vendor/symfony/finder/Symfony/Component/Finder/"
clean_dependency "vendor/symfony/class-loader/Symfony/Component/ClassLoader/"
