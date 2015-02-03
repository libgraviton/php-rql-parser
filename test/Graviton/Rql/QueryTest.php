<?php

namespace Graviton\Rql;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test parsing of a basic eq() match
     */
    public function testEqMatch()
    {
        $sut = new \Graviton\Rql\Query('eq(name,foo)');

        $mock = $this->getMock('\Graviton\Rql\QueryInterface');

        $mock->expects($this->once())
             ->method('andEq')
             ->with($this->equalTo('name'), $this->equalTo('foo'));

        $sut->applyToQueriable($mock);
    }

    /**
     * test a basic eq()|eq() match
     */
    public function testOrEqMatch()
    {
        $sut = new \Graviton\Rql\Query('eq(name,foo)|eq(name,bar)');

        $mock = $this->getMock('\Graviton\Rql\QueryInterface');
        $mock->expects($this->once())
              ->method('andEq')
              ->with($this->equalTo('name'), $this->equalTo('foo'));
        $mock->expects($this->once())
              ->method('orEq')
              ->with($this->equalTo('name'), $this->equalTo('bar'));

        $sut->applyToQueriable($mock);
    }
}
