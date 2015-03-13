<?php

namespace Graviton\Rql;

use Graviton\Rql\Parser\Strategy;
use Graviton\Rql\Parser\Strategy\ParsingStrategyInterface;
use Graviton\Rql\AST\OperationInterface;

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
     * @var ParsingStrategyInterface[]
     */
    private $strategies = array();

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
     * @return AST\OperationInterface
     */
    public function getAST()
    {
        return $this->resourceQuery(true);
    }

    /**
     * @return AST\OperationInterface
     */
    public function resourceQuery($first = false)
    {
        $operation = false;
        $this->lexer->moveNext();
        $type = $this->lexer->lookahead['type'];

        foreach ($this->strategies as $strategy) {
            if ($strategy->accepts($type)) {
                $operation = $strategy->parse();
                $glimpse = $this->lexer->glimpse();
                if ($first && $glimpse['type'] == Lexer::T_COMMA) {
                    $this->lexer->moveNext();
                    $operation = $this->wrapperOperation($operation);
                }
            }
        }
        if (!$operation) {
            throw new \RuntimeException(
                sprintf("No strategies matched the type %s. Did you load all strategies?", $type)
            );
        }
        return $operation;
    }

    /**
     * @return AST\OperationInterface
     */
    protected function wrapperOperation(OperationInterface $operation)
    {
        $wrapper = new AST\QueryOperation();
        $wrapper->addQuery($operation);
        $wrapper->addQuery($this->resourceQuery());
        return $wrapper;
    }
}
