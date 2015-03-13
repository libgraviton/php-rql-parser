<?php

namespace Graviton\Rql;

use Graviton\Rql\AST;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test parser
     *
     * @dataProvider parserProvider
     */
    public function testParser($rql, $expected)
    {
        $sut = \Graviton\Rql\Parser::createParser($rql);

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

        $tripleAndAST = new AST\AndOperation;
        $tripleAndAST->addQuery($eqAST);
        $tripleAndAST->addQuery($eqAST);
        $tripleAndAST->addQuery($eqAST);
        $tests['triple and'] = array('and(eq(name,foo),eq(name,foo),eq(name,foo))', $tripleAndAST);

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
        $sortAST->addField(array('count', 'asc'));
        $sortAST->addField(array('name', 'desc'));
        $tests['sort'] = array('sort(+count,-name)', $sortAST);

        $sortAST2 = new AST\SortOperation;
        $sortAST2->addField(array('count'));
        $sortAST2->addField(array('asc'));
        $tests['sort with asc as param'] = array('sort(count,asc)', $sortAST2);

        $likeAST = new AST\LikeOperation;
        $likeAST->setProperty('name');
        $likeAST->setValue('fo*');
        $tests['like'] = array('like(name,fo*)', $likeAST);

        $limitAST = new AST\LimitOperation;
        $limitAST->setSkip(0);
        $limitAST->setLimit(10);
        $tests['limit'] = array('limit(10)', $limitAST);

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

        $gtLimitAST = new AST\QueryOperation;
        $gtLimitAST->addQuery($gtAST);
        $gtLimitAST->addQuery($limitAST);
        $tests['gt and limit'] = array('gt(count,1),limit(10)', $gtLimitAST);

        $complexAST = new AST\OrOperation;
        $complexAST->addQuery($andAST);
        $complexAST->addQuery($gtAST);
        $tests['complex nested query'] = array('or(and(eq(name,foo),ne(name,bar)),gt(count,1))', $complexAST);

        return $tests;
    }
}
