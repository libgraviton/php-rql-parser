<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Lexer;

abstract class ParsingStrategy implements ParsingStrategyInterface
{
    public function __construct(Lexer &$lexer)
    {
        $this->lexer =& $lexer;
    }
}
