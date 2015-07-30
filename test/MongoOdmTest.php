<?php
/**
 * acceptence tests for the MongoOdm queriable.
 */

namespace Graviton\Rql;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\ODM\MongoDB\DocumentManager;
use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Parser as RqlParser;
use Graviton\Rql\Visitor\MongoOdm;
use Graviton\Rql\DataFixtures\MongoOdm as MongoOdmFixtures;
use Doctrine\Common\DataFixtures\Executor\MongoDBExecutor;
use Doctrine\Common\DataFixtures\Purger\MongoDBPurger;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

/**
 * run tests against local mongodb with loaded fixtures
 *
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class MongoOdmTest extends \PHPUnit_Framework_TestCase
{
    private $builder;

    /**
     * setup mongo-odm and load fixtures
     *
     * @return void
     */
    public function setUp()
    {
        AnnotationDriver::registerAnnotationClasses();

        $config = new Configuration();
        $config->setHydratorDir('/tmp/hydrators');
        $config->setHydratorNamespace('Hydrators');
        $config->setProxyDir('/tmp/proxies');
        $config->setProxyNamespace('Proxies');
        $config->setMetadataDriverImpl(AnnotationDriver::create(__DIR__ . '/Documents/'));

        $dm = DocumentManager::create(new Connection(), $config);

        $loader = new Loader();
        $loader->addFixture(new MongoOdmFixtures());

        $executor = new MongoDBExecutor($dm, new MongoDBPurger());
        $executor->execute($loader->getFixtures());

        $this->builder = $dm->createQueryBuilder('Graviton\Rql\Documents\Foo');
    }

    /**
     * @dataProvider basicQueryProvider
     *
     * @param string  $query    rql query string
     * @param array[] $expected structure of expected return value
     *
     * @return void
     */
    public function testBasicQueries($query, $expected)
    {
        $lexer = new Lexer;
        $parser = RqlParser::createDefault();
        $visitor = new MongoOdm($this->builder);

        $rqlQuery = $parser->parse($lexer->tokenize($query));
        $builder = $visitor->visit($rqlQuery);

        $results = [];
        foreach ($builder->getQuery()->execute() as $doc) {
            $results[] = $doc;
        }

        $this->assertEquals(count($expected), count($results), 'record count mismatch');

        foreach ($expected as $position => $data) {
            foreach ($data as $name => $value) {
                $this->assertEquals($value, $results[$position]->$name);
            }
        }
    }

    /**
     * @return array<string>
     */
    public function basicQueryProvider()
    {
        return array(
            'eq search for non existant document' => array(
                'eq(name,'.$this->encodeString('Not My Sprocket').')', array()
            ),
            'eq search for document by name' => array(
                'eq(name,'.$this->encodeString('My First Sprocket').')', array(
                    array('name' => 'My First Sprocket')
                )
            ),
            'eq OR search' => array(
                'or(eq(name,'.$this->encodeString('My First Sprocket').'),eq(name,'.$this->encodeString('The Third Wheel').'))',
                array(
                    array('name' => 'My First Sprocket'),
                    array('name' => 'The Third Wheel')
                )
            ),
            'eq OR search with sugar' => array(
                '(eq(name,'.$this->encodeString('My First Sprocket').')|eq(name,'.$this->encodeString('The Third Wheel').'))', array(
                    array('name' => 'My First Sprocket'),
                    array('name' => 'The Third Wheel')
                ),
            ),
            'like OR search' => array(
                'or(like(name,*'.$this->encodeString('First').'),like(name,*'.$this->encodeString('Wheel').'))',
                array(
                    array('name' => 'My First Sprocket'),
                    array('name' => 'The Third Wheel')
                )
            ),
            'ne search' => array(
                'ne(name,'.$this->encodeString('My First Sprocket').')', array(
                    array('name' => 'The Third Wheel'),
                    array('name' => 'A Simple Widget'),
                )
            ),
            'eq AND search' => array(
                'and(eq(name,'.$this->encodeString('My First Sprocket').'),eq(count,10))', array(
                    array('name' => 'My First Sprocket'),
                )
            ),
            'eq AND search with sugar' => array(
                'eq(name,'.$this->encodeString('My First Sprocket').')&eq(count,10)', array(
                    array('name' => 'My First Sprocket'),
                )
            ),
            'gt 10 search' => array(
                'gt(count,10)', array(
                    array('name' => 'A Simple Widget', 'count' => 100)
                )
            ),
            'ge 10 search' => array(
                'ge(count,10)', array(
                    array('name' => 'My First Sprocket'),
                    array('name' => 'A Simple Widget', 'count' => 100)
                )
            ),
            'lt 10 search' => array(
                'lt(count,10)', array(
                    array('name' => 'The Third Wheel', 'count' => 3)
                )
            ),
            'le 10 search' => array(
                'le(count,10)', array(
                    array('name' => 'My First Sprocket', 'count' => 10),
                    array('name' => 'The Third Wheel', 'count' => 3)
                )
            ),
            'sort by int explicit' => array(
                'sort(+count)', array(
                    array('count' => 3),
                    array('count' => 10),
                    array('count' => 100),
                )
            ),
            'reverse sort by int' => array(
                'sort(-count)', array(
                    array('count' => 100),
                    array('count' => 10),
                    array('count' => 3),
                )
            ),
            'string sort explicit ' => array(
                'sort(+name)', array(
                    array('name' => 'A Simple Widget', 'count' => 100),
                    array('name' => 'My First Sprocket', 'count' => 10),
                    array('name' => 'The Third Wheel', 'count' => 3),
                )
            ),
            'reverse string sort' => array(
                'sort(-name)', array(
                    array('name' => 'The Third Wheel', 'count' => 3),
                    array('name' => 'My First Sprocket', 'count' => 10),
                    array('name' => 'A Simple Widget', 'count' => 100),
                )
            ),
            'like search' => array(
                'like(name,My*)', array(
                    array('name' => 'My First Sprocket', 'count' => 10),
                )
            ),
            'limit(1) search' => array(
                'limit(1)', array(
                    array('name' => 'My First Sprocket', 'count' => 10),
                )
            ),
            'limit(1,1) search' => array(
                'limit(1,1)', array(
                    array('name' => 'The Third Wheel', 'count' => 3),
                )
            ),
            'in() search' => array(
                'in(name,('.$this->encodeString('The Third Wheel').'))', array(
                    array('name' => 'The Third Wheel')
                )
            ),
            'out() search' => array(
                'out(name,('.$this->encodeString('A Simple Widget').','.$this->encodeString('My First Sprocket').'))', array(
                    array('name' => 'The Third Wheel')
                ),
            ),
            'like and limit search' => array(
                'like(name,*'.$this->encodeString('et').')&limit(1)', array(
                    array('name' => 'My First Sprocket')
                ),
            ),
            'like without glob' => array(
                'like(name,'.$this->encodeString('The Third Wheel').')', array(
                    array('name' => 'The Third Wheel')
                )
            ),
            'complex example from #6 without sugar' => array(
                'or(and(eq(name,'.$this->encodeString('The Third Wheel').'),lt(count,10)),eq(count,100))', array(
                    array('name' => 'The Third Wheel', 'count' => 3),
                    array('name' => 'A Simple Widget', 'count' => 100),
                )
            ),

            'lt() with asc sort() by count' => [
                'lt(count,50)&sort(+count)',
                [
                    ['name' => 'The Third Wheel', 'count' => 3],
                    ['name' => 'My First Sprocket', 'count' => 10],
                ],
            ],
            'lt() with desc sort() by count' => [
                'lt(count,50)&sort(-count)',
                [
                    ['name' => 'My First Sprocket', 'count' => 10],
                    ['name' => 'The Third Wheel', 'count' => 3],
                ],
            ],

            'lt() with asc sort() by name' => [
                sprintf('lt(name,%s)&sort(+name)', $this->encodeString('The Third Wheel')),
                [
                    ['name' => 'A Simple Widget', 'count' => 100],
                    ['name' => 'My First Sprocket', 'count' => 10],
                ],
            ],
            'lt() with desc sort() by name' => [
                sprintf('lt(name,%s)&sort(-name)', $this->encodeString('The Third Wheel')),
                [
                    ['name' => 'My First Sprocket', 'count' => 10],
                    ['name' => 'A Simple Widget', 'count' => 100],
                ],
            ],

            'lt() by count with asc sort() by name' => [
                'lt(count,50)&sort(+name)',
                [
                    ['name' => 'My First Sprocket', 'count' => 10],
                    ['name' => 'The Third Wheel', 'count' => 3],
                ],
            ],
            'lt() by count with desc sort() by name' => [
                'lt(count,50)&sort(-name)',
                [
                    ['name' => 'The Third Wheel', 'count' => 3],
                    ['name' => 'My First Sprocket', 'count' => 10],
                ],
            ],

            'lt() by name & count with asc sort() by name & count' => [
                sprintf('or(lt(count,50),lt(name,%s))&sort(+name,+count)', $this->encodeString('My')),
                [
                    ['name' => 'A Simple Widget', 'count' => 100],
                    ['name' => 'My First Sprocket', 'count' => 10],
                    ['name' => 'The Third Wheel', 'count' => 3],
                ],
            ],
            'lt() by name & count with desc sort() by name & count' => [
                sprintf('or(lt(count,50),lt(name,%s))&sort(+count,-name)', $this->encodeString('My')),
                [
                    ['name' => 'The Third Wheel', 'count' => 3],
                    ['name' => 'My First Sprocket', 'count' => 10],
                    ['name' => 'A Simple Widget', 'count' => 100],
                ],
            ],
        );
    }

    /**
     * @dataProvider errorQueryProvider
     *
     * @param string $query        rql query string
     * @param string $errorMessage structure of expected return value
     *
     * @return void
     */
    public function testErrorQueries($query, $errorMessage)
    {
        $this->setExpectedException('Xiag\Rql\Parser\Exception\SyntaxErrorException', $errorMessage);

        RqlParser::createDefault()->parse(
            (new Lexer())
                ->tokenize($query)
        );
    }
    /**
     * @return array<string>
     */
    public function errorQueryProvider()
    {
        return [
            'sort by int' => [
                'sort(count)',
                'Unexpected token "count" (T_STRING) (expected T_PLUS|T_MINUS)'
            ],
            'string sort' => [
                'sort(name)',
                'Unexpected token "name" (T_STRING) (expected T_PLUS|T_MINUS)'
            ],
        ];
    }


    /**
     * Proper string encoding
     *
     * @param string $value String value
     * @return string
     */
    private function encodeString($value)
    {
        return strtr(rawurlencode($value), [
            '-' => '%2D',
            '_' => '%5F',
            '.' => '%2E',
            '~' => '%7E',
        ]);
    }
}
