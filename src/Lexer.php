<?php
/**
 * XIAG declined our PR to fix a needed search by values with $
 */

namespace Graviton\Rql;

use Xiag\Rql\Parser\Lexer as BaseLexer;

/**
 * @author   List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://swisscom.ch
 */
class Lexer extends BaseLexer
{
    // Overriding this to include $ search by.
    const REGEX_VALUE       = '/(\w|\$|\-|\+|\*|\?|\:|\.|\%[0-9a-f]{2})+/Ai';

    /**
     * Custom replace - to %2D for easier find by.
     *
     * @param string $code request uri params
     * @return \Xiag\Rql\Parser\TokenStream
     */
    public function tokenize($code)
    {
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

        return parent::tokenize($code);
    }
}
