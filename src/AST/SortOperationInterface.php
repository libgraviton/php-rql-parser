<?php

namespace Graviton\Rql\AST;

interface SortOperationInterface
{
    /**
     * @return array<string[]>
     */
    public function getFields();
}
