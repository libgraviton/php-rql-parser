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
     * @var mixed
     */
    public $value;

    /**
     * @var Operation[]
     */
    public $queries = array();

    /**
     * @var array<string[]>
     */
    public $fields = array();

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

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

    /**
     * @return array<string[]>
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return OperationInterface[]
     */
    public function getQueries()
    {
        return $this->queries;
    }

    public function accept(VisitorInterface $visitor)
    {
        foreach ($this->queries as $query) {
            $query->accept($visitor);
        }
        $visitor->visit($this);
    }
}
