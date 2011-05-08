# OrientDB PHP Library

## What's Orient?

A Set of tools to use and manage any OrientDB instances from PHP.

Orient includes:
* the HTTP protocol binding for OrientDB
* the data mapper for OrientDB

If you don't know OrientDB here are few resources:

Homepage: http://www.orientechnologies.com/
Documentation: http://code.google.com/p/orient | http://www.odino.org/tags?tag=orient

## Current status of the binding

BETA: the binding is almost finished, we will develop a custom mapper which will sit on top of this low-level binding.
Some things will change (implementation of better interfaces, naming conventions), but nothing should stop you from using it (yes, also in production: further changes in the binding will be small and well-documented).

## Current status of the mapper

Just started. Things will come.

## Tests

In order to run the tests you only need phpunit.

cd /path/to/repo
phpunit --colors Tests/

The binding test directly connects to a working OrientDB instance, so you'll need, only for this test, to start the OrientDB server, with an empty
database ( the demo one goes well ) and add the server admin credentials into your /orientdbpath/config/orientdb-server-config.xml file
(just add the admin/admin credentials).
