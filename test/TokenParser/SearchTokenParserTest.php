<?php
/**
 * SearchTokenParserTest class file
 */

namespace Graviton\Rql\TokenParser;

use Graviton\Rql\Node\SearchNode;
use Graviton\Rql\Parser as GravitonParser;
use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;

/**
 * @author  List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class SearchTokenParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test SearchTokenParser::parse()

     * @return void
     */
    public function testMultiParse()
    {
        $terms = array("testSearchTerm1", "testSearchTerm2");

        $expectedNode = new SearchNode($terms);

        $rql = "search(testSearchTerm1%20testSearchTerm2)";
        $result = GravitonParser::createDefault()->parse((new Lexer())->tokenize($rql));

        $this->assertTrue($result->getQuery() instanceof SearchNode);

        /** @var SearchNode $searchNode */
        $searchNode = $result->getQuery();

        $this->assertEquals($expectedNode->getSearchTerms(), $searchNode->getSearchTerms());
    }

    /**
     * Test SearchTokenParser::parse()

     * @return void
     */
    public function testSingleParse()
    {
        $terms = array("testSearchTerm1");

        $expectedNode = new SearchNode($terms);

        $rql = "search(testSearchTerm1)";
        $result = GravitonParser::createDefault()->parse((new Lexer())->tokenize($rql));

        $this->assertTrue($result->getQuery() instanceof SearchNode);

        /** @var SearchNode $searchNode */
        $searchNode = $result->getQuery();

        $this->assertEquals($expectedNode->getSearchTerms(), $searchNode->getSearchTerms());
    }

}