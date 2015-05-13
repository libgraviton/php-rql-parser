<?php
/**
 * some basic utils for parsers
 *
 * @todo this should most likely be moved to the (abstract-)parser himself
 */

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
    /**
     * @param Lexer $lexer doctrine/lexer
     *
     * @return void
     */
    public static function parseStart(Lexer &$lexer)
    {
        $lexer->moveNext();
        if ($lexer->lookahead['type'] != Lexer::T_OPEN_PARENTHESIS) {
            self::syntaxError('missing open parenthesis');
        }
    }

    /**
     * @param Lexer $lexer    doctrine/lexer
     * @param bool  $optional is the comma optional?
     *
     * @return bool
     */
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

    /**
     * @param Lexer $lexer doctrine/lexer
     * @param bool  $move  should i moveNext before getting a string?
     *
     * @return string
     */
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

    /**
     * @param Lexer $lexer doctrine/lexer
     *
     * @return string
     */
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
        if ($string === 'true') {
            $string = true;
        }
        if ($string === 'false') {
            $string = false;
        }
        return $string;
    }

    /**
     * @param Lexer $lexer doctrine/lexer
     *
     * @return void
     */
    public static function parseEnd(Lexer &$lexer)
    {
        $lexer->moveNext();
        if ($lexer->lookahead['type'] != Lexer::T_CLOSE_PARENTHESIS) {
            self::syntaxError('missing close parenthesis');
        }
    }

    /**
     * @param string $message error message
     *
     * @return void
     */
    public static function syntaxError($message)
    {
        throw new \LogicException($message);
    }
}
