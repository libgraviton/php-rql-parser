<?php

namespace Graviton\Rql\Parser;

use Graviton\Rql\Lexer;

class ParserUtil
{
    static function parseStart(Lexer &$lexer)
    {
        $lexer->moveNext();
        if ($lexer->lookahead['type'] != Lexer::T_OPEN_PARENTHESIS) {
            self::syntaxError('missing open parenthesis');
        }
    }

    static function parseComma(Lexer &$lexer, $optional = false)
    {
        if (!$optional && $lexer->lookahead['type'] != Lexer::T_COMMA) {
            $this->syntaxError('missing comma');
        }
        $lexer->moveNext();
    }

    static function getString(Lexer &$lexer)
    {
        $lexer->moveNext();
        $string = null;
        if ($lexer->lookahead['type'] == Lexer::T_STRING) {
            $string = $lexer->lookahead['value'];
        } else {
            $this->syntaxError('no string found');
        }
        return $string;
    }

    static function parseArgument(Lexer &$lexer)
    {
        $lexer->moveNext();
        $string = null;
        if ($lexer->lookahead['type'] == Lexer::T_STRING) {
            $string = $lexer->lookahead['value'];
        } elseif ($lexer->lookahead['type'] == Lexer::T_INTEGER) {
            $string = (int) $lexer->lookahead['value'];
        } else {
            $this->syntaxError('no valid argument found');
        }
        return $string;
    }

    static function parseEnd(Lexer &$lexer)
    {
        $lexer->moveNext();
        if ($lexer->lookahead['type'] != Lexer::T_CLOSE_PARENTHESIS) {
            $this->syntaxError('missing close parenthesis');
        }
    }

    /**
     * @param string $message
     */
    static function syntaxError($message)
    {
        throw new \LogicException($message);
    }
}
