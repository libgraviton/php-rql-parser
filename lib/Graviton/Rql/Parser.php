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
        '!=' => 'ne'
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
        $ret = array();

        // @todo replace fiql expressions

        // eventuell noch klammern vor und hintendran parsen und merken wann sie gekommen sind
        // das ist glaub der ansatz - wenn "(" alleine - opening brance - dann parent setzen bis ")" kommt..
        $pattern = '/([\(]?)([&|\|]?)([a-z|0-9|,]*)\(([a-z|0-9|,]*)\)([\)]?)/';

        preg_match_all($pattern, $this->query, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            array_walk($match, 'trim');
            $fullMatch = $match[0]; // full match text
            $openingCond = $match[1]; // "(" if set
            $cond = $match[2]; // & or |
            $action = $match[3]; // eq, lt and so on
            $actionParams = $match[4]; // params

            // cleanup
            if (strlen($cond) < 1) $cond = '&';

            if (in_array($action, $this->allowedMethods)) {
                $ret[] = array(
                    'cond' => $this->allowedConditions[$cond],
                    'action' => $action,
                    'actionParams' => $actionParams,
                );
            }

        }

    }

}