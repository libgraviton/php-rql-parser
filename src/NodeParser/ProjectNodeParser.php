<?php
namespace Graviton\Rql\NodeParser;

use Graviton\Rql\Node\ProjectNode;
use Graviton\Rql\Node\SearchNode;
use Graviton\RqlParser\Token;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\NodeParserInterface;
use Graviton\RqlParser\SubParserInterface;
use Graviton\RqlParser\ValueParser\ArrayParser;

class ProjectNodeParser implements NodeParserInterface
{

    protected ArrayParser $arrayParser;

    public function __construct(ArrayParser $arrayParser)
    {
        $this->arrayParser = $arrayParser;
    }

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $fields = [];

        $tokenStream->expect(Token::T_OPERATOR, 'project');

        $fields = $this->arrayParser->parse($tokenStream);
        var_dump($fields);

        return new ProjectNode($fields);
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, 'project');
    }
}

