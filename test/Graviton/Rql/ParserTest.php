<?php

namespace Graviton\Rql;

use Graviton\Rql\AST;

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

        $AST = $sut->getAST();
        $this->assertEquals($expected, $AST);
    }

    /**
     * @return array<string>
     */
    public function parserProvider()
    {
        $tests = array();

        $eqAST = new AST\Operation('eq');
        $eqAST->property = 'name';
        $eqAST->value = 'foo';
        $tests['simple eq'] = array('eq(name,foo)', $eqAST);

        $eqASTwhitespace = new AST\Operation('eq', 'name');
        $eqASTwhitespace->property = 'name';
        $eqASTwhitespace->value = 'foo bar';
        $tests['simple eq with whitespace'] = array('eq(name,foo bar)', $eqASTwhitespace);

        $neAST = new AST\Operation('ne', 'name');
        $neAST->property = 'name';
        $neAST->value = 'bar';
        $tests['simple ne'] = array('ne(name,bar)', $neAST);

        $andAST = new AST\Operation('and');
        $andAST->queries = array($eqAST, $neAST);
        $tests['simple and'] = array('and(eq(name,foo),ne(name,bar))', $andAST);

        $eqASTint = new AST\Operation('eq', 'count');
        $eqASTint->property = 'count';
        $eqASTint->value = 1;
        $tests['integer in eq'] = array('eq(count,1)', $eqASTint);

        $orAST = new AST\Operation('or');
        $orAST->queries = array($eqAST, $neAST);
        $tests['simple or'] = array('or(eq(name,foo),ne(name,bar))', $orAST);

        $ltAST = new AST\Operation('lt');
        $ltAST->property = 'count';
        $ltAST->value = 1;
        $tests['lt attribute'] = array('lt(count,1)', $ltAST);

        $gtAST = new AST\Operation('gt');
        $gtAST->property = 'count';
        $gtAST->value = 1;
        $tests['gt attribute'] = array('gt(count,1)', $gtAST);

        $lteAST = new AST\Operation('lte');
        $lteAST->property = 'count';
        $lteAST->value = 1;
        $tests['lte attribute'] = array('lte(count,1)', $lteAST);

        $gteAST = new AST\Operation('gte');
        $gteAST->property = 'count';
        $gteAST->value = 1;
        $tests['gte attribute'] = array('gte(count,1)', $gteAST);

        $sortAST = new AST\Operation('sort');
        $sortAST->fields = array('count' => 'asc', 'name' => 'desc');
        $test['sort'] = array('sort(+count,-name)', $sortAST);

        return $tests;
    }
}
