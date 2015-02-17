<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Parser\ParserUtil;
use Graviton\Rql\AST\OperationFactory;

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
}
