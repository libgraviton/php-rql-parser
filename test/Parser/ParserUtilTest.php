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
}
