<?php

namespace Graviton\Rql\AST;

use Graviton\Rql\Visitor\VisitorInterface;

class Operation implements OperationInterface
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $property = '';

    /**
     * @var Operation[]
     */
    public $queries = array();

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function accept(VisitorInterface $visitor)
    {
        foreach ($this->queries as $query) {
            $query->accept($visitor);
        }
        $visitor->visit($this);
    }
}
