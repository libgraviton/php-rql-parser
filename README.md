php-rql-parser - Query MongoDB using RQL in PHP
==============

[![Build Status](https://travis-ci.org/libgraviton/php-rql-parser.svg?branch=develop)](https://travis-ci.org/libgraviton/php-rql-parser) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/libgraviton/php-rql-parser/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/libgraviton/php-rql-parser/?branch=develop) [![Code Coverage](https://scrutinizer-ci.com/g/libgraviton/php-rql-parser/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/libgraviton/php-rql-parser/?branch=develop) [![Latest Stable Version](https://poser.pugx.org/graviton/php-rql-parser/v/stable.svg)](https://packagist.org/packages/graviton/php-rql-parser) [![Total Downloads](https://poser.pugx.org/graviton/php-rql-parser/downloads.svg)](https://packagist.org/packages/graviton/php-rql-parser) [![Latest Unstable Version](https://poser.pugx.org/graviton/php-rql-parser/v/unstable.svg)](https://packagist.org/packages/graviton/php-rql-parser) [![License](https://poser.pugx.org/graviton/php-rql-parser/license.svg)](https://packagist.org/packages/graviton/php-rql-parser)


This is a wrapper around [xiag-ag/rql-parser](https://github.com/xiag-ag/rql-parser) that combines that parser with a small layer of mongodb integration.

This package adheres to [SemVer](http://semver.org/spec/v2.0.0.html) versioning. It will be considered stable after reaching 2.x since the initial 1.x release is
considered rather buggy.

It uses a github version of [git-flow](http://nvie.com/posts/a-successful-git-branching-model/) in which new features and bugfixes must be merged to develop
using a github pull request. It uses the standard git-flow naming conventions with the addition of a 'v' prefix to version tags.

## Installation

Install it using [composer](https://getcomposer.org/).

```bash
composer require graviton/php-rql-parser
```

## Usage

```php
<?php

require 'vendor/autoload.php';

$rql = 'or(eq(name,foo)&eq(name,bar))';

/** @var \Doctrine\ODM\MongoDB\Query\Builder $builder */
$visitor = new \Graviton\Rql\Visitor\MongoOdm($builder);
$lexer = new \Xiag\Rql\Parser\Lexer;
$parser = \Xiag\Rql\Parser\Parser::createDefault();

// parse some Resource Query Language 
$rqlQuery = $parser->parse($lexer->tokenize($rql));

// get query
$query = $visitor->visit($rqlQuery)->getQuery();

// ...
```

## Development

We welcome contributions on the develop branch.
