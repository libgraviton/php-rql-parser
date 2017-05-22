<?php

/**
 * Search token parser for rql
 */

namespace Graviton\Rql\TokenParser;

use Graviton\Rql\Node\SearchNode;
use Xiag\Rql\Parser\AbstractTokenParser;
use Xiag\Rql\Parser\Exception\UnknownTokenException;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;

/**
 * Class SearchTokenParser
 * @package Graviton\Rql\TokenParser
 *
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class SearchTokenParser extends AbstractTokenParser
{
    /**
     * @param TokenStream $tokenStream token stream
     * @return SearchNode
     */
    public function parse(TokenStream $tokenStream)
    {
        $tokenStream->expect(Token::T_OPERATOR, 'search');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);
        $searchTerm = $this->getParser()->getExpressionParser()->parseScalar($tokenStream);

        // Only Strings or Integers
        if (is_int($searchTerm) || is_float($searchTerm)) {
            $searchTerm = (string) $searchTerm;
        } elseif (!is_string($searchTerm)) {
            throw new UnknownTokenException('RQL Search only allows strings');
        }

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        $searchNode = SearchNode::getInstance();
        foreach (explode(" ", $searchTerm) as $string) {
            $searchNode->addSearchTerm($string);
        }
        return $searchNode;
    }

    /**
     * @param TokenStream $tokenStream token stream
     *
     * @return bool
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, 'search');
    }
}
