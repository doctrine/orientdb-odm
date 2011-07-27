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

## After Clone

execute this two commands:

    git submodule init
    git submodule update

## Current status of the binding

The binding is complete: it is an HTTP client wrapper with some methods bound to OrientDB's HTTP interface.

Its usage is straightforward:

    $driver   = new Orient\Http\Client\Curl();
    $orient   = new Orient\Foundation\Binding($driver, '127.0.0.1', '2480', 'admin', 'admin', 'demo');
    $response = $orient->query("SELECT FROM Address");
    $output   = json_decode($response->getBody());

    foreach ($output->result as $address)
    {
      var_dump($address->street);
    }

Use the PHP5.3 standard autoloader (https://gist.github.com/221634).


## Current status of the query builder

All the known SQL command are implemented for the latest version of OrientDB, that means also ALTER CLASS/ALTER PROPERTY are included.

Integration tests (the ones directly connecting to OrientDB) are almost finished, we only need to test:

* class creation
* altering a class
* class deletion
* property creation
* altering a property
* property removal
* UPDATE command

The release of the query-builder is scheduled for middle June 2011.

## Current status of the mapper

We started working on the mapper and, right now, it is able to map OrientDB responses (converted in StdObject) to annotation-mapped POPOs.
Also collections are hydrated properly.

However, it's under heavy work, so don't expect to be able to use it in a few weeks. Next steps are:

* hydrate OrientDB native data-type (it includes floats, embedded-set|link|list, embedded-map|link|list and many others...)
* provide a base repository class 
* implementation of the persistence from the ODM to OrientDB

## Tests & reports

In order to run the tests you only need to:

    cd /path/to/repo
    phpunit --configuration=Test/PHPUnit/phpunit.xml

This should be enough.
For the braves, if you want to run the full test suite, which includes the integration tests, you should:

* download the supported version of OrientDB
* make sure it has the demo database bundled with every OrientDB release (if you use a snapshot, please create the database directory and copy there the database bundled with the latest OrientDB official release)
* add the server administration credential admin/admin in config/orientdb-server-config.xml
* launch the server on :2424, reacheable via web at the :2480

and the you can run the full test-suite:

    cd /path/to/repo
    phpunit --configuration=Test/PHPUnit/phpunit.xml Test/

As you'll notice, tests are obviously slower (they need a direct connection through the HTTP protocol to OrientDB), so we highly discourage you from testing this way.
Integration tests are run by the development team before any tag in the repository, so you are sure that any tag is fully tested against a real OrientDB instance.

You can take a look at the library coding health by using PHP_CodeBrowser.

You need to install:

* phpcpd
* phpdcd
* phploc
* phpmd
* phpdepend
* phpunit ( of course )
* phpcb

and the run:

    chmod +x report.sh
    ./report.sh

You'll be able to see the HTML reports in log/report/index.html.

## License

Copyright (C) 2011 by Alessandro Nadalin <alessandro.nadalin@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE


## Requirements

These are the requirements in order to use the library:

* PHP >= 5.3.3
* OrientDB 1.0.0RC3-snapshot

In order to launch the test suite PHPUnit 3.5 is required.

## Known issues

Updating of the Maps is currently bugged within the SQL statement constructors.
