<?php

namespace Graviton\Rql;

/**
 * RQL Parser
 * This class tries to form array structures form RQL queries.
 * It aims to be a reference implementation port to PHP of the js version located at
 * https://github.com/persvr/rql/blob/master/parser.js
 *
 * @category Graviton
 * @package  Rql
 * @author   Dario Nuevo <dario.nuevo@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
class Parser {

    private $query;

    private $allowedMethods = array(
        '=' => 'eq',
        '==' => 'eq',
        '>' => 'gt',
        '>=' => 'ge',
        '<' => 'lt',
        '<=' => 'le',
        '!=' => 'ne',
        'and' => 'and',
        'or' => 'or'
    );

    private $allowedConditions = array(
        '&' => 'and',
        '|' => 'or'
    );

    public function __construct($query) {
        $this->query = $query;
    }

    public function parse()
    {
        // @todo replace fiql expressions before parsing
        return $this->parseExpression();
    }

    private function parseExpression()
    {
        $ret = array();
        $pattern = '/([\(]?)([&|\|]?)([a-z|0-9|,]*)\(([a-z|0-9|,]*)\)([\)]?)/';

        preg_match_all($pattern, $this->query, $matches, PREG_SET_ORDER);

        $i = 1;
        $currentParent = 0;
        foreach ($matches as $match) {
            // just to make sure..
            array_walk($match, 'trim');

            $fullMatch = $match[0]; // full match text
            $openingCond = $match[1]; // "(" if set
            $conditionType = $match[2]; // & or |
            $action = $match[3]; // eq, lt and so on
            $actionParams = $match[4]; // params
            $closingCond = $match[5]; // ")" if set

            // cleanup
            if (strlen($conditionType) < 1) $conditionType = '&';

            if (in_array($action, $this->allowedMethods)) {

                if ($openingCond == '(') $currentParent = $i;

                $ret[$i] = array(
                    'conditionType' => $this->allowedConditions[$conditionType],
                    'action' => $action,
                    'actionParams' => $actionParams,
                    'parentCondition' => $currentParent
                );
                $i++;

                if ($closingCond == ')') $currentParent = 0;
            }
        }

        return $ret;
    }

}