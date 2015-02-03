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
     * @param string|array $query The user RQL query
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
     *
     * @return string query string
     */
    private function reconstructQueryFromArray(array $params)
    {
        $ret = array();
        foreach ($params as $name => $val) {
            $thisParam = $name;
            if (strlen($val) > 0) {
                $thisParam .= '=' . $val;
            }
            $ret[] = $thisParam;
        }

        return implode('&', $ret);
    }

    /**
     * Applies all parsed conditions to an implementation of the QueryInterface.
     * This allows you to implement your own filtering logic (for your data storage use case).
     *
     * @param QueryInterface $queriable A Queriable instance
     *
     * @return QueryInterface the interface instance; altered..
     */
    public function applyToQueriable(QueryInterface $queriable)
    {
        $conditions = $this->getParsedConditions();

        foreach ($conditions as $condition) {
            // set the name (i.e. andEq)
            $methodName = strtolower($condition['conditionType']) . ucfirst($condition['action']);
            if (method_exists($queriable, $methodName)) {
                call_user_func_array(
                    array($queriable, $methodName),
                    explode(',', $condition['actionParams'])
                );
            } else {
                // fallback to just action (i.e. sort)
                $methodName = strtolower($condition['action']);
                if (method_exists($queriable, $methodName)) {
                    call_user_func_array(
                        array($queriable, $methodName),
                        explode(',', $condition['actionParams'])
                    );
                }
            }
        }

        return $queriable;
    }

    /**
     * Parses an expression with Parser
     *
     * @deprecated this is an alias for getParsedConditions() now..
     * @return array the conditions
     */
    public function parse()
    {
        return $this->getConditions();
    }

    /**
     * Returns all parsed conditions in the internal array format
     *
     * @return array conditions
     */
    public function getParsedConditions()
    {
        $parser = new Parser($this->query);
        return $parser->parse();
    }

    /**
     * Returns all conditions as they came in (through the query). This is an
     * array, one item per condition.
     *
     * @return array conditions
     */
    public function getConditions()
    {
        $parser = new Parser($this->query);
        return $parser->getMatchingConditions();
    }
}
