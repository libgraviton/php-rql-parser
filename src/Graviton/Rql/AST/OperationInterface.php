<?php

namespace Graviton\Rql\AST;

use Graviton\Rql\Visitor\VisitorInterface;

interface OperationInterface
{
        public function __construct($name);

        public function accept(VisitorInterface $visitor);
}
