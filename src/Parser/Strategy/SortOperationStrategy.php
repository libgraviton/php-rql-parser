<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Parser\ParserUtil;
use Graviton\Rql\AST\OperationFactory;
use Graviton\Rql\AST\SortOperationInterface;
use Graviton\Rql\Lexer;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
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
            $this->lexer->moveNext();
            $type = $this->getTypeAndMove($this->lexer->lookahead['type']);

            if ($this->lexer->lookahead == null || $this->lexer->lookahead['type'] == Lexer::T_CLOSE_PARENTHESIS) {
                $sortDone = true;
            } elseif ($this->lexer->lookahead['type'] == Lexer::T_STRING) {
                $operation->addField($this->getField($type));
            }
            ParserUtil::parseComma($this->lexer, true);
        }

        return $operation;
    }

    public function getAcceptedTypes()
    {
        return array(
            Lexer::T_SORT,
        );
    }

    private function getTypeAndMove($token)
    {
        switch ($token) {
            case Lexer::T_MINUS:
                $this->lexer->moveNext();
                $type = 'desc';
                break;
            case Lexer::T_PLUS:
                $this->lexer->moveNext();
                $type = 'asc';
                break;
            default:
                // don't touch type in default case and leave it up to the visitor to set defaults
                $type = null;
        }
        return $type;
    }

    private function getField($type)
    {
        $property = ParserUtil::getString($this->lexer, false);

        $field = array($property);
        if (!empty($type)) {
            $field[] = $type;
        }
        return $field;
    }
}
