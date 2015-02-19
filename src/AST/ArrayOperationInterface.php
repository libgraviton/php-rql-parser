<?php

namespace Graviton\Rql\AST;

interface ArrayOperationInterface
{
    /**
     * @return string
     */
    public function getProperty();

    /**
     * @return OperationInterface[]
     */
    public function getArray();
}
