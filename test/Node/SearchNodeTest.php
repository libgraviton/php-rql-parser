<?php

/**
 * SearchNodeTest class file
 */

namespace Graviton\Rql\Node;

/**
 * @author  List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class SearchNodeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test SearchNode::getNodeName()
     *
     * @return void
     */
    public function testNodeName()
    {
        $sut = new SearchNode();
        $this->assertEquals('search', $sut->getNodeName());
    }

    /**
     * Test SearchNode::addSearchTerm()
     *
     * @return void
     */
    public function testAddTerm()
    {
        $expectedResult = array('searchTermTest1', 'searchTermTest2');

        $sut = new SearchNode();
        $sut->addSearchTerm('searchTermTest1');
        $sut->addSearchTerm('searchTermTest2');

        $this->assertEquals($expectedResult, array_values($sut->getSearchTerms()));
    }

}