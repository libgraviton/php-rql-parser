<?php

namespace Graviton\Rql\Parser;

use Graviton\Rql\Lexer;
use Graviton\Rql\Parser\ParserUtil;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
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
        $return = true;
        $lexer->moveNext();
        if ($lexer->lookahead['type'] != Lexer::T_COMMA) {
            $optional || self::syntaxError('missing comma');
            $return = false;
        }
        return $return;
    }

    public static function getString(Lexer &$lexer, $move = true)
    {
        $move && $lexer->moveNext();
        $string = null;
        if ($lexer->lookahead['type'] == Lexer::T_STRING) {
            $string = $lexer->lookahead['value'];
            $glimpse = $lexer->glimpse();
            if ($glimpse['type'] == Lexer::T_MINUS ||
                $glimpse['type'] == Lexer::T_PLUS
            ) {
                $lexer->moveNext();
                $string = $string . $lexer->lookahead['value'] . self::getString($lexer);
            }
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
            $string = self::getString($lexer, false);
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
