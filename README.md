php-rql-parser - A PHP RQL Parsing Library
==============

**Important: Don't use this just yet for production projects, it's in development**

This is a small RQL (Resource Query Language) parsing library written in PHP (a port of the js implementation at https://github.com/persvr/rql/),
allowing a flexible integration of your own business logic and into your application, while still having the parsing logic decoupled. Read on if this is what you need.. ;-)  

## How to use

Basically, this library consists of the following parts:

* A simple PHP RQL Parsing library, parsing a RQL expression into an array form
* An interface that you can implement within a class *of yours*. The inherited class will receive calls on the interface functions in order to implement the query in your business logic.
* An example implementation of that interface, covering the use case of a Doctrine ODM MongoDB based backend.

## Use with composer

This library is in [Packagist](https://packagist.org/packages/graviton/php-rql-parser), so you can use it with Composer. Just put this in your ``composer.json``:

    require: ["graviton/php-rql-parser": "dev-master"]

## Current state

Currently, this is in alpha state. If you urgently *need* a RQL parsing engine, consider contributing to this one ;-).

What it **does** currently

* Parse the basic RQL structure of ``func(param)`` and applying that to your business logic. Currently implemented methods include stuff like ``eq(), ne(), sort()`` and so on..
* Handing simple AND and OR conditions, passing that along
* It has simple casting of bools & nulls, so you can match those with ``eq()``

What is **doesn't** do

* It doesn't support anything else then ``func(param)``, so no special stuff like explicit type checking, existence checking and stuff like that. 
* It doesn't support hierarchical condition nesting like ``((eq(field,value)|eq(field2,value2))|ne(field3,val3))``. It 
parses it correctly, but the interface implementation is not currently able to apply it the correct way.

## Example usage

Once you added the library to your project via Composer, you can use it as follows (in this example we use the example
business logic implementation for a Doctrine ODM MongoDB backend). If you have another use case (i.e. not using Doctrine ODM), please
 read the next section on how to implement your logic.

````php
use Graviton\Rql\Queriable\MongoOdm;
use Graviton\Rql\Query;

// get the query string from somewhere - your framework.
// we expect a string like "eq(field,fieldValue)&gt(fieldName,55)"
$q = $request->getQueryString();

$queryParser = new Query($q);

$queriable = new MongoOdm($this->repository);
$queriable = $queryParser->applyToQueriable($queriable);

$records = $queriable->getDocuments();
````

## How to integrate your business logic

As it should be obvious, the parsing of the user supplied query is always the same process. This is in contrast to the *applying
of the conditions supplied by the user on your data set*. No library can obviously cover all possible use cases.

To make that easy, this library ships with an interface class called ``\Graviton\Rql\QueryInterface``. This interface
provides functions for all supported query functions. To implement the code necessary to filter/query the data in your application,
just create a class implementing this interface. You can refer to the supplied example implementation of ``\Graviton\Rql\Queriable\MongoOdm``.

In summary, the workflow is as follows:

* Create an instance of the ``Query`` class, passing the user RQL query (as string).
* Create a class that implements the ``\Graviton\Rql\QueryInterface`` interface and pass it to ``applyToQueriable()`` in the ``Query`` class. All the conditions in the specified query will then be called on that class.
* Work again with your implementation to get the results you need (this is again use case specific).
