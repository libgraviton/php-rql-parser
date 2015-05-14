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
     * @param string $expectedString String expected to be returned by the sut
     * @param string $rqlAttribs     String representing the query parameters of a rql-string
     *
     * @return void
     */
    public function testGetString($expectedString, $rqlAttribs)
    {
        $lexer = new Lexer();
        $lexer->setInput($rqlAttribs);

        ParserUtil::parseStart($lexer);

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
        $typeNotString = '([],foo)';

        $lexer = new Lexer();
        $lexer->setInput($typeNotString);

        ParserUtil::parseStart($lexer);

        $this->setExpectedException('\LogicException');

        ParserUtil::getString($lexer);
    }

    /**
     * Verify identification of the closing parenthesis
     *
     * @return void
     */
    public function testParseEnd()
    {
        $lexer = new Lexer();
        $lexer->setInput('(jon,doe)');
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
        $lexer->setInput('(jon,doe)');

        $this->setExpectedException('\LogicException');

        ParserUtil::parseEnd($lexer);
    }

    /**
     * Validates the behavior of parseArgument
     *
     * @dataProvider rqlStringProvider
     *
     * @return void
     */
    public function testParseArgument($expected, $rql)
    {
        $lexer = new Lexer();
        $lexer->setInput($rql);

        ParserUtil::parseStart($lexer);

        $this->assertEquals($expected, ParserUtil::parseArgument($lexer));
    }

    /**
     * @return array
     */
    public function rqlStringProvider()
    {
        return array(
            'string' => array('jon', '(jon, doe)'),
            'numeric' => array('12', '(12, doe)'),
            'quotation' => array('"12"', '("12", doe)'),
            'boolean (true)' => array(true, '(true, doe)'),
            'boolean (false)' => array(false, '(false, doe)'),
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

        $this->setExpectedException('\LogicException');

        ParserUtil::parseArgument($lexer);
    }
}
