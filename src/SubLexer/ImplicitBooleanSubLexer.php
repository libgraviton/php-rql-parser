<?php
namespace Graviton\Rql\SubLexer;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\SubLexerInterface;

class ImplicitBooleanSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        $test4 = substr($code, $cursor, 4);
        if ($test4 === 'true') {
            return new Token(Token::T_TRUE, $test4, $cursor, $cursor + 4);
        }

        $test5 = substr($code, $cursor, 5);
        if ($test5 === 'false') {
            return new Token(Token::T_FALSE, $test5, $cursor, $cursor + 5);
        }

        return null;
    }
}
