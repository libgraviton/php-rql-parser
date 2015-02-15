<?php

namespace Graviton\Rql\AST;

class Operation
{
    public function __construct($name, $property)
    {
        $this->name = $name;
        $this->property = $property;
    }
}
