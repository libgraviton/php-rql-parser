<?php

// this doesn't work? try `composer install`
require 'vendor/autoload.php';

use Graviton\Rql\Query;

// please refer to https://github.com/persvr/rql/blob/master/test/query.js
// for example queries and intended results..

// impl inspiration: https://github.com/outlandishideas/RestBundle/blob/master/Controller/RestController.php

$query = '|gt(a,b)&eq(a,b)';

$query = '(gt(a,b)|eq(a,b))|eq(a,c)';

$query = '(gt(a,b)|eq(a,b))|eq(a,c)';

$p = new Query($query);

$parts = $p->parse();
var_dump($parts);