<?php
/**
 * acceptence tests for the MongoOdm queriable.
 */

namespace Graviton\Rql;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\ODM\MongoDB\DocumentManager;
use Xiag\Rql\Parser\Query;
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
     * @param boolean $skip     skip test
     *
     * @return void
     */
    public function testBasicQueries($query, $expected, $skip = false)
    {
        if ($skip) {
            $this->markTestSkipped();
        }
        $parser = new Parser(
            new Lexer,
            RqlParser::createDefault(),
            new MongoOdm($this->builder)
        );

        $parser->parse($query);
        $builder = $parser->buildQuery();

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
                'eq(name,'.rawurlencode('Not My Sprocket').')', array()
            ),
            'eq search for document by name' => array(
                'eq(name,'.rawurlencode('My First Sprocket').')', array(
                    array('name' => 'My First Sprocket')
                )
            ),
            'eq OR search' => array(
                'or(eq(name,'.rawurlencode('My First Sprocket').'),eq(name,'.rawurlencode('The Third Wheel').'))', array(
                    array('name' => 'My First Sprocket'),
                    array('name' => 'The Third Wheel')
                )
            ),
            'eq OR search with sugar' => array(
                'eq(name,'.rawurlencode('My First Sprocket').')|eq(name,'.rawurlencode('The Third Wheel').')', array(
                    array('name' => 'My First Sprocket'),
                    array('name' => 'The Third Wheel')
                ), true
            ),
            'ne search' => array(
                'ne(name,'.rawurlencode('My First Sprocket').')', array(
                    array('name' => 'The Third Wheel'),
                    array('name' => 'A Simple Widget'),
                )
            ),
            'eq AND search' => array(
                'and(eq(name,'.rawurlencode('My First Sprocket').'),eq(count,10))', array(
                    array('name' => 'My First Sprocket'),
                )
            ),
            'eq AND search with sugar' => array(
                'eq(name,'.rawurlencode('My First Sprocket').')&eq(count,10)', array(
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
            'sort by int' => array(
                'sort(count)', array(
                    array('count' => 3),
                    array('count' => 10),
                    array('count' => 100),
                ), true
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
            'string sort' => array(
                'sort(name)', array(
                    array('name' => 'A Simple Widget', 'count' => 100),
                    array('name' => 'My First Sprocket', 'count' => 10),
                    array('name' => 'The Third Wheel', 'count' => 3),
                ), true
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
                'in(name,('.rawurlencode('The Third Wheel').'))', array(
                    array('name' => 'The Third Wheel')
                )
            ),
            'out() search' => array(
                'out(name,('.rawurlencode('A Simple Widget,My First Sprocket').'))', array(
                    array('name' => 'The Third Wheel')
                ), true
            ),
            'like and limit search' => array(
                'like(name,'.rawurlencode('*et').'),limit(1)', array(
                    array('name' => 'My First Sprocket')
                ), true
            ),
            'complex example from #6 without sugar' => array(
                'or(and(eq(name,'.rawurlencode('The Third Wheel').'),lt(count,10)),eq(count,100))', array(
                    array('name' => 'The Third Wheel', 'count' => 3),
                    array('name' => 'A Simple Widget', 'count' => 100),
                )
            ),
        );
    }
}
