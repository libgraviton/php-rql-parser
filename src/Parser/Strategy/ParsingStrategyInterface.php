<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Lexer;
use Graviton\Rql\Parser;
use Graviton\Rql\AST\OperationInterface;

interface ParsingStrategyInterface
{
    public function setParser(Parser &$parser);

    public function setLexer(Lexer &$lexer);

    /**
     * @return OperationInterface
     */
    public function parse();

    /**
     * @param int $type Lexer::T_* type
     *
     * @return bool
     */
    public function accepts($type);

    /**
     * @return int[]
     */
    public function getAcceptedTypes();
}
