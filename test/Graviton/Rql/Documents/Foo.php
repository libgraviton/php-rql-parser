<?php
/**
 * foo document for testing
 */

namespace Graviton\Rql\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 *
 * @category MongoODM
 * @package  RqlParser
 * @author   Lucas Bickel <lucas.bickel@swisscom.com>
 * @license  http://opensource.org/licenses/MIT MIT License (c) 2015 Swisscom
 * @link     http://swisscom.ch
 */
class Foo
{
    /**
     * @ODM\Id
     */
    private $id;

    /**
     * @ODM\String
     */
    public $name;

    /**
     * @ODM\Int
     */
    public $count;
}
