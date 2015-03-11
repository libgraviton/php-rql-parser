<?php
/**
 * Exception handling
 */
namespace Graviton\Rql;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
class RqlParserException extends \Exception
{
    const VISITOR_INTERFACE_NOT_IMPLEMENTED = 412;
    const VISITOR_NOT_SUPPORTED = 405;
}
