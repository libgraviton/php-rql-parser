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
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
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
