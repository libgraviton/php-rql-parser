<?php
namespace Graviton\Rql\NodeParser;

use Graviton\Rql\Node\SearchNode;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\NodeParserInterface;
use Xiag\Rql\Parser\SubParserInterface;

class SearchNodeParser implements NodeParserInterface
{
    /**
     * @var SubParserInterface
     */
    protected $fieldNameParser;

    /**
     * @param SubParserInterface $fieldNameParser
     */
    public function __construct(SubParserInterface $fieldNameParser)
    {
        $this->fieldNameParser = $fieldNameParser;
    }

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $fields = [];

        $tokenStream->expect(Token::T_OPERATOR, 'search');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $searchTerm = $this->fieldNameParser->parse($tokenStream);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new SearchNode(explode(' ', $searchTerm));
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, 'search');
    }
}

