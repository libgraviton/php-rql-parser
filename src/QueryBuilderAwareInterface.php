<?php
/**
 * Interface QueryBuilderAwareInterface
 */

namespace Graviton\Rql;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://swisscom.ch
 */
interface QueryBuilderAwareInterface
{
    /**
     * @param Builder $builder query builder
     *
     * @return void
     */
    public function setBuilder(Builder $builder);

    /**
     * Provides the  Doctrine QueryBuilder
     *
     * @return Builder
     */
    public function getBuilder();
    /**
     * @param DocumentRepository $repository repository
     *
     * @return void
     */
    public function setRepository(DocumentRepository $repository);

    /**
     * Provides the repository
     *
     * @return DocumentRepository
     */
    public function getRepository();
}
