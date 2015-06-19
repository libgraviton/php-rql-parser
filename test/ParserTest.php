<?php
/**
 * verify that parser build a correct AST
 */

namespace Graviton\Rql;

use Graviton\Rql\Parser;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return void
     */
    public function doubleData()
    {
        $rql = 'eq(a,string:1';

        $astDouble = $this
            ->getMock('Xiag\Rql\Parser\Query');

        $tokenDouble = $this
            ->getMockBuilder('Xiag\Rql\Parser\TokenStream')
            ->disableOriginalConstructor()
            ->getMock();

        $lexerDouble = $this
            ->getMock('Xiag\Rql\Parser\Lexer');

        $lexerDouble->expects($this->once())
            ->method('tokenize')
            ->with($rql)
            ->willReturn($tokenDouble);

        $parserDouble = $this
            ->getMockBuilder('Xiag\Rql\Parser\Parser')
            ->disableOriginalConstructor()
            ->getMock();
        $parserDouble
            ->expects($this->once())
            ->method('parse')
            ->with($tokenDouble)
            ->willReturn($astDouble);

        $visitorDouble = $this
            ->getMock('Graviton\Rql\Visitor\VisitorInterface');

        return [
            [
                $rql,
                $lexerDouble,
                $parserDouble,
                $visitorDouble,
                $astDouble
            ]
        ];
    }

    /**
     * gets an AST from parser
     *
     * @dataProvider doubleData
     *
     * @return void
     */
    public function testParse($rql, $lexerDouble, $parserDouble, $visitorDouble)
    {
        $sut = new Parser($lexerDouble, $parserDouble, $visitorDouble);
        $sut->parse($rql);
    }

    /**
     * Run AST through query builder
     *
     * @dataProvider doubleData
     *
     * @return void
     */
    public function testBuildQuery($rql, $lexerDouble, $parserDouble, $visitorDouble, $astDouble)
    {
        $builderDouble = new \StdClass;
        $visitorDouble
            ->expects($this->once())
            ->method('visit')
            ->with($astDouble);

        $sut = new Parser($lexerDouble, $parserDouble, $visitorDouble);
        $sut->parse($rql);

        $sut->buildQuery($builderDouble);
    }
}
