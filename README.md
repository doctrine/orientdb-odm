# OrientDB PHP Library

## What's Orient?

A Set of tools to use and manage any OrientDB instances from PHP.

Orient includes:

* the HTTP protocol binding
* the query builder
* the data mapper ( Object Graph Mapper )


If you don't know OrientDB here are few resources:

Homepage: http://www.orientechnologies.com/
Documentation: http://code.google.com/p/orient | http://www.odino.org/tags?tag=orient

## Current status of the binding

BETA: the binding is almost finished, we will develop a custom mapper which will sit on top of this low-level binding.
Some things will change (implementation of better interfaces, naming conventions), but nothing should stop you from using it (yes, also in production: further changes in the binding will be small and well-documented).

## Current status of the query builder

We've correctly implemented an API to do SELECTs and INSERTs, which are the most difficult expressions to manage.

Shortly a "final" version of the query builder will be published in order to start the heavy work for the mapper.

## Current status of the mapper

Not started yet.

## Tests

In order to run the tests you only need to:

    cd /path/to/repo
    phpunit --configuration=Test/PHPUnit/phpunit.xml

## Requirements

PHP >= 5.3.5

PHPUnit >= 3.5

OrientDB 0.9.25
