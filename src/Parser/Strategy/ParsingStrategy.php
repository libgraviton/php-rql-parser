<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Lexer;
use Graviton\Rql\Parser;

abstract class ParsingStrategy implements ParsingStrategyInterface
{
    public function setParser(Parser &$parser)
    {
        $this->parser =& $parser;
    }

    public function setLexer(Lexer &$lexer)
    {
        $this->lexer =& $lexer;
    }

    public function accepts($type)
    {
        return in_array(
            $type,
            $this->getAcceptedTypes()
        );
    }
}
