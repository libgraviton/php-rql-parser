<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\AST\OperationFactory;
use Graviton\Rql\AST\LimitOperationInterface;
use Graviton\Rql\Lexer;

class LimitOperationStrategy extends ParsingStrategy
{
    /**
     * @return LimitOperationInterface
     */
    public function parse()
    {
        $operation = OperationFactory::fromLexerToken($this->lexer->lookahead['type']);

        if (!$operation instanceof LimitOperationInterface) {
            throw new \RuntimeException;
        }

        $fields = array();
        $limitDone = false;
        while (!$limitDone) {
            if ($this->lexer->lookahead == null) {
                $limitDone = true;
            } elseif ($this->lexer->lookahead['type'] == Lexer::T_INTEGER) {
                $fields[] = $this->lexer->lookahead['value'];
                $this->lexer->moveNext();
            } else {
                $this->lexer->moveNext();
            }
        }
        $operation->setLimit($fields[0]);
        if (!empty($fields[1])) {
            $operation->setSkip($fields[1]);
        }
        return $operation;
    }

    public function getAcceptedTypes()
    {
        return array(
            Lexer::T_LIMIT,
        );
    }
}
