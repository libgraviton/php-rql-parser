<?php

namespace Graviton\Rql;

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
     * @var string<int>
     */
    private $propertyOperations = array(
        Lexer::T_EQ => 'eq',
        Lexer::T_NE => 'ne',
        Lexer::T_LT => 'lt',
        Lexer::T_LTE => 'lte',
        Lexer::T_GT => 'gt',
        Lexer::T_GTE => 'gte',
    );
    /**
     * @var string<int>
     */
    private $queryOperations = array(
        Lexer::T_AND => 'and',
        Lexer::T_OR  => 'or',
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

    /**
     * return abstract syntax tree
     *
     * @return AST\Operation
     */
    public function getAST()
    {
        $AST = $this->resourceQuery();

        return $AST;
    }

    /**
     * @return AST\Operation
     */
    public function resourceQuery()
    {
        $this->lexer->moveNext();
        $type = $this->lexer->lookahead['type'];

        if (in_array($type, array_keys($this->propertyOperations))) {
            $operation = $this->propertyOperation($this->propertyOperations[$type]);

        } elseif (in_array($type, array_keys($this->queryOperations))) {
            $operation = $this->queryOperation($this->queryOperations[$type]);

        } elseif ($type == Lexer::T_SORT) {
            $operation = $this->sortOperation();

        } else {
            throw new \LogicException(sprintf('unknown operation %s', $type));
        }

        return $operation;
    }

    protected function propertyOperation($name)
    {
        $operation = $this->operation($name);
        $operation->property = $this->getString();
        $this->lexer->moveNext();
        if ($this->lexer->lookahead['type'] != Lexer::T_COMMA) {
            $this->syntaxError('missing comma');
        }
        $operation->value = $this->getArgument();
        $this->closeOperation();
        return $operation;
    }

    protected function queryOperation($name)
    {
        $operation = $this->operation($name);
        $operation->queries = array();
        $operation->queries[] = $this->resourceQuery();
        $this->lexer->moveNext();
        $hasQueries = $this->lexer->lookahead['type'] == Lexer::T_COMMA;
        while ($hasQueries) {
            $operation->queries[] = $this->resourceQuery();

            $this->lexer->moveNext();
            $hasQueries = $this->lexer->lookahead['type'] == Lexer::T_COMMA;
        }
        return $operation;
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
            } elseif ($this->lexer->lookahead['type'] != Lexer::T_CLOSE_PARENTHESIS) {
                $sortDone = true;
            }
            if (!$sortDone) {
                $operation->fields[] = array($property, $type);
            }
        }

        return $operation;
    }

    protected function operation($name)
    {
        $this->lexer->moveNext();
        if ($this->lexer->lookahead['type'] != Lexer::T_OPEN_PARENTHESIS) {
            $this->syntaxError('missing open parenthesis');
        }
        $operation = new AST\Operation($name);
        return $operation;
    }

    protected function closeOperation()
    {
        $this->lexer->moveNext();
        if ($this->lexer->lookahead['type'] != Lexer::T_CLOSE_PARENTHESIS) {
            $this->syntaxError('missing close parenthesis');
        }
    }

    protected function getArgument()
    {
        $this->lexer->moveNext();
        $string = null;
        if ($this->lexer->lookahead['type'] == Lexer::T_STRING) {
            $string = $this->lexer->lookahead['value'];
        } elseif ($this->lexer->lookahead['type'] == Lexer::T_INTEGER) {
            $string = (int) $this->lexer->lookahead['value'];
        } else {
            $this->syntaxError('no valid argument found');
        }
        return $string;
    }

    protected function getString()
    {
        $this->lexer->moveNext();
        $string = null;
        if ($this->lexer->lookahead['type'] == Lexer::T_STRING) {
            $string = $this->lexer->lookahead['value'];
        } else {
            $this->syntaxError('no string found');
        }
        return $string;
    }

    /**
     * @param string $message
     */
    protected function syntaxError($message)
    {
        throw new \LogicException($message);
    }
}
