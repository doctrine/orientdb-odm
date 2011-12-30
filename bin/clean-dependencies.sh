#!/bin/sh

rm -rf composer.lock vendor/bin vendor/.composer

clean_dependency () {
    find $1 -maxdepth 1 -not -wholename $1 -iname "*" -exec rm -rf '{}' \;
}

clean_dependency "vendor/doctrine/common/"
clean_dependency "vendor/symfony/finder/Symfony/Component/Finder/"
clean_dependency "vendor/symfony/class-loader/Symfony/Component/ClassLoader/"
