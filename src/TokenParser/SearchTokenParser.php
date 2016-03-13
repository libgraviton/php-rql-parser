<?php

namespace Graviton\Rql\TokenParser;


use Graviton\Rql\Node\SearchNode;
use Xiag\Rql\Parser\AbstractTokenParser;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;

class SearchTokenParser extends AbstractTokenParser
{

    public function parse(TokenStream $tokenStream)
    {
        $searchTerms = [];

        $tokenStream->expect(Token::T_OPERATOR, 'search');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $searchTermsImploded = $this->getParser()->getExpressionParser()->parseScalar($tokenStream);
        $searchTerms = explode(" ", $searchTermsImploded);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new SearchNode($searchTerms);
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, 'search');
    }


}