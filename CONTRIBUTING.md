## Filing bug reports ##

Bugs or feature requests can be posted online on the
[GitHub issues](https://github.com/doctrine/orientdb-odm/issues) section of the project.

When reporting bugs, in addition to the obvious description of your issue you __must__ always provide
some essential information about your environment such as:

  1. version of the OrientDB ODM library (either a tagged release or a git commit ID).
  2. version of OrientDB.
  3. version of PHP.
  4. name and version of the operating system.
  5. when possible, a small snippet of code that reproduces the issue.

These details are essential to help us isolating issues and fixing them more promptly, which means it gets
easier for us if you provide as much details as possible.


## Contributing code ##

If you want to work on the OrientDB ODM it is highly recommended that you first run the test suite in
order to check that everything is OK and report strange behaviours or bugs. When modifying Orient please
make sure that no warnings or notices are emitted by PHP by running the interpreter in your development
environment with the `error_reporting` variable set to `E_ALL | E_STRICT`.

The recommended way to contribute to the OrientDB ODM is to fork the project on GitHub, create new topic
branches on your newly created repository to fix or add features (possibly with tests covering your
modifications) and then open a new pull request with a description of the applied changes.

When writing code please follow the [basic coding (PSR-1)](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
and [coding style (PSR-2)](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
standards and stick with the conventions used through the library to name classes and interfaces.

Please also follow some basic [commit guidelines](http://git-scm.com/book/ch5-2.html#Commit-Guidelines)
before opening pull requests.
