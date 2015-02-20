<?php

namespace Graviton\Rql\AST;

abstract class AbstractPropertyOperation extends AbstractOperation implements PropertyOperationInterface
{
    /**
     * @var string
     */
    private $property = '';

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $property name
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
     * @param mixed $value value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
