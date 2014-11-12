<?php

namespace Graviton\Rql\Queriable;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Graviton\Rql\QueryInterface;

// Doctrine\ODM\MongoDB\DocumentRepository
/**
 * Mongo ODM Queriable.
 * As an example, this is a partial Queriable implementation for applying
 * the queries in a RQL query to a Mongo ODM document repository. To use, just
 * construct the class with a valid Doctrine DocumentRepository instance.
 *
 * @category Graviton
 * @package  Rql
 * @author   Dario Nuevo <dario.nuevo@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
class MongoOdm implements QueryInterface
{

    /**
     * Repository
     *
     * @var DocumentRepository repository
     */
    private $repository;

    /**
     * Query Builder
     *
     * @var \Doctrine\ODM\MongoDB\Query\Builder query builder
     */
    private $qb;

    public function __construct(DocumentRepository $repository)
    {
        $this->repository = $repository;

        // init querybuilder..
        $this->qb = $this->repository->getDocumentManager()->createQueryBuilder()->find(
            $repository->getDocumentName()
        );
    }

    public function getDocuments()
    {
        $this->qb->field('testField')->equals('some text');

        $docs = array();
        foreach ($this->qb->getQuery()->execute() as $doc) {
            $docs[] = $doc;
        }

        return $docs;
    }

    public function andEqual($field, $value)
    {
        // TODO: Implement andEqual() method.
    }

    public function orEqual($field, $value)
    {
        // TODO: Implement orEqual() method.
    }

    public function sort($fieldName, $direction = '')
    {
        // TODO: Implement orEqual() method.
    }

}
