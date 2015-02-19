<?php

namespace Graviton\Rql\AST;

interface SortOperationInterface
{
    /**
     * @param string[]
     */
    public function addField($field);

    /**
     * @return array<string[]>
     */
    public function getFields();
}
