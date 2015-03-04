<?php

namespace Graviton\Rql\AST;

use Graviton\Rql\Visitor\VisitorInterface;

interface OperationInterface
{
    public function __construct($name);

    public function accept(VisitorInterface $visitor);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getProperty();

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return array<string[]>
     */
    public function getFields();

    /**
     * @return OperationInterface[]
     */
    public function getQueries();
}
