<?php
/**
 * interface for parser streategies to implement
 */

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Lexer;
use Graviton\Rql\Parser;
use Graviton\Rql\AST\OperationInterface;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
interface ParsingStrategyInterface
{
    /**
     * @param Parser $parser reference to parser
     *
     * @return void
     */
    public function setParser(Parser &$parser);

    /**
     * @param Lexer $lexer doctrine/lexer
     *
     * @return void
     */
    public function setLexer(Lexer &$lexer);

    /**
     * @return OperationInterface
     */
    public function parse();

    /**
     * @param int $type Lexer::T_* type
     *
     * @return bool
     */
    public function accepts($type);

    /**
     * @return int[]
     */
    public function getAcceptedTypes();
}
