<?php

namespace Graviton\Rql\Visitor;

use Graviton\Rql\AST\OperationInterface;

interface VisitorInterface
{
    public function visit(OperationInterface $operation);
}
