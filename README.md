php-rql-parser - Query MongoDB using RQL in PHP
==============

This is a wrapper around [libgraviton/rql-parser](https://github.com/libgraviton/rql-parser) that combines that parser with a small layer of mongodb integration.

This package adheres to [SemVer](http://semver.org/spec/v2.0.0.html) versioning.

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
$visitor = new \Graviton\Rql\Visitor\MongoOdm();
$visitor->setBuilder($builder);
$lexer = new \Graviton\RqlParser\Lexer;
$parser = \Graviton\RqlParser\Parser::createDefault();

// parse some Resource Query Language 
$rqlQuery = $parser->parse($lexer->tokenize($rql));

// get query
$query = $visitor->visit($rqlQuery)->getQuery();

// ...
```

## Development

We welcome contributions on the develop branch.
