<?php

namespace Graviton\Rql\Parser;

use Graviton\Rql\Lexer;
use Graviton\Rql\Parser\ParserUtil;

class ParserUtil
{
    public static function parseStart(Lexer &$lexer)
    {
        $lexer->moveNext();
        if ($lexer->lookahead['type'] != Lexer::T_OPEN_PARENTHESIS) {
            self::syntaxError('missing open parenthesis');
        }
    }

    public static function parseComma(Lexer &$lexer, $optional = false)
    {
        $lexer->moveNext();
        if (!$optional && $lexer->lookahead['type'] != Lexer::T_COMMA) {
            self::syntaxError('missing comma');
        }
    }

    public static function getString(Lexer &$lexer, $move = true)
    {
        $move && $lexer->moveNext();
        $string = null;
        if ($lexer->lookahead['type'] == Lexer::T_STRING) {
            $string = $lexer->lookahead['value'];
        } else {
            self::syntaxError('no string found');
        }
        return $string;
    }

    public static function parseArgument(Lexer &$lexer)
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

    public static function parseEnd(Lexer &$lexer)
    {
        $lexer->moveNext();
        if ($lexer->lookahead['type'] != Lexer::T_CLOSE_PARENTHESIS) {
            self::syntaxError('missing close parenthesis');
        }
    }

    /**
     * @param string $message
     */
    public static function syntaxError($message)
    {
        throw new \LogicException($message);
    }
}
