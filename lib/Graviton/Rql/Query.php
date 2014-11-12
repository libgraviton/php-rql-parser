<?php

namespace Graviton\Rql;

/**
 * Query class
 * Glue code, wrapping the other stuff. See readme.md for
 * an example.
 *
 * @category Graviton
 * @package  Rql
 * @author   Dario Nuevo <dario.nuevo@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
class Query
{

    /**
     * The rql query
     *
     * @var string the query
     */
    private $query;

    /**
     * Constructor
     *
     * @param $query string|array The user RQL query
     */
    public function __construct($query)
    {
        // if it's an array, assume its key => param name, value => param value
        if (is_array($query)) {
            $query = $this->reconstructQueryFromArray($query);
        }

        $this->query = $query;
    }

    /**
     * Parses an expression with Parser
     *
     * @return array the conditions
     */
    public function parse()
    {
        $parser = new Parser($this->query);
        return $parser->parse();
    }

    /**
     * If we get an array of params instead of a string,
     * we assume that it's keys are the value field, it's values
     * the filter value (or empty/null if key is a pure rql expression).
     *
     * Thus we reconstruct it that way, also assuming it's urldecoded already..
     * We DON'T use http_build_query() because:
     * * it urlencodes again
     * * if value is empty, it makes a "name=&" part, which is correct for
     * the purpose of http_build_query() but unnecessary for us
     *
     * @param array $params the params
     */
    private function reconstructQueryFromArray(array $params)
    {
        $ret = array();
        foreach ($params as $name => $val) {
            $thisParam = $name;
            if (strlen($val) > 0) $thisParam .= '='.$val;
            $ret[] = $thisParam;
        }

        return implode('&', $ret);
    }

    /**
     * Applies all parsed conditions to an implementation of the QueryInterface.
     * This allows you to implement your own filtering logic (for your data storage use case).
     *
     * @param QueryInterface $queriable A Queriable instance
     */
    public function applyToQueriable(QueryInterface $queriable)
    {
        $conditions = $this->parse();

        foreach ($conditions as $condition) {
            // set the name (i.e. andEq)
            $methodName = strtolower($condition['conditionType']).ucfirst($condition['action']);
            if (method_exists($queriable, $methodName)) {
                $queriable->$methodName($condition['actionParams']);
            } else {
                // fallback to just action (i.e. sort)
                $methodName = strtolower($condition['action']);
                if (method_exists($queriable, $methodName)) {
                    $queriable->$methodName($condition['actionParams']);
                }
            }
        }

        return $queriable;
    }
}
