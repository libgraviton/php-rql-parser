<?php
/**
 * foo document for testing
 */

namespace Graviton\Rql\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 *
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
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
