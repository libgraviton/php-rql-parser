<?php
/**
 * ElemMatchTokenParser class file
 */

namespace Graviton\Rql\TokenParser;

use Graviton\Rql\Node\ElemMatchNode;
use Graviton\RqlParser\AbstractNode;
use Graviton\RqlParser\Exception\SyntaxErrorException;
use Graviton\RqlParser\Node\AbstractQueryNode;
use Graviton\RqlParser\Token;
use Graviton\RqlParser\TokenParserInterface;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\AbstractTokenParser;

/**
 * elemMatch() token parser
 *
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    http://swisscom.ch
 */
class ElemMatchTokenParser extends AbstractTokenParser
{
    /**
     * @var TokenParserInterface
     */
    private $queryTokenParser;

    /**
     * Constructor
     *
     * @param TokenParserInterface $queryTokenParser Query parser
     */
    public function __construct(TokenParserInterface $queryTokenParser)
    {
        $this->queryTokenParser = $queryTokenParser;
    }

    /**
     * Is current token supported by this parser
     *
     * @param TokenStream $tokenStream Token stream
     * @return bool
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, 'elemMatch');
    }

    /**
     * Parse
     *
     * @param TokenStream $tokenStream Token stream
     * @return AbstractNode
     */
    public function parse(TokenStream $tokenStream)
    {
        $tokenStream->expect(Token::T_OPERATOR, 'elemMatch');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $field = $tokenStream->expect(Token::T_STRING)->getValue();
        $tokenStream->expect(Token::T_COMMA);

        $query = $this->queryTokenParser->parse($tokenStream);
        if (!$query instanceof AbstractQueryNode) {
            throw new SyntaxErrorException(
                sprintf(
                    '"elemMatch" operator expects parameter "query" to be instance of "%s", "%s" given',
                    'Graviton\RqlParser\Node\AbstractQueryNode',
                    get_class($query)
                )
            );
        }

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new ElemMatchNode($field, $query);
    }
}
