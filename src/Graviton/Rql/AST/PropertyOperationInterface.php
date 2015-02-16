<?php

namespace Graviton\Rql\AST;

use Graviton\Rql\Visitor\VisitorInterface;

interface PropertyOperationInterface
{
    /**
     * @return string
     */
    public function getProperty();

    /**
     * @return mixed
     */
    public function getValue();
}
