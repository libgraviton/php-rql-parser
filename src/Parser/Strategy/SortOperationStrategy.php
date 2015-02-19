<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Parser\ParserUtil;
use Graviton\Rql\AST\OperationFactory;
use Graviton\Rql\Lexer;

class SortOperationStrategy extends ParsingStrategy
{
    /**
     * @return OperationInterface
     */
    public function parse()
    {
        $operation = OperationFactory::fromLexerToken($this->lexer->lookahead['type']);
        $this->lexer->moveNext();

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
                ParserUtil::syntaxError('property name expected in sort');
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

    public function getAcceptedTypes()
    {
        return array(
            Lexer::T_SORT,
        );
    }
}
