<?php

namespace Graviton\Rql\AST;

abstract class AbstractPropertyOperation extends AbstractOperation implements PropertyOperationInterface
{
    /**
     * @var string
     */
    protected $property = '';

    /**
     * @var mixed
     */
    protected $value;

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
