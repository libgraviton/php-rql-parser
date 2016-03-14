<?php

/**
 * Search token parser for rql
 */

namespace Graviton\Rql\TokenParser;

use Graviton\Rql\Node\SearchNode;
use Xiag\Rql\Parser\AbstractTokenParser;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;

/**
 * Class SearchTokenParser
 * @package Graviton\Rql\TokenParser
 *
 * @author  List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
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

        $searchTermsImploded = $this->getParser()->getExpressionParser()->parseScalar($tokenStream);
        $searchTerms = explode(" ", $searchTermsImploded);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new SearchNode($searchTerms);
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
