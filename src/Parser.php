<?php

namespace Graviton\Rql;

use Graviton\Rql\Parser\Strategy;
use Graviton\Rql\Parser\Strategy\ParsingStrategyInterface;

/**
 * RQL Parser
 * This class tries to form array structures form RQL queries.
 * It aims to be a reference implementation port to PHP of the js version located at
 * https://github.com/persvr/rql/blob/master/parser.js
 *
 * @category Graviton
 * @package  Rql
 * @author   Dario Nuevo <dario.nuevo@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
class Parser
{
    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * @var ParsingStrategyInterface[]
     */
    private $strategies = array();

    /**
     * @var string<int>
     */
    private $internalOperations = array(
        Lexer::T_SORT => 'sortOperation',
        Lexer::T_LIMIT => 'limitOperation',
    );

    static public function createParser($rql)
    {
        $parser = new Parser($rql);

        $parser->addStrategy(new Strategy\PropertyOperationStrategy);
        $parser->addStrategy(new Strategy\QueryOperationStrategy);
        $parser->addStrategy(new Strategy\ArrayOperationStrategy);
        $parser->addStrategy(new Strategy\SortOperationStrategy);
        $parser->addStrategy(new Strategy\LimitOperationStrategy);

        return $parser;
    }

    /**
     * create parser and lex input
     *
     * @param string $rql rql to lex
     */
    public function __construct($rql)
    {
        $this->lexer = new Lexer;
        $this->lexer->setInput($rql);
    }

    public function addStrategy(ParsingStrategyInterface $strategy)
    {
        $strategy->setParser($this);
        $strategy->setLexer($this->lexer);
        $this->strategies[] = $strategy;
    }

    /**
     * return abstract syntax tree
     *
     * @return AST\OperationInterface|null
     */
    public function getAST()
    {
        return $this->resourceQuery();
    }

    /**
     * @return AST\Operation
     */
    public function resourceQuery()
    {
        $this->lexer->moveNext();
        $type = $this->lexer->lookahead['type'];

        if (is_null($type)) {
            return;
        }
        foreach ($this->strategies as $strategy) {
            if ($strategy->accepts($type)) {
                $operation = $strategy->parse();
                return $operation;
            }
        }
        throw new \RuntimeException(sprintf("No strategies matched the type %s. Did you load all strategies?", $type));
    }
}
