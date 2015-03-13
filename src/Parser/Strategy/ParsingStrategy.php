<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Lexer;
use Graviton\Rql\Parser;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
abstract class ParsingStrategy implements ParsingStrategyInterface
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var Lexer
     */
    protected $lexer;

    public function setParser(Parser &$parser)
    {
        $this->parser =& $parser;
    }

    public function setLexer(Lexer &$lexer)
    {
        $this->lexer =& $lexer;
    }

    public function accepts($type)
    {
        return in_array(
            $type,
            $this->getAcceptedTypes()
        );
    }
}
