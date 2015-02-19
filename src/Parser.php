<?php

namespace Graviton\Rql;

use Graviton\Rql\Parser\Strategy\ParsingStrategyInterface;
use Graviton\Rql\Parser\ParserUtil;

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
        return $this->resourceQuery();
    }

    /**
     * @return AST\Operation
     */
    public function resourceQuery()
    {
        $this->lexer->moveNext();
        $type = $this->lexer->lookahead['type'];

        foreach ($this->strategies as $strategy) {
            if ($strategy->accepts($type)) {
                $operation = $strategy->parse();
                return $operation;
            }
        }
    }

    protected function sortOperation()
    {
        $operation = $this->operation('sort');
        $operation->fields = array();
        $sortDone = false;
        while (!$sortDone) {
            $property = null;
            $this->lexer->moveNext();
            switch ($this->lexer->lookahead['type']) {
                case Lexer::T_MINUS:
                    $this->lexer->moveNext();
                    $type = 'desc';
                    break;
                case Lexer::T_PLUS:
                    $this->lexer->moveNext();
                    // + is same as default
                default:
                    $type = 'asc';
                    break;
            }

            if ($this->lexer->lookahead == null) {
                $sortDone = true;
            } elseif ($this->lexer->lookahead['type'] != Lexer::T_STRING) {
                $this->syntaxError('property name expected in sort');
            } else {
                $property = $this->lexer->lookahead['value'];
                $this->lexer->moveNext();
            }

            if ($this->lexer->lookahead['type'] != Lexer::T_COMMA) {
                $this->lexer->moveNext();
            }
            if (!$sortDone) {
                $operation->fields[] = array($property, $type);
            }
        }

        return $operation;
    }

    protected function limitOperation()
    {
        $operation = $this->operation('limit');
        $operation->fields = array();
        $limitDone = false;
        while (!$limitDone) {
            if ($this->lexer->lookahead == null) {
                $limitDone = true;
            } elseif ($this->lexer->lookahead['type'] == Lexer::T_INTEGER) {
                $operation->fields[] = $this->lexer->lookahead['value'];
                $this->lexer->moveNext();
            } else {
                $this->lexer->moveNext();
            }
        }
        return $operation;
    }
}
