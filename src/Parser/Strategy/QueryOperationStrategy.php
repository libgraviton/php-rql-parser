<?php

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Parser\ParserUtil;
use Graviton\Rql\AST\OperationFactory;
use Graviton\Rql\AST\QueryOperationInterface;
use Graviton\Rql\Lexer;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class QueryOperationStrategy extends ParsingStrategy
{
    /**
     * @return QueryOperationInterface
     */
    public function parse()
    {
        $operation = OperationFactory::fromLexerToken($this->lexer->lookahead['type']);

        if (!$operation instanceof QueryOperationInterface) {
            throw new \RuntimeException;
        }

        ParserUtil::parseStart($this->lexer);
        $operation->addQuery($this->parser->resourceQuery());

        $hasQueries = true;
        while ($hasQueries) {
            ParserUtil::parseComma($this->lexer, true);
            $query = $this->parser->resourceQuery();
            if ($query) {
                $operation->addQuery($query);
            }
            $glimpse = $this->lexer->glimpse();
            $hasQueries = $glimpse['type'] == Lexer::T_COMMA;
            if (!$hasQueries) {
                ParserUtil::parseEnd($this->lexer);
            }
        }

        return $operation;
    }

    public function getAcceptedTypes()
    {
        return array(
            Lexer::T_AND,
            Lexer::T_OR,
        );
    }
}
