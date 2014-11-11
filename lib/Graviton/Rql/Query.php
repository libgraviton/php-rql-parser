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
class Query {

    private $query;

    public function __construct($query) {
        $this->query = $query;
    }

    public function parse()
    {
        $parser = new Parser($this->query);
        return $parser->parse();
    }

    public function applyToQueriable(QueryInterface $queriable)
    {

    }


}