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

        $this->setExpectedException('\Graviton\Rql\Exceptions\SyntaxErrorException');

        ParserUtil::getString($lexer);
    }
}
