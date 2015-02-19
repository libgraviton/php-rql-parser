<?php

namespace Graviton\Rql\AST;

abstract class AbstractArrayOperation extends AbstractOperation implements ArrayOperationInterface
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var OperationInterface[]
     */
    private $array = array();

    /**
     * @var string $property property name
     *
     * @return void
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param string $value value operation
     *
     * @return void
     */
    public function addValue($value)
    {
        $this->array[] = $value;
    }

    /**
     * @return string[]
     */
    public function getArray()
    {
        return $this->array;
    }
}
