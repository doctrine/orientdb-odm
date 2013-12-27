#!/bin/sh

PARENT_DIR=$(dirname $(cd "$(dirname "$0")"; pwd))

cd "$PARENT_DIR"
. "$PARENT_DIR/bin/odb-shared.sh"

odb_clean_dependencies () {
  . "$PARENT_DIR/bin/clean-dependencies.sh"
}

odb_initialize_composer () {
  if [ ! -f "composer.phar" ]; then
    echo "Could not find composer.phar, downloading it now..."
    odb_download_composer "http://getcomposer.org/composer.phar"
  fi
  /usr/bin/env php composer.phar install
}

odb_clean_dependencies
odb_initialize_composer
