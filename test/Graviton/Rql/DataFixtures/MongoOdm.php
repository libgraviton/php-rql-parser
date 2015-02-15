<?php
/**
 * small collection of basic features
 */

namespace Graviton\Rql\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Graviton\Rql\Documents\Foo;

/**
 * Make up some fixtures for testing
 *
 * @category MongoODM
 * @package  RqlParser
 * @author   Lucas Bickel <lucas.bickel@swisscom.com>
 * @license  http://opensource.org/licenses/MIT MIT License (c) 2015 Swisscom
 * @link     http://swisscom.ch
 */
class MongoOdm implements FixtureInterface
{
    /**
     * load fixtures with the passed em
     *
     *  @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $doc = new Foo;
        $doc->name = 'My First Sprocket';
        $doc->count = 10;

        $manager->persist($doc);

        $wheel = new Foo;
        $wheel->name = 'The Third Wheel';
        $wheel->count = 3;

        $manager->persist($wheel);

        $widget = new Foo;
        $widget->name = 'A Simple Widget';
        $widget->count = 100;

        $manager->persist($widget);

        $manager->flush();
    }
}
