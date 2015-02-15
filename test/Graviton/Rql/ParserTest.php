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
            $this->assertEquals($expected->name, $AST->name);
            $this->assertEquals($expected->target, $AST->target);
            $this->assertEquals($expected->value, $AST->value);
    }

    /**
     * @return array<string>
     */
    public function parserProvider()
    {
        $tests = array();

        $eqAST = new AST\Operation('eq', 'name');
        $eqAST->value = 'foo';
        $tests['simple eq'] = array('eq(name,foo)', $eqAST);

        $eqASTwhitespace = new AST\Operation('eq', 'name');
        $eqASTwhitespace->value = 'foo bar';
        $tests['simple eq with whitespace'] = array('eq(name,foo bar)', $eqASTwhitespace);

        $neAST = new AST\Operation('ne', 'name');
        $neAST->value = 'foo';
        $tests['simple ne'] = array('ne(name,foo)', $neAST);

        return $tests;
    }
}
