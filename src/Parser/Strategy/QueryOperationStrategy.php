<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Parser\ParserUtil;
use Graviton\Rql\AST\OperationFactory;
use Graviton\Rql\AST\QueryOperationInterface;
use Graviton\Rql\Lexer;

class QueryOperationStrategy extends ParsingStrategy
{
    /**
     * @return QueryOperationInterface
     */
    public function parse()
    {
        $operation = OperationFactory::fromLexerToken($this->lexer->lookahead['type']);

        ParserUtil::parseStart($this->lexer);
        $operation->queries = array();
        $operation->queries[] = $this->parser->resourceQuery();

        $hasQueries = true;
        while ($hasQueries) {
            ParserUtil::parseComma($this->lexer, true);
            $query = $this->parser->resourceQuery();
            if ($query) {
                $operation->queries[] = $query;
            }
            $hasQueries = $this->lexer->lookahead['type'] == Lexer::T_CLOSE_PARENTHESIS;
        }

        return $operation;
    }

    public function getAcceptedTypes()
    {
        return array(
            Lexer::T_AND,
            Lexer::T_OR,
        );
    }
}
