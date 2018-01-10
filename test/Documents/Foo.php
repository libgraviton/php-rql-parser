<?php
/**
 * foo document for testing
 */

namespace Graviton\Rql\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 *
 * @Document
 */
class Foo
{
    /**
     * @Id
     */
    private $id;

    /**
     * @Field(type="string")
     */
    public $name;

    /**
     * @Field(type="int")
     */
    public $count;
}
