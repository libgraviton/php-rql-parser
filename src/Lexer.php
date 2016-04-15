<?php
/**
 * XIAG declined our PR to fix a needed search by values with $
 */

namespace Graviton\Rql;

use Xiag\Rql\Parser\Lexer as BaseLexer;

/**
 * @author   List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
class Lexer extends BaseLexer
{
    // Overriding this to include $ search by.
    const REGEX_VALUE       = '/(\w|\$|\-|\+|\*|\?|\:|\.|\%[0-9a-f]{2})+/Ai';
}
