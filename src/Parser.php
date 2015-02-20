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

    public static function createParser($rql)
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
        return $this->resourceQuery(true);
    }

    /**
     * @return null|AST\OperationInterface
     */
    public function resourceQuery($first = false)
    {
        $this->lexer->moveNext();
        $type = $this->lexer->lookahead['type'];

        if (is_null($type)) {
            return;
        }
        foreach ($this->strategies as $strategy) {
            if ($strategy->accepts($type)) {
                $operation = $strategy->parse();
                $glimpse = $this->lexer->glimpse();
                if ($first && $glimpse['type'] == Lexer::T_COMMA) {
                    $this->lexer->moveNext();
                    $wrapper = new AST\QueryOperation();
                    $wrapper->addQuery($operation);
                    $query = $this->resourceQuery();
                    if ($query instanceof AST\OperationInterface) {
                        $wrapper->addQuery($query);
                    }
                    return $wrapper;
                }
                return $operation;
            }
        }
        throw new \RuntimeException(sprintf("No strategies matched the type %s. Did you load all strategies?", $type));
    }
}
