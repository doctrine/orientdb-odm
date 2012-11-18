# OrientDB PHP Library

[![project status](http://stillmaintained.com/odino/Orient.png)](http://stillmaintained.com/congow/Orient)
[![Build Status](https://secure.travis-ci.org/congow/Orient.png?branch=master)](http://secure.travis-ci.org/congow/Orient)

## What's Orient?

A set of tools to use and manage any OrientDB instance from PHP.

Orient includes:

* the HTTP protocol binding
* the query builder
* the data mapper ( Object Graph Mapper )


If you don't know [OrientDB](http://www.orientechnologies.com/) take a look at its [Documentation](http://code.google.com/p/orient).

## After cloning

In order to be able to run the examples and the test suite provided by Orient, you must first enter the root of
the cloned repository and initialize all the needed dependencies with Composer [Composer](http://getcomposer.org/).
We provide an utility script in the `bin/` subdirectory to ease this process so you can just do the following:

    $ ./bin/initialize-dependencies.sh


## Current status of the binding

The binding is complete: it is an HTTP client wrapper with some methods bound to OrientDB's HTTP interface.

Its usage is straightforward:

    $parameters = Congow\Orient\Binding\BindingParameters::create('http://admin:admin@127.0.0.1:2480/demo');
    $orient = new Congow\Orient\Binding\HttpBinding($parameters);
    $output = $orient->query("SELECT FROM Address");

    foreach ($output->getResult() as $address) {
        var_dump($address->street);
    }

Use the PHP5.3 standard autoloader (https://gist.github.com/221634).


## Current status of the query builder

The query-builder is finished, in the future we will consider the integration of OrientDB
Graph Edition: http://code.google.com/p/orient/wiki/GraphEdTutorial.

To take advantage of the QB you only have to instantiate a Query object:

    use Congow\Orient\Query;
    
    $query = new Query();
    $query->from(array('users'))->where('username = ?', "admin");
    
    echo $query->getRaw(); // SELECT FROM users WHERE username = "admin"

The Query object incapsulates lots of sub-commands, like SELECT, DROP, GRANT, INSERT
and so on...

You can use also those commands:

    use Congow\Orient\Query\Command\Select;
    
    $select = new Select('users');
    echo $select->getRaw(); // SELECT FROM users


However, we strongly discourage this approach: commands will change, Query, thought as a facade, - hopely - not.

You'd better take a look at the tests of the Query class and its subcommands to get
a full overview of the available commands: in order to match OrientDB's native
SQL-like synthax we tried to preserve names and stuff like that, but a few things
have changed so far.

## Current status of the mapper

We started working on the mapper and, right now, it is able to map OrientDB responses (converted in StdObject) to annotation-mapped POPOs.
Also collections are hydrated properly.

However, it's under heavy work, so don't expect to be able to use it in a few weeks. Next steps are:

* hydrate OrientDB native data-type (it includes floats, embedded-set|link|list, embedded-map|link|list and many others...)
* provide a base repository class 
* implementation of the persistence from the ODM to OrientDB

## Utilities

Orient incapsulates also a few utilities for PHP developers: on of them is an implementation of Dijkstra's algorithm.

    use Congow\Orient\Graph;
    use Congow\Orient\Graph\Vertex;
    use Congow\Orient\Algorithm; 
    
    $graph = new Graph();
    
    $rome      = new Vertex('Rome');
    $paris     = new Vertex('Paris');
    $london    = new Vertex('London');
    
    $rome->connect($paris, 2);
    $rome->connect($london, 3);
    $paris->connect($london, 1);

    $graph->add($rome);
    $graph->add($paris);
    $graph->add($london);
    
    $algorithm = new Algorithm\Dijkstra($graph);
    $algorithm->setStartingVertex($rome);
    $algorithm->setEndingVertex($tokio);
    
    var_dump($algorithm->solve());

## Tests & reports

In order to run the tests you only need to:

    $ cd /path/to/repo
    $ phpunit

This should be enough.
For the braves, if you want to run the full test suite, which includes the integration tests, you should:

* download the supported version of OrientDB
* make sure it has the demo database bundled with every Congow\OrientDB release (if you use a snapshot, please create the database directory and copy there the database bundled with the latest OrientDB official release)
* add the server administration credential admin/admin in config/orientdb-server-config.xml
* launch the server on :2424, reacheable via web at the :2480

and then you can run the full test-suite:

    $ phpunit test/

As you'll notice, tests are obviously slower (they need a direct connection through the HTTP protocol to Congow\OrientDB), so we highly discourage you from testing this way.
They also may file the first time you launch them, because we set the curl timeout limit to 1 second, and the first connections to Congow\OrientDB require it to bootstrap lots of stuff (so during more than 1 second, making the tests fail): try to re-launch the tests, you should see the green bar.
Integration tests, by the way, are run by the development team before any tag in the repository, so you are sure that any tag is fully tested against a real Congow\OrientDB instance.

You can take a look at the library coding health by using PHP_CodeBrowser.

You need to install:

* phpcpd
* phpdcd
* phploc
* phpmd
* phpdepend
* phpunit ( of course )
* phpcb

and then run:

    $ ./bin/report.sh

You'll be able to see the HTML reports in log/report/index.html.

## License

Copyright (C) 2011 by Alessandro Nadalin <alessandro.nadalin@gmail.com>
Copyright (C) 2011 by David Funaro <ing.davidino@gmail.com>

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
* OrientDB 1.0.0RC6

In order to launch the test suite PHPUnit 3.6 is required.

## Tracker & software lifecycle

See: https://github.com/congow/Orient/issues

## Further documentation

If you want to take a look at a fancy PHPDoc documentation you can use doxygen:

    sudo apt-get install doxygen

and then use the script provided under the docs directory:

    doxygen docs/orient.d

which will generate technical documentation under the folder docs/html.
