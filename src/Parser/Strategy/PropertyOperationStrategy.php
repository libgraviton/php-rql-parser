<?php
/**
 * parse the various property type operations (like eq, lt, ...)
 */

namespace Graviton\Rql\Parser\Strategy;

use Graviton\Rql\Parser\ParserUtil;
use Graviton\Rql\AST\OperationFactory;
use Graviton\Rql\AST\PropertyOperationInterface;
use Graviton\Rql\Lexer;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class PropertyOperationStrategy extends ParsingStrategy
{
    /**
     * @return PropertyOperationInterface
     */
    public function parse()
    {
        $operation = OperationFactory::fromLexerToken($this->lexer->lookahead['type']);

        if (!$operation instanceof PropertyOperationInterface) {
            throw new \RuntimeException;
        }

        ParserUtil::parseStart($this->lexer);
        $operation->setProperty(ParserUtil::getString($this->lexer));
        ParserUtil::parseComma($this->lexer);
        $operation->setValue(ParserUtil::parseArgument($this->lexer));
        ParserUtil::parseEnd($this->lexer);

        return $operation;
    }

    /**
     * @return array
     */
    public function getAcceptedTypes()
    {
        return array(
            Lexer::T_EQ,
            Lexer::T_NE,
            Lexer::T_LT,
            Lexer::T_GT,
            Lexer::T_LTE,
            Lexer::T_GTE,
            Lexer::T_LIKE
        );
    }
}
