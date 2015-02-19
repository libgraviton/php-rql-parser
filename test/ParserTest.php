<?php

namespace Graviton\Rql;

use Graviton\Rql\AST;
use Graviton\Rql\Parser\Strategy;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test parser
     *
     * @dataProvider parserProvider
     */
    public function testParser($rql, $expected)
    {
        $sut = new \Graviton\Rql\Parser($rql);
        $sut->addStrategy(new Strategy\PropertyOperationStrategy);
        $sut->addStrategy(new Strategy\QueryOperationStrategy);
        $sut->addStrategy(new Strategy\ArrayOperationStrategy);
        $sut->addStrategy(new Strategy\SortOperationStrategy);
        $sut->addStrategy(new Strategy\LimitOperationStrategy);

        $AST = $sut->getAST();
        $this->assertEquals($expected, $AST);
    }

    /**
     * @return array<string>
     */
    public function parserProvider()
    {
        $tests = array();

        $eqAST = new AST\EqOperation;
        $eqAST->setProperty('name');
        $eqAST->setValue('foo');
        $tests['simple eq'] = array('eq(name,foo)', $eqAST);

        $eqASTwhitespace = new AST\EqOperation;
        $eqASTwhitespace->setProperty('name');
        $eqASTwhitespace->setValue('foo bar');
        $tests['simple eq with whitespace'] = array('eq(name,foo bar)', $eqASTwhitespace);

        $neAST = new AST\NeOperation;
        $neAST->setProperty('name');
        $neAST->setValue('bar');
        $tests['simple ne'] = array('ne(name,bar)', $neAST);

        $andAST = new AST\AndOperation;
        $andAST->addQuery($eqAST);
        $andAST->addQuery($neAST);
        $tests['simple and'] = array('and(eq(name,foo),ne(name,bar))', $andAST);

        $eqASTint = new AST\EqOperation;
        $eqASTint->setProperty('count');
        $eqASTint->setValue(1);
        $tests['integer in eq'] = array('eq(count,1)', $eqASTint);

        $orAST = new AST\OrOperation;
        $orAST->addQuery($eqAST);
        $orAST->addQuery($neAST);
        $tests['simple or'] = array('or(eq(name,foo),ne(name,bar))', $orAST);

        $ltAST = new AST\LtOperation;
        $ltAST->setProperty('count');
        $ltAST->setValue(1);
        $tests['lt attribute'] = array('lt(count,1)', $ltAST);

        $gtAST = new AST\GtOperation;
        $gtAST->setProperty('count');
        $gtAST->setValue(1);
        $tests['gt attribute'] = array('gt(count,1)', $gtAST);

        $lteAST = new AST\LteOperation;
        $lteAST->setProperty('count');
        $lteAST->setValue(1);
        $tests['lte attribute'] = array('lte(count,1)', $lteAST);

        $gteAST = new AST\GteOperation;
        $gteAST->setProperty('count');
        $gteAST->setValue(1);
        $tests['gte attribute'] = array('gte(count,1)', $gteAST);

        $sortAST = new AST\SortOperation;
        $sortAST->fields = array(array('count', 'asc'), array('name', 'desc'));
        $tests['sort'] = array('sort(+count,-name)', $sortAST);

        $likeAST = new AST\LikeOperation;
        $likeAST->setProperty('name');
        $likeAST->setValue('fo*');
        $tests['like'] = array('like(name,fo*)', $likeAST);

        $limitAST = new AST\LimitOperation;
        $limitAST->setSkip(0);
        $limitAST->setLimit(10);
        $tests['limit'] = array('limit(0,10)', $limitAST);

        $inAST = new AST\InOperation;
        $inAST->setProperty('name');
        $inAST->addValue('foo');
        $inAST->addValue('bar');
        $tests['in'] = array('in(name,[foo,bar])', $inAST);

        $outAST = new AST\OutOperation;
        $outAST->setProperty('name');
        $outAST->addValue('foo');
        $outAST->addValue('bar');
        $tests['out'] = array('out(name,[foo,bar])', $outAST);

        return $tests;
    }
}
