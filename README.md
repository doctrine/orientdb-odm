# OrientDB PHP Library

[![Build Status](https://secure.travis-ci.org/doctrine/orientdb-odm.png?branch=master)](http://secure.travis-ci.org/doctrine/orientdb-odm)
[![Total Downloads](https://poser.pugx.org/doctrine/orientdb-odm/downloads.png)](https://packagist.org/packages/doctrine/orientdb-odm)
[![Latest Stable Version](https://poser.pugx.org/doctrine/orientdb-odm/v/stable.png)](https://packagist.org/packages/doctrine/orientdb-odm)
## What's Orient?

A set of tools to use and manage any OrientDB instance from PHP.

Orient includes:

* the HTTP protocol binding
* the query builder
* the data mapper ( Object Graph Mapper )

If you don't know [OrientDB](http://www.orientechnologies.com/) take a look at its [Documentation](http://code.google.com/p/orient).


## After cloning

In order to be able to run the examples and the test suite provided by Orient, you must first enter the root of
the cloned repository and initialize all the needed dependencies using [Composer](http://getcomposer.org/).
We provide an utility script in the `bin/` subdirectory to ease this process so you can just do the following:

```
$ ./bin/initialize-dependencies.sh
```


## Current status of the binding

The binding is complete: it is an HTTP client wrapper with some methods bound to OrientDB's HTTP interface.

Its usage is straightforward:

```
$parameters = Doctrine\OrientDB\Binding\BindingParameters::create('http://admin:admin@127.0.0.1:2480/demo');
$orient = new Doctrine\OrientDB\Binding\HttpBinding($parameters);
$output = $orient->query("SELECT FROM Address");

foreach ($output->getResult() as $address) {
    var_dump($address->street);
}
```

Use the PHP5.3 standard autoloader (https://gist.github.com/221634).


## Current status of the query builder

The query-builder is finished, in the future we will consider the integration of OrientDB
Graph Edition: http://code.google.com/p/orient/wiki/GraphEdTutorial.

To take advantage of the QB you only have to instantiate a Query object:

```
use Doctrine\OrientDB\Query\Query;

$query = new Query();
$query->from(array('users'))->where('username = ?', "admin");

echo $query->getRaw();      // SELECT FROM users WHERE username = "admin"
```

The Query object incapsulates lots of sub-commands, like SELECT, DROP, GRANT, INSERT
and so on...

You can use also those commands:

```
use Doctrine\OrientDB\Query\Command\Select;

$select = new Select(array('users'));
echo $select->getRaw();     // SELECT FROM users
```

However, we strongly discourage this approach: commands will change, Query, thought as a facade, - hopefully - not.

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

```
use Doctrine\OrientDB\Graph\Graph;
use Doctrine\OrientDB\Graph\Vertex;
use Doctrine\OrientDB\Graph\Algorithm\Dijkstra;

$graph = new Graph();

$rome = new Vertex('Rome');
$paris = new Vertex('Paris');
$london = new Vertex('London');

$rome->connect($paris, 2);
$rome->connect($london, 3);
$paris->connect($london, 1);

$graph->add($rome);
$graph->add($paris);
$graph->add($london);

$algorithm = new Dijkstra($graph);
$algorithm->setStartingVertex($rome);
$algorithm->setEndingVertex($london);

var_dump($algorithm->solve());
```


## Tests

The test suite can be launched simply by executing `phpunit` from the root directory of the repository.

By default the suite does not perform integration tests to verify the correct behaviour of our implementation against a running instance of OrientDB.
Since integration tests are marked using the [@group](http://www.phpunit.de/manual/current/en/appendixes.annotations.html#appendixes.annotations.group)
annotation, they can be enabled by default via `phpunit.xml` by adding a comment to the `integration` group in the list of excluded groups or,
if you just want to execute them on a single execution basis, first load fixtures with this script

```
php ./test/Integration/fixtures/load.php
```

followeb by launching the suite with the additional `--group` argument:

```
phpunit --group __nogroup__,integration
```

It is also possible to generate a HTML report showing the code health of the library using `PHP_CodeBrowser` paired with the following dependencies
(in addition to `phpunit`):

* phpcpd
* phpdcd
* phploc
* phpmd
* phpdepend
* phpcb

Executing `./bin/report.sh` from the root directory of the repository will generate the report in `log/report/index.html`.


## Requirements

These are the requirements in order to use the library:

* PHP >= 5.3.3
* OrientDB >= 1.2.0

In order to launch the test suite PHPUnit 3.6 is required.


## Tracker & software lifecycle

See: https://github.com/doctrine/orientdb-odm/issues


## Further documentation

If you want to take a look at a fancy PHPDoc documentation you can use doxygen:

```
sudo apt-get install doxygen
```

and then use the script provided under the docs directory:

```
doxygen docs/orient.d
```

which will generate technical documentation under the folder docs/html.
