<?php
/**
 * ParserTest class file
 */

namespace Graviton\Rql;

use Graviton\Rql\Node\ElemMatchNode;
use Graviton\Rql\Lexer as GrvLexer;
use Xiag\Rql\Parser\Node;
use Xiag\Rql\Parser\Query;
use Xiag\Rql\Parser\QueryBuilder;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    http://swisscom.ch
 */
class ParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test elemMatch() operator
     *
     * @param string $rql           RQL query
     * @param Query  $expectedQuery Expected query
     * @return void
     *
     * @dataProvider dataElemMatchOperator
     */
    public function testElemMatchOperator($rql, Query $expectedQuery)
    {
        $this->assertEquals(
            $expectedQuery,
            Parser::createDefault()->parse((new GrvLexer())->tokenize($rql))
        );
    }

    /**
     * Data for elemMatch() test
     *
     * @return array
     */
    public function dataElemMatchOperator()
    {
        return [
            'simple' => [
                'elemMatch(x,eq(a,1))',
                (new QueryBuilder())
                    ->addQuery(new ElemMatchNode('x', new Node\Query\ScalarOperator\EqNode('a', 1)))
                    ->getQuery(),
            ],
            'with logic' => [
                'a=1&(b=2|elemMatch(x,(c=3|(d=4))))&limit(1,2)',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('a', 1))
                    ->addQuery(
                        new Node\Query\LogicOperator\OrNode(
                            [
                                new Node\Query\ScalarOperator\EqNode('b', 2),
                                new ElemMatchNode(
                                    'x',
                                    new Node\Query\LogicOperator\OrNode(
                                        [
                                            new Node\Query\ScalarOperator\EqNode('c', 3),
                                            new Node\Query\ScalarOperator\EqNode('d', 4),
                                        ]
                                    )
                                ),
                            ]
                        )
                    )
                    ->addLimit(new Node\LimitNode(1, 2))
                    ->getQuery(),
            ],
            'with logic with dashed' => [
                'a=1&(b=2|elemMatch(x,(c=string:3-r|(d=string:4%2Db))))&limit(1,2)',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('a', 1))
                    ->addQuery(
                        new Node\Query\LogicOperator\OrNode(
                            [
                                new Node\Query\ScalarOperator\EqNode('b', 2),
                                new ElemMatchNode(
                                    'x',
                                    new Node\Query\LogicOperator\OrNode(
                                        [
                                            new Node\Query\ScalarOperator\EqNode('c', '3-r'),
                                            new Node\Query\ScalarOperator\EqNode('d', '4-b'),
                                        ]
                                    )
                                ),
                            ]
                        )
                    )
                    ->addLimit(new Node\LimitNode(1, 2))
                    ->getQuery(),
            ],
        ];
    }
}
