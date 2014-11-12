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
interface QueryInterface
{

    /**
     * Apply "equal" condition; AND
     *
     * @param string $field Field name
     * @param mixed  $value Field value
     *
     * @return void
     */
    public function andEq($field, $value);

    /**
     * Apply "equal" condition; OR
     *
     * @param string $field Field name
     * @param mixed  $value Field value
     *
     * @return void
     */
    public function orEq($field, $value);

    /**
     * Apply "not equal" condition; AND
     *
     * @param string $field Field name
     * @param mixed  $value Field value
     *
     * @return void
     */
    public function andNe($field, $value);

    /**
     * Apply "not equal" condition; OR
     *
     * @param string $field Field name
     * @param mixed  $value Field value
     *
     * @return void
     */
    public function orNe($field, $value);

    /**
     * Apply "greater then" condition; AND
     *
     * @param string $field Field name
     * @param mixed  $value Field value
     *
     * @return void
     */
    public function andGt($field, $value);

    /**
     * Apply "greater then" condition; OR
     *
     * @param string $field Field name
     * @param mixed  $value Field value
     *
     * @return void
     */
    public function orGt($field, $value);

    /**
     * Apply "greater equals" condition; AND
     *
     * @param string $field Field name
     * @param mixed  $value Field value
     *
     * @return void
     */
    public function andGe($field, $value);

    /**
     * Apply "greater equals" condition; OR
     *
     * @param string $field Field name
     * @param mixed  $value Field value
     *
     * @return void
     */
    public function orGe($field, $value);

    /**
     * Apply "less then" condition; AND
     *
     * @param string $field Field name
     * @param mixed  $value Field value
     *
     * @return void
     */
    public function andLt($field, $value);

    /**
     * Apply "less then" condition; OR
     *
     * @param string $field Field name
     * @param mixed  $value Field value
     *
     * @return void
     */
    public function orLt($field, $value);

    /**
     * Apply "less equals" condition; AND
     *
     * @param string $field Field name
     * @param mixed  $value Field value
     *
     * @return void
     */
    public function andLe($field, $value);

    /**
     * Apply "less equals" condition; OR
     *
     * @param string $field Field name
     * @param mixed  $value Field value
     *
     * @return void
     */
    public function orLe($field, $value);

    /**
     * @param string      $fieldName Field name
     * @param string|null $direction Sort direction (asc/desc)
     *
     * @return mixed
     */
    public function sort($fieldName, $direction = null);
}
