<?php
/**
 * our lexer
 */

namespace Graviton\Rql;

use Graviton\Rql\SubLexer\ImplicitBooleanSubLexer;
use Graviton\Rql\SubLexer\RelaxedStringSubLexer;
use Graviton\RqlParser\Lexer as BaseLexer;
use Graviton\RqlParser\SubLexer\ConstantSubLexer;
use Graviton\RqlParser\SubLexer\DatetimeSubLexer;
use Graviton\RqlParser\SubLexer\FiqlOperatorSubLexer;
use Graviton\RqlParser\SubLexer\GlobSubLexer;
use Graviton\RqlParser\SubLexer\NumberSubLexer;
use Graviton\RqlParser\SubLexer\PunctuationSubLexer;
use Graviton\RqlParser\SubLexer\RqlOperatorSubLexer;
use Graviton\RqlParser\SubLexer\SortSubLexer;
use Graviton\RqlParser\SubLexer\TypeSubLexer;
use Graviton\RqlParser\SubLexerChain;

/**
 * @author   List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://swisscom.ch
 */
class Lexer extends BaseLexer
{

    public static function createSubLexer()
    {
        return (new SubLexerChain())
            ->addSubLexer(new ConstantSubLexer())
            ->addSubLexer(new PunctuationSubLexer())
            ->addSubLexer(new FiqlOperatorSubLexer())
            ->addSubLexer(new RqlOperatorSubLexer())
            ->addSubLexer(new TypeSubLexer())

            ->addSubLexer(new GlobSubLexer())
            ->addSubLexer(new DatetimeSubLexer())

            // this is our own
            ->addSubLexer(new ImplicitBooleanSubLexer())

            // as we allow "-" in the middle of a string, this must come before our relaxed guy..
            ->addSubLexer(new SortSubLexer())

            ->addSubLexer(new RelaxedStringSubLexer())

            ->addSubLexer(new NumberSubLexer());
    }

    /**
     * Custom replace - to %2D for easier find by.
     *
     * @param string $code request uri params
     * @return \Graviton\RqlParser\TokenStream
     */
    public function tokenize($code)
    {
        return parent::tokenize($code);
    }
}
