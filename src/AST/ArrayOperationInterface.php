<?php

namespace Graviton\Rql\AST;

interface ArrayOperationInterface
{
    /**
     * @param string $property
     *
     * @return void
     */
    public function setProperty($property);

    /**
     * @return string
     */
    public function getProperty();

    /**
     * @param string $value possible value
     *
     * @return void
     */
    public function addValue($value);

    /**
     * @return string[]
     */
    public function getArray();
}
