<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Parser\ParserUtil;
use Graviton\Rql\AST\OperationFactory;
use Graviton\Rql\AST\OperationInterface;
use Graviton\Rql\Lexer;

class PropertyOperationStrategy extends ParsingStrategy
{
    /**
     * @return OperationInterface
     */
    public function parse()
    {
        $operation = OperationFactory::fromLexerToken($this->lexer->lookahead['type']);

        ParserUtil::parseStart($this->lexer);
        $operation->property = ParserUtil::getString($this->lexer);
        ParserUtil::parseComma($this->lexer);
        $operation->value = ParserUtil::parseArgument($this->lexer);
        ParserUtil::parseEnd($this->lexer);

        return $operation;
    }

    public function getAcceptedTypes()
    {
        return array(
            Lexer::T_EQ,
            Lexer::T_NE,
            Lexer::T_LT,
            Lexer::T_GT,
            Lexer::T_LTE,
            Lexer::T_GTE,
            Lexer::T_LIKE
        );
    }
}
