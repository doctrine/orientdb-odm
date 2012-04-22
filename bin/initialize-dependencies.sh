#!/bin/sh

FETCH_METHOD=${1:-"composer"}
PARENT_DIR=$(dirname $(cd "$(dirname "$0")"; pwd))

cd "$PARENT_DIR"

. "$PARENT_DIR/bin/odb-shared.sh"

odb_clean_dependencies () {
  . "$PARENT_DIR/bin/clean-dependencies.sh"
}

odb_initialize_composer () {
  if [ ! -f "composer.phar" ]; then
    echo "Could not find composer.phar, downloading it now..."
    odb_download "http://getcomposer.org/composer.phar"
  fi
  /usr/bin/env php composer.phar install
}

odb_initialize_submodules () {
  git submodule --quiet update --init
}

if [ "$FETCH_METHOD" = "composer" ] ; then
  odb_clean_dependencies
  odb_initialize_composer
elif [ "$FETCH_METHOD" = "submodules" ] ; then
  odb_clean_dependencies
  odb_initialize_submodules
else
  echo "Invalid option: $FETCH_METHOD"
fi
