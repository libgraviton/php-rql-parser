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
        }

        if ($this->lexer->lookahead === null) {
            $this->syntaxError('end of string');
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
        $operation->value = $this->getString();
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

