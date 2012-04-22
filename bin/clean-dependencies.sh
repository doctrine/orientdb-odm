#!/bin/sh

PARENT_DIR=$(dirname $(cd "$(dirname "$0")"; pwd))

cd $PARENT_DIR

rm -rf composer.lock vendor/bin vendor/.composer
rm -rf vendor/installed.json vendor/autoload*.php vendor/ClassLoader.php

odb_clean_dependency () {
  find "$1" -maxdepth 1 -not -wholename "$1" -iname "*" -exec rm -rf '{}' \;
}

odb_clean_dependency "vendor/doctrine/common/"
odb_clean_dependency "vendor/symfony/finder/Symfony/Component/Finder/"
odb_clean_dependency "vendor/symfony/class-loader/Symfony/Component/ClassLoader/"
