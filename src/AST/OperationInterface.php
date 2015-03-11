<?php

namespace Graviton\Rql\AST;

use Graviton\Rql\Visitor\VisitorInterface;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
interface OperationInterface
{
    public function __construct($name);

    public function accept(VisitorInterface $visitor);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getProperty();

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return array<string[]>
     */
    public function getFields();

    /**
     * @return OperationInterface[]
     */
    public function getQueries();
}
