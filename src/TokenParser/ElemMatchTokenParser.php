<?php
/**
 * ElemMatchTokenParser class file
 */

namespace Graviton\Rql\TokenParser;

use Graviton\Rql\Node\ElemMatchNode;
use Xiag\Rql\Parser\AbstractNode;
use Xiag\Rql\Parser\Exception\SyntaxErrorException;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenParserInterface;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\AbstractTokenParser;

/**
 * elemMatch() token parser
 *
 * @author  List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
                    'Xiag\Rql\Parser\Node\AbstractQueryNode',
                    get_class($query)
                )
            );
        }

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new ElemMatchNode($field, $query);
    }
}
