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

All the SQL command are implemented but:

* Alter Table
* Alter Property
* Update of the maps
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

PHP >= 5.3.5

PHPUnit >= 3.5

OrientDB 0.9.25
