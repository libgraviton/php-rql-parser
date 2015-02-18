<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Parser\ParserUtil;
use Graviton\Rql\AST\OperationFactory;

class QueryOperationStrategy extends ParsingStrategy
{
    /**
     * @return OperationInterface
     */
    public function parse()
    {
        $operation = OperationFactory::fromLexerToken($this->lexer->lookahead['type']);

        ParserUtil::parseStart($this->lexer);
        $operation->queries = array();
        $operation->queries[] = $this->resourceQuery();

        while ($hasQueries) {
            ParserUtil::parseComma($this->lexer, true);
            ParserUtil::parseQuery(); // ????
        }

        $operation->value = ParserUtil::parseArgument($this->lexer);
        ParserUtil::parseEnd($this->lexer);

        return $operation;
    }
}
