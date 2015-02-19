<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Parser\ParserUtil;
use Graviton\Rql\AST\OperationFactory;
use Graviton\Rql\AST\SortOperationInterface;
use Graviton\Rql\Lexer;

class SortOperationStrategy extends ParsingStrategy
{
    /**
     * @return SortOperationInterface
     */
    public function parse()
    {
        $operation = OperationFactory::fromLexerToken($this->lexer->lookahead['type']);

        if (!$operation instanceof SortOperationInterface) {
            throw new \RuntimeException;
        }

        ParserUtil::parseStart($this->lexer);

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

            if ($this->lexer->lookahead == null || $this->lexer->lookahead['type'] == Lexer::T_CLOSE_PARENTHESIS) {
                $sortDone = true;
            } elseif ($this->lexer->lookahead['type'] == Lexer::T_STRING) {
                $property = ParserUtil::getString($this->lexer, false);
            }
            ParserUtil::parseComma($this->lexer, true);

            if (!$sortDone) {
                $operation->addField(array($property, $type));
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
