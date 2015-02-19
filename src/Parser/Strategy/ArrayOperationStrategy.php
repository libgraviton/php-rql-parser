<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Parser\ParserUtil;
use Graviton\Rql\AST\OperationFactory;
use Graviton\Rql\AST\ArrayOperationInterface;
use Graviton\Rql\Lexer;

class ArrayOperationStrategy extends ParsingStrategy
{
    /**
     * @return ArrayOperationInterface
     */
    public function parse()
    {
        $operation = OperationFactory::fromLexerToken($this->lexer->lookahead['type']);

        if (!$operation instanceof ArrayOperationInterface) {
            throw new \RuntimeException;
        }

        $this->lexer->moveNext();
        $operation->setProperty(ParserUtil::getString($this->lexer));
        ParserUtil::parseComma($this->lexer);

        $this->lexer->moveNext();
        if ($this->lexer->lookahead['type'] == Lexer::T_OPEN_BRACKET) {
            $this->lexer->moveNext();
        } else {
            ParserUtil::syntaxError('Missing [ in params');
        }

        $hasValues = true;
        while ($hasValues) {
            if ($this->lexer->lookahead['type'] == Lexer::T_COMMA) {
                $this->lexer->moveNext();
            }
            if ($this->lexer->lookahead['type'] == Lexer::T_STRING) {
                $operation->addValue($this->lexer->lookahead['value']);
                $this->lexer->moveNext();
            }
            if ($this->lexer->lookahead == null || $this->lexer->lookahead['type'] == Lexer::T_CLOSE_BRACKET) {
                $hasValues = false;
            }
        }

        return $operation;
    }

    public function getAcceptedTypes()
    {
        return array(
            Lexer::T_IN,
            Lexer::T_OUT,
        );
    }
}
