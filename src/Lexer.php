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
            ->addSubLexer(new NumberSubLexer())

            ->addSubLexer(new SortSubLexer())

            // our own stuff
            ->addSubLexer(new ImplicitBooleanSubLexer())
            ->addSubLexer(new RelaxedStringSubLexer());
    }

    /**
     * Custom replace - to %2D for easier find by.
     *
     * @param string $code request uri params
     * @return \Graviton\RqlParser\TokenStream
     */
    public function tokenize($code)
    {

        /*
        // Replace for each string value between (), there can be many rql params.
        if (strpos($code, 'string:') !== false) {
            preg_match_all('/\bstring:(.*?)[\(\)&,|(\s)]/', $code.' ', $matches);
            if (array_key_exists(1, $matches) && !empty($matches)) {
                foreach ($matches[1] as $match) {
                    if (strpos($match, '-') !== false) {
                        $new = preg_replace('/-/', '%2D', $match);
                        $code = preg_replace('/' . $match . '/', $new, $code, 1);
                    }
                }
            }
        }
        */

        return parent::tokenize($code);
    }
}
