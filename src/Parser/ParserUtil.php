<?php

namespace Graviton\Rql\Parser;

class ParserUtil
{
    static function parseStart(&$lexer)
    {
        $lexer->moveNext();
        if ($lexer->lookahead['type'] != Lexer::T_OPEN_PARENTHESIS) {
            self::syntaxError('missing open parenthesis');
        }
    }
}
