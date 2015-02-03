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

    /**
     * test ne cases
     */
    public function testNeMatches()
    {
        $sut = new \Graviton\Rql\Query('ne(name,foo)|ne(name,bar)');

        $mock = $this->getMock('\Graviton\Rql\QueryInterface');

        $mock->expects($this->once())
             ->method('andNe')
             ->with($this->equalTo('name'), $this->equalTo('foo'));
        $mock->expects($this->once())
             ->method('orNe')
             ->with($this->equalTo('name'), $this->equalTo('bar'));

        $sut->applyToQueriable($mock);
    }

    /**
     * test lt and gt case
     */
    public function testLtAndGtMatches()
    {
        $sut = new \Graviton\Rql\Query('lt(count,100)&gt(count,10)');

        $mock = $this->getMock('\Graviton\Rql\QueryInterface');

        $mock->expects($this->once())
             ->method('andLt')
             ->with($this->equalTo('count'), $this->equalTo(100));
        $mock->expects($this->once())
             ->method('andGt')
             ->with($this->equalTo('count'), $this->equalTo(10));

        $sut->applyToQueriable($mock);
    }

    /**
     * test lt or gt case
     */
    public function testLtOrGtMatches()
    {
        $sut = new \Graviton\Rql\Query('lt(count,10)|gt(count,0)');

        $mock = $this->getMock('\Graviton\Rql\QueryInterface');

        $mock->expects($this->once())
             ->method('andLt')
             ->with($this->equalTo('count'), $this->equalTo(10));
        $mock->expects($this->once())
             ->method('orGt')
             ->with($this->equalTo('count'), $this->equalTo(0));

        $sut->applyToQueriable($mock);
    }

    /**
     * test blocks
     */
    public function testBlockMatches()
    {
        $sut = new \Graviton\Rql\Query('(ge(count,0)&le(count,10)&eq(name,foo))');

        $mock = $this->getMock('\Graviton\Rql\QueryInterface');

        $mock->expects($this->once())
             ->method('andGe')
             ->with($this->equalTo('count'), $this->equalTo(0));
        $mock->expects($this->once())
             ->method('andLe')
             ->with($this->equalTo('count'), $this->equalTo(10));
        $mock->expects($this->once())
             ->method('andEq')
             ->with($this->equalTo('name'), $this->equalTo('foo'));

        $sut->applyToQueriable($mock);
    }

}
