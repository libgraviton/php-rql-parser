<?php

namespace Graviton\Rql\AST;

interface PropertyOperationInterface
{
    /**
     * @param string $property name
     */
    public function setProperty($property);

    /**
     * @return string
     */
    public function getProperty();

    /**
     * @param mixed $value value
     */
    public function setValue($value);

    /**
     * @return mixed
     */
    public function getValue();
}
