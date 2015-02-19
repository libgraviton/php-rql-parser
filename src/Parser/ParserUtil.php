<?php

namespace Graviton\Rql\Parser;

use Graviton\Rql\Lexer;
use Graviton\Rql\Parser\ParserUtil;

class ParserUtil
{
    static public function parseStart(Lexer &$lexer)
    {
        $lexer->moveNext();
        if ($lexer->lookahead['type'] != Lexer::T_OPEN_PARENTHESIS) {
            self::syntaxError('missing open parenthesis');
        }
    }

    static public function parseComma(Lexer &$lexer, $optional = false)
    {
        $lexer->moveNext();
        if (!$optional && $lexer->lookahead['type'] != Lexer::T_COMMA) {
            self::syntaxError('missing comma');
        }
    }

    static public function getString(Lexer &$lexer)
    {
        $lexer->moveNext();
        $string = null;
        if ($lexer->lookahead['type'] == Lexer::T_STRING) {
            $string = $lexer->lookahead['value'];
        } else {
            self::syntaxError('no string found');
        }
        return $string;
    }

    static public function parseArgument(Lexer &$lexer)
    {
        $lexer->moveNext();
        $string = null;
        if ($lexer->lookahead['type'] == Lexer::T_STRING) {
            $string = $lexer->lookahead['value'];
        } elseif ($lexer->lookahead['type'] == Lexer::T_INTEGER) {
            $string = (int) $lexer->lookahead['value'];
        } else {
            self::syntaxError('no valid argument found');
        }
        return $string;
    }

    static public function parseEnd(Lexer &$lexer)
    {
        $lexer->moveNext();
        if ($lexer->lookahead['type'] != Lexer::T_CLOSE_PARENTHESIS) {
            self::syntaxError('missing close parenthesis');
        }
    }

    /**
     * @param string $message
     */
    static public function syntaxError($message)
    {
        throw new \LogicException($message);
    }
}
