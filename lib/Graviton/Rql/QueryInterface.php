<?php

namespace Graviton\Rql;

/**
 * QueryInterface
 * Just implement this interface, adding your business logic to each function
 * as needed in your specific use case - this will be called a "Queriable".
 * Then once implemented, construct your object and pass it to Query.applyToQueriable().
 * The query class will then call all applicable methods in the Query string to your Queriable.
 *
 * @category Graviton
 * @package  Rql
 * @author   Dario Nuevo <dario.nuevo@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
interface QueryInterface {

    public function andEqual($field, $value);

    public function orEqual($field, $value);

} 