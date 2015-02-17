<?php

namespace Graviton\Rql\AST;

use Graviton\Rql\Visitor\VisitorInterface;

abstract class AbstractOperation implements OperationInterface
{
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visit($this);
    }
}
