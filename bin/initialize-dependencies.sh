#!/bin/sh

FETCH_METHOD=${1:-"composer"}

clean_dependencies () {
  ./bin/clean-dependencies.sh
}

initialize_composer () {
  wget -ncv http://getcomposer.org/composer.phar
  /usr/bin/env php composer.phar install
}

initialize_submodules () {
  git submodule --quiet update --init
}

if [ "$FETCH_METHOD" = "composer" ] ; then
  clean_dependencies
  initialize_composer
elif [ "$FETCH_METHOD" = "submodules" ] ; then
  clean_dependencies
  initialize_submodules
else
  echo "Invalid option: $FETCH_METHOD"
fi
