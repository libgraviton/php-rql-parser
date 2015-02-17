<?php

namespace Graviton\Rql\AST;

use Graviton\Rql\Visitor\VisitorInterface;

abstract class AbstractPropertyOperation extends AbstractOperation implements PropertyOperationInterface
{
    /**
     * @var string
     */
    public $property = '';

    /**
     * @var mixed
     */
    public $value;

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
