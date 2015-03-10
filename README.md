php-rql-parser - A PHP RQL Parsing Library
==============

[![Build Status](https://travis-ci.org/libgraviton/php-rql-parser.svg?branch=develop)](https://travis-ci.org/libgraviton/php-rql-parser) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/libgraviton/php-rql-parser/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/libgraviton/php-rql-parser/?branch=develop) [![Code Coverage](https://scrutinizer-ci.com/g/libgraviton/php-rql-parser/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/libgraviton/php-rql-parser/?branch=develop) [![Latest Stable Version](https://poser.pugx.org/graviton/php-rql-parser/v/stable.svg)](https://packagist.org/packages/graviton/php-rql-parser) [![Total Downloads](https://poser.pugx.org/graviton/php-rql-parser/downloads.svg)](https://packagist.org/packages/graviton/php-rql-parser) [![Latest Unstable Version](https://poser.pugx.org/graviton/php-rql-parser/v/unstable.svg)](https://packagist.org/packages/graviton/php-rql-parser) [![License](https://poser.pugx.org/graviton/php-rql-parser/license.svg)](https://packagist.org/packages/graviton/php-rql-parser)


This is a small [RQL](https://github.com/persvr/rql/) (Resource Query Language) parsing library written in PHP. It uses [doctrine/lexer](https://github.com/doctrine/lexer) and
a doctrine inspired parser to create and abstract syntax tree of the query. While it ships with a [doctrine/mongo-odm](https://github.com/doctrine/mongodb-odm) querybuilding
visitor writing you own should not be hard.

This package adheres to [SemVer](http://semver.org/spec/v2.0.0.html) versioning. It will be considered stable after reaching 2.x since the initial 1.x release is
considered too buggy at the moment.

It uses a github version of [git-flow](http://nvie.com/posts/a-successful-git-branching-model/) in which new features and bugfixes must be merged to develop
using a github pull request. It uses the standard git-flow naming conventions with the addition of a 'v' prefix to version tags.

This library is under heavy development. If you need an RQL parser for PHP please consider pitching in by contributing to this parser.

## Overview

This library consists of the following parts:

* a lexer and parser for creating an abstract syntax tree from rql code
* a visitor implementation for using a mongo-odm quiery builder with the abstract syntax tree
* unit and acceptance tests for all this

## Installation

Install it using composer.

```bash
composer require graviton/php-rql-parser
```

## Current state

All this can currently do is parse a very limited subset of rql. Please have a look 
at ``test/Graviton/Rql/LexerTest.php`` and ``test/Graviton/Rql/ParserTest.php`` to
see what the parser currently supports.

The mongo-odm visitor only supports a limited subset of rql. Look at ``test/Graviton/Rql/MongoOdmTest.php``
to see how to use it and what is supported.

## Development

We welcome contributions on the develop branch.

Please look at the ``src/Graviton/Rql/Visitor/`` for examples of how to traverse the abstract syntax
tree if you need to implement some thing based off the parser.
