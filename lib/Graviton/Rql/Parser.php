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
                $operation = $this->eqOperation();
                break;
            case Lexer::T_NE:
                $operation = $this->neOperation();
                break;
        }

        if ($this->lexer->lookahead === null) {
            $this->syntaxError('end of string');
        }
        
        return $operation;
    }

    protected function eqOperation()
    {
            $operation = $this->operation('eq');
            $this->lexer->moveNext();
            if ($this->lexer->lookahead['type'] != Lexer::T_STRING) {
                $this->syntaxError('missing value for eq');
            }
            $operation->value = $this->lexer->lookahead['value'];
            return $operation;
    }

    protected function neOperation()
    {
        return $this->operation('ne');
    }

    protected function operation($name)
    {
        $this->lexer->moveNext();
        if ($this->lexer->lookahead['type'] == Lexer::T_OPEN_PARENTHESIS) {
            $this->lexer->moveNext();
            if ($this->lexer->lookahead['type'] == Lexer::T_STRING) {
                $property = $this->lexer->lookahead['value'];
            } else {
                $this->syntaxError('no property specified');
            }
            $this->lexer->moveNext();
            if ($this->lexer->lookahead['type'] != Lexer::T_COMMA) {
                $this->syntaxError('missing comma');
            }


        } else {
            $this->syntaxError('missing open parenthesis');
        }
        $operation = new AST\Operation($name, $property);
        return $operation;
    }
}

