<?php

namespace Graviton\Rql\AST;

abstract class AbstractArrayOperation extends AbstractOperation implements ArrayOperationInterface
{
    /**
     * @var string
     */
    public $property;

    /**
     * @var OperationInterface[]
     */
    public $array = array();

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @return OperationInterface[]
     */
    public function getArray()
    {
        return $this->array;
    }
}
