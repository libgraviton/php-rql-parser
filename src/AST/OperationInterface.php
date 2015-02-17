<?php

namespace Graviton\Rql\AST;

use Graviton\Rql\Visitor\VisitorInterface;

interface OperationInterface
{
    public function accept(VisitorInterface $visitor);
}
