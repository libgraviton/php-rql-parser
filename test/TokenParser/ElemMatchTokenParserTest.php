<?php
/**
 * ElemMatchTokenParserTest class file
 */

namespace Graviton\Rql\TokenParser;

use Graviton\Rql\Node\ElemMatchNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Token;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    http://swisscom.ch
 */
class ElemMatchTokenParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test ElemMatchTokenParser::supports()

     * @return void
     */
    public function testSupports()
    {
        $expectedResult = __LINE__;

        $queryTokenParser = $this
            ->getMockBuilder('Xiag\Rql\Parser\TokenParserInterface')
            ->getMock();

        $tokenStream = $this
            ->getMockBuilder('Xiag\Rql\Parser\TokenStream')
            ->disableOriginalConstructor()
            ->getMock();
        $tokenStream
            ->expects($this->once())
            ->method('test')
            ->with(Token::T_OPERATOR, 'elemMatch')
            ->willReturn($expectedResult);

        $tokenParser = new ElemMatchTokenParser($queryTokenParser);
        $this->assertSame($expectedResult, $tokenParser->supports($tokenStream));
    }

    /**
     * Test ElemMatchTokenParser::parse()

     * @return void
     */
    public function testParse()
    {
        $field = 'field';
        $query = new EqNode('subfield', 'subvalue');
        $expectedNode = new ElemMatchNode($field, $query);

        $tokenStream = $this
            ->getMockBuilder('Xiag\Rql\Parser\TokenStream')
            ->disableOriginalConstructor()
            ->getMock();
        $tokenStream
            ->expects($this->at(0))
            ->method('expect')
            ->with(Token::T_OPERATOR, 'elemMatch');
        $tokenStream
            ->expects($this->at(1))
            ->method('expect')
            ->with(Token::T_OPEN_PARENTHESIS);
        $tokenStream
            ->expects($this->at(2))
            ->method('expect')
            ->with(Token::T_STRING)
            ->willReturn(new Token(Token::T_STRING, $field, 0));
        $tokenStream
            ->expects($this->at(3))
            ->method('expect')
            ->with(Token::T_COMMA);
        $tokenStream
            ->expects($this->at(4))
            ->method('expect')
            ->with(Token::T_END, 'parse subquery');
        $tokenStream
            ->expects($this->at(5))
            ->method('expect')
            ->with(Token::T_CLOSE_PARENTHESIS);

        $queryTokenParser = $this
            ->getMockBuilder('Xiag\Rql\Parser\TokenParserInterface')
            ->getMock();
        $queryTokenParser
            ->expects($this->once())
            ->method('parse')
            ->with($tokenStream)
            ->willReturnCallback(
                function () use ($tokenStream, $query) {
                    $tokenStream->expect(Token::T_END, 'parse subquery');
                    return $query;
                }
            );

        $tokenParser = new ElemMatchTokenParser($queryTokenParser);
        $this->assertEquals($expectedNode, $tokenParser->parse($tokenStream));
    }
}
