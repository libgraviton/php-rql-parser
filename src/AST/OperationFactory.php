<?php

namespace Graviton\Rql\AST;

use Graviton\Rql\Lexer;
use Graviton\AST;

class OperationFactory
{
    private $lexerMap = array(
        Lexer::T_EQ => 'AST\EqOperation',
    );

    /**
     * @return OperationInterface
     */
    static function fromLexerToken($token)
    {
        if (in_array($token, array_keys($this->lexerMap))) {
            $className = $this->lexerMap[$token];
            return new $className;
        }
        throw new \RuntimeException(sprintf('Could not find class for token %s', $token));
    }

}
