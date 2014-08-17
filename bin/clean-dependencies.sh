#!/bin/sh

PARENT_DIR=$(dirname $(cd "$(dirname "$0")"; pwd))

cd $PARENT_DIR

rm -rf composer.lock

rm -rf bin/phpunit

odb_clean_dependency () {
  # find "$1" -maxdepth 1 -not -wholename "$1" -iname "*" -exec rm -rf '{}' \;
  rm -rf "$1"
}

odb_clean_dependency "vendor/"
