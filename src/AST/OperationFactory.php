<?php

namespace Graviton\Rql\AST;

use Graviton\Rql\Lexer;
use Graviton\AST;

class OperationFactory
{
    static protected function getLexerMap() {
        return array(
            Lexer::T_EQ => 'Graviton\Rql\AST\EqOperation',
            Lexer::T_NE => 'Graviton\Rql\AST\NeOperation',
            Lexer::T_GT => 'Graviton\Rql\AST\GtOperation',
            Lexer::T_LT => 'Graviton\Rql\AST\LtOperation',
            Lexer::T_LTE => 'Graviton\Rql\AST\LteOperation',
            Lexer::T_GTE => 'Graviton\Rql\AST\GteOperation',
            Lexer::T_LIKE => 'Graviton\Rql\AST\LikeOperation',
            Lexer::T_AND => 'Graviton\Rql\AST\AndOperation',
            Lexer::T_OR => 'Graviton\Rql\AST\OrOperation',
            Lexer::T_IN => 'Graviton\Rql\AST\InOperation',
            Lexer::T_OUT => 'Graviton\Rql\AST\OutOperation',
            Lexer::T_SORT => 'Graviton\Rql\AST\SortOperation',
            Lexer::T_LIMIT => 'Graviton\Rql\AST\LimitOperation',
        );
    }

    /**
     * @return OperationInterface
     */
    static public function fromLexerToken($token)
    {
        if (in_array($token, array_keys(self::getLexerMap()))) {
            $className = self::getLexerMap()[$token];
            return new $className;
        }
        throw new \RuntimeException(sprintf('Could not find class for token %s', $token));
    }
}
