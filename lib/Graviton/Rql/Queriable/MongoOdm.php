<?php

namespace Graviton\Rql\Queriable;

use Graviton\Rql\QueryInterface;

/**
 * Mongo ODM Queriable.
 * As an example, this is a partial Queriable implementation for applying
 * the queries in a RQL query to a Mongo ODM document.
 *
 * @category Graviton
 * @package  Rql
 * @author   Dario Nuevo <dario.nuevo@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
class MongoOdm implements QueryInterface {

    private $document;

    public function __construct($document) {
        $this->document = $document;
    }

    public function andEqual($field, $value)
    {
        // TODO: Implement andEqual() method.
    }

    public function orEqual($field, $value)
    {
        // TODO: Implement orEqual() method.
    }
}