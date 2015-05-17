<?php
/**
 * verify parser utilities
 */

namespace Graviton\Rql\Parser;

use Graviton\Rql\Lexer;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class ParserUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Verify identification of query attributes
     *
     * @dataProvider stringProvider
     *
     * @param string $expectedString expected to be returned by the sut
     * @param string $rqlAttributes  representing the query parameters of a rql-string
     *
     * @return void
     */
    public function testGetString($expectedString, $rqlAttributes)
    {
        $lexer = $this->getStartedLexer($rqlAttributes);

        $this->assertEquals($expectedString, ParserUtil::getString($lexer));
    }

    /**
     * @return array
     */
    public function stringProvider()
    {
        return array(
            'multi concat string' => array('name-part+test', '(name-part+test,foo)'),
            'simple string' => array('name', '(name,foo)'),
            'single concat string' => array('metadata.mime', '(metadata.mime,foo)'),
        );
    }

    /**
     * Verify exception handling of getString
     *
     * @return void
     */
    public function testGetStringLogicExpectingException()
    {
        $lexer = $this->getStartedLexer('([],foo)');

        $this->setExpectedException('\Graviton\Rql\Exceptions\SyntaxErrorException');

        ParserUtil::getString($lexer);
    }

    /**
     * get started parser/lexer
     *
     * @param string $rql rql to seed lexer with
     *
     * @return Lexer
     */
    private function getStartedLexer($rql)
    {
        $lexer = new Lexer();
        $lexer->setInput($rql);
        ParserUtil::parseStart($lexer);

        return $lexer;
    }

    /**
     * Verify identification of the closing parenthesis
     *
     * @return void
     */
    public function testParseEnd()
    {
        $lexer = new Lexer();
        $lexer->setInput('eq(jon,doe)');
        $lexer->moveNext();
        $lexer->moveNext();
        $lexer->moveNext();
        $lexer->moveNext();
        $lexer->moveNext();

        ParserUtil::parseEnd($lexer);
    }

    /**
     * Verify exception handling of parseEnd
     *
     * @return void
     */
    public function testParseEndExpectingException()
    {
        $lexer = new Lexer();
        $lexer->setInput('eq(jon,doe)');

        $this->setExpectedException('\Graviton\Rql\Exceptions\SyntaxErrorException');

        ParserUtil::parseEnd($lexer);
    }

    /**
     * Validates the behavior of parseArgument
     *
     * @dataProvider rqlStringProvider
     *
     * @param string $expected Expected outcome
     * @param string $rql      RQL-Query string
     *
     * @return void
     */
    public function testParseArgument($expected, $rql)
    {
        $lexer = new Lexer();
        $lexer->setInput($rql);

        $this->assertEquals($expected, ParserUtil::parseArgument($lexer));
    }

    /**
     * @return array
     */
    public function rqlStringProvider()
    {
        return array(
            'string' => array('jon', 'jon'),
            'numeric' => array('12', '12'),
            'quotation' => array('"12"', '"12"'),
            'boolean (true)' => array(true, 'true'),
            'boolean (false)' => array(false, 'false'),
            'multiple, encapsulated quotation' => array("\"Hans 'Housi' Wale-Sepp\"", "\"Hans 'Housi' Wale-Sepp\""),
            'apostrophe quotation' => array("it's a cake!!", "it's a cake!!"),
            'multiple quotation with apostrophe' =>
                array("it's a \"cake\" \"blaster\"!!", "it's a \"cake\" \"blaster\"!!"),
        );
    }

    /**
     * Validates the exception handling of parseArgument
     *
     * @return void
     */
    public function testParseArgumentExpectingException()
    {
        $lexer = new Lexer();
        $lexer->setInput('()');

        $this->setExpectedException('\Graviton\Rql\Exceptions\SyntaxErrorException');

        ParserUtil::parseArgument($lexer);
    }
}
