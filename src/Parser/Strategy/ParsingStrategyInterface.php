<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Lexer;
use Graviton\Rql\AST\OperationInterface;

interface ParsingStrategyInterface
{
    public function __construct(Lexer &$lexer);

    /**
     * @return OperationInterface
     */
    public function parse();
}
