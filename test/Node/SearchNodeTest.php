<?php

/**
 * SearchNodeTest class file
 */

namespace Graviton\Rql\Node;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    http://swisscom.ch
 */
class SearchNodeTest extends \PHPUnit\Framework\TestCase
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
