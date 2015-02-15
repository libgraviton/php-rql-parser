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

    public function __construct($rql)
    {
        $this->lexer = new Lexer;
        $this->lexer->setInput($rql);
    }

    public function getAST()
    {
        $AST = $this->resourceQuery();

        return $AST;
    }

    public function resourceQuery()
    {
        $this->lexer->moveNext();

        switch($this->lexer->lookahead['type']) {
            case Lexer::T_EQ:
                $operation = $this->propertyOperation('eq');
                break;
            case Lexer::T_NE:
                $operation = $this->propertyOperation('ne');
                break;
            case Lexer::T_AND:
                $operation = $this->queryOperation('and');
                break;
            case Lexer::T_OR:
                $operation = $this->queryOperation('or');
                break;
            case Lexer::T_LT:
                $operation = $this->propertyOperation('lt');
                break;
            case Lexer::T_LTE:
                $operation = $this->propertyOperation('lte');
                break;
            case Lexer::T_GT:
                $operation = $this->propertyOperation('gt');
                break;
            case Lexer::T_GTE:
                $operation = $this->propertyOperation('gte');
                break;
            case Lexer::T_SORT:
                $operation = $this->sortOperation();
                break;
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
                $this->lexer->moveNext();
                switch ($this->lexer->lookahead['type']) {
                    case Lexer::T_MINUS:
                        $this->lexer->moveNext();
                        $type = 'desc';
                        break;
                    case Lexer::T_PLUS:
                        $this->lexer->moveNext();
                    default:
                        $type = 'asc';
                        break;
                }
                if ($this->lexer->lookahead == NULL) {
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
        if ($this->lexer->lookahead['type'] == Lexer::T_STRING) {
            $string = $this->lexer->lookahead['value'];
        } else if ($this->lexer->lookahead['type'] == Lexer::T_INTEGER) {
            $string = (int) $this->lexer->lookahead['value'];
        } else {
            $this->syntaxError('no valid argument found');
        }
        return $string;
    }

    protected function getString()
    {
        $this->lexer->moveNext();
        if ($this->lexer->lookahead['type'] == Lexer::T_STRING) {
            $string = $this->lexer->lookahead['value'];
        } else {
            $this->syntaxError('no string found');
        }
        return $string;
    }

    protected function syntaxError($message) {
        throw new \LogicException($message);
    }
}

