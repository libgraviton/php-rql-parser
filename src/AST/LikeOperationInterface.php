<?php

namespace Graviton\Rql\AST;

interface LimitOperationInterface
{
    /**
     * @return int
     */
    public function getLimit();

    /**
     * @return int
     */
    public function getSkip();
}
