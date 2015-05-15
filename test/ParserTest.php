<?php
/**
 * verify that parser build a correct AST
 */

namespace Graviton\Rql;

use Graviton\Rql\AST;
use Graviton\Rql\Parser;

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
     *
     * @param string $rql      rql expression
     * @param object $expected expected AST object
     *
     * @return void
     */
    public function testParser($rql, $expected)
    {
        $sut = Parser::createParser($rql);

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
        $eqAST->setValue('"12"');
        $tests['simple eq with numeric in double quotes'] = array('eq(name,"12")', $eqAST);

        $eqAST = new AST\EqOperation;
        $eqAST->setProperty('name');
        $eqAST->setValue("'12'");
        $tests['simple eq with numeric in single quotes'] = array("eq(name,'12')", $eqAST);

        $eqAST = new AST\EqOperation;
        $eqAST->setProperty('name');
        $eqAST->setValue("\"Hans 'Housi' Wale-Sepp\"");
        $tests['simple eq with mixed quotes'] = array("eq(name,\"Hans 'Housi' Wale-Sepp\")", $eqAST);

        $eqAST = new AST\EqOperation;
        $eqAST->setProperty('shout');
        $eqAST->setValue("it's a cake!!");
        $tests['simple eq single quote'] = array("eq(shout,it's a cake!!)", $eqAST);

        $eqAST = new AST\EqOperation;
        $eqAST->setProperty('shout');
        $eqAST->setValue("it's a \"cake\" \"blaster\"!!");
        $tests['simple eq single quote'] = array("eq(shout,it's a \"cake\" \"blaster\"!!)", $eqAST);

        $eqAST = new AST\EqOperation;
        $eqAST->setProperty('noText');
        $eqAST->setValue("''");
        $tests['simple eq single quotes only'] = array("eq(noText,'')", $eqAST);

        $eqAST = new AST\EqOperation;
        $eqAST->setProperty('name');
        $eqAST->setValue('foo');
        $tests['simple eq'] = array('eq(name,foo)', $eqAST);

        $eqASTwhitespace = new AST\EqOperation;
        $eqASTwhitespace->setProperty('name');
        $eqASTwhitespace->setValue('foo bar');
        $tests['simple eq with whitespace'] = array('eq(name,foo bar)', $eqASTwhitespace);

        $eqASTchars = new AST\EqOperation;
        $eqASTchars->setProperty('name-part+test');
        $eqASTchars->setValue('foo+bar-baz');
        $tests['simple eq with concatecators'] = array('eq(name-part+test,foo+bar-baz)', $eqASTchars);

        $eqASTchars = new AST\EqOperation;
        $eqASTchars->setProperty('name-part+test');
        $eqASTchars->setValue('foo+bar-');
        $tests['simple eq with ending concatenator'] = array('eq(name-part+test,foo+bar-)', $eqASTchars);

        $eqASTchars = new AST\EqOperation;
        $eqASTchars->setProperty('name-part+test');
        $eqASTchars->setValue('+foo-bar');
        $tests['simple eq with starting concatenator'] = array('eq(name-part+test,+foo-bar)', $eqASTchars);

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

        $sortASTCanon = new AST\SortOperation;
        $sortASTCanon->addField(array('count'));
        $sortASTCanon->addField(array('asc'));
        $tests['sort with asc as param'] = array('sort(count,asc)', $sortASTCanon);

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

        $booleanAST = new AST\EqOperation;
        $booleanAST->setProperty('name');
        $booleanAST->setValue(true);
        $tests['boolean true in AST'] = array('eq(name,true)', $booleanAST);

        $booleanFalseAST = new AST\EqOperation;
        $booleanFalseAST->setProperty('name');
        $booleanFalseAST->setValue(false);
        $tests['boolean false in AST'] = array('eq(name,false)', $booleanFalseAST);

        return $tests;
    }

    /**
     * Test parser exception handling
     *
     * @return void
     */
    public function testParserExpectingException()
    {
        $sut = Parser::createParser("eq(name,)");

        $this->setExpectedException('\\LogicException');

        $sut->getAST();
    }

    /**
     * Test resourceQuery exception handling
     *
     * @return void
     */
    public function testResourceQueryExpectingException()
    {
        $sut = new Parser('eq(foo,bar)');


        $this->setExpectedException('\RuntimeException');
        $sut->getAST();
    }
}
