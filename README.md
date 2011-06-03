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

All the SQL command are implemented but:

* index management ( http://code.google.com/p/orient/wiki/Indexes )

and, in order to use it in production, integration tests need to be written.

## Current status of the mapper

Not started yet.

## Tests & reports

In order to run the tests you only need to:

    cd /path/to/repo
    phpunit --configuration=Test/PHPUnit/phpunit.xml

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
* OrientDB 1.0.0RC1

In order to launch the test suite PHPUnit 3.5 is required.

## Known issues

Updating of the Maps is currently bugged within the SQL statement constructors.
