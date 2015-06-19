<?php
/**
 * parse rql input using the lexer
 */

namespace Graviton\Rql;

use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Parser as BaseParser;
use Graviton\Rql\Visitor\VisitorInterface;

/**
 * RQL Parser
 *
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class Parser
{
    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * @var BaseParser
     */
    private $parser;

    /**
     * @var VisitorInterface
     */
    private $visitor;

    /**
     * @param Lexer            $lexer   lexer
     * @param BaseParser       $parser  parser
     * @param VisitorInterface $visitor visitor
     */
    public function __construct(Lexer $lexer, BaseParser $parser, VisitorInterface $visitor)
    {
        $this->lexer = $lexer;
        $this->parser = $parser;
        $this->visitor = $visitor;
    }

    /**
     * @param string $rql rql expression
     *
     * @return
     */
    public function parse($rql)
    {
        $this->ast = $this->parser->parse(
            $this->lexer->tokenize($rql)
        );
        return $this->ast;
    }

    /**
     * @return mixed
     */
    public function buildQuery()
    {
        return $this->visitor->visit($this->ast);
    }
}
