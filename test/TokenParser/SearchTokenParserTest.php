<?php
/**
 * SearchTokenParserTest class file
 */

namespace Graviton\Rql\TokenParser;

use Graviton\Rql\Lexer;
use Graviton\Rql\Node\SearchNode;
use Graviton\Rql\Parser as GravitonParser;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\SortNode;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class SearchTokenParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup, clear search params
     * @return void
     */
    public function setup()
    {
        SearchNode::getInstance()->resetSearchTerms();
    }

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

    /**
     * Test SearchTokenParser::parse()
     * @return void
     */
    public function testStringDashParse()
    {
        $terms = array("test-Search-Term-1");

        $expectedNode = new SearchNode($terms);

        $rql = "search(string:test-Search-Term-1)";
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
    public function testStringSingleDashParse()
    {
        $terms = array("test-SearchTerm1");

        $expectedNode = new SearchNode($terms);

        $rql = "search(string:test-SearchTerm1)";
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
    public function testStringSingleNoDashParse()
    {
        $terms = array("test-SearchTerm1");

        $expectedNode = new SearchNode($terms);

        $rql = "search(string:test%2DSearchTerm1)";
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
    public function testDashReplaceMultipleParse()
    {
        $terms = array("test-Search-Term1", "test-SearchTerm2");

        $expectedSearchNodes = [];
        foreach ($terms as $item) {
            $expectedSearchNodes[] = new SearchNode($terms);
        }
        $expectedNode = new AndNode($expectedSearchNodes);

        $rql = "search(string:test-Search-Term1)&search(string:test%2DSearchTerm2)";
        $result = GravitonParser::createDefault()->parse((new Lexer())->tokenize($rql));

        $this->assertTrue($result->getQuery() instanceof AndNode);

        /** @var AndNode $andNodes */
        $andNodes = $result->getQuery();

        /** @var SearchNode $searchNode */
        foreach ($andNodes->getQueries() as $index => $searchNode) {
            $this->assertTrue($searchNode instanceof SearchNode);
        }

        $this->assertEquals($expectedNode, $andNodes);
    }



    /**
     * Test SearchTokenParser::parse()
     * Multiple searches should be only one visited
     * @return void
     */
    public function testDashReplaceMultipleAndSortParse()
    {
        $terms = ["test-Search-Term1", "test-SearchTerm2"];
        $sort  = ['createdDate' => -1];
        $rql   = "search(string:test-Search-Term1)&search(string:test%2DSearchTerm2)&sort(-createdDate)";

        $expectedSearchNodes = [];
        foreach ($terms as $item) {
            $expectedSearchNodes[] = new SearchNode($terms);
        }
        $expectedNode = new AndNode($expectedSearchNodes);
        $expectedSortNode = new SortNode($sort);

        $result = GravitonParser::createDefault()->parse((new Lexer())->tokenize($rql));

        $this->assertTrue($result->getQuery() instanceof AndNode);

        /** @var AndNode $andNodes */
        $andNodes = $result->getQuery();
        $sortNode = $result->getSort();

        /** @var SearchNode $searchNode */
        foreach ($andNodes->getQueries() as $index => $searchNode) {
            $this->assertTrue($searchNode instanceof SearchNode);
        }

        $this->assertEquals($expectedNode, $andNodes);
        $this->assertEquals($expectedSortNode, $sortNode);
    }
}
