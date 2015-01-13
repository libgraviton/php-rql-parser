<?php

namespace Graviton\Rql\Queriable;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Graviton\Rql\QueryInterface;

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

    /**
     * Constructor; instanciate with a valid DocumentRepository instance
     *
     * @param DocumentRepository $repository repository
     */
    public function __construct(DocumentRepository $repository)
    {
        $this->repository = $repository;

        // init querybuilder..
        $this->qb = $this->repository->getDocumentManager()
                                     ->createQueryBuilder()
                                     ->find(
                                         $repository->getDocumentName()
                                     );
    }

    /**
     * Returns the result of the query; the array of Documents.
     * Call this after all query conditions were applied.
     *
     * @return array Results
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getDocuments()
    {
        $docs = array();
        foreach ($this->qb->getQuery()
                          ->execute() as $doc
        ) {
            $docs[] = $doc;
        }

        return $docs;
    }

    /**
     * {@inheritdoc}
     */
    public function andEq($field, $value)
    {
        $this->qb->addAnd(
            $this->qb->expr()
                     ->field($field)
                     ->equals($this->roughTypeConvert($value))
        );
    }

    /**
     * Some basic conversion for string to bool/null types..
     *
     * @param string $value Value
     *
     * @return bool|null The converted value
     */
    private function roughTypeConvert($value)
    {
        $ret = $value;
        if ($value == 'true') {
            $ret = true;
        }
        if ($value == 'false') {
            $ret = false;
        }
        if ($value == 'null') {
            $ret = null;
        }

        // is it maybe a date? -> we only accept W3C format.. (i.e. 2001-12-31T15:00:00Z)
        // @todo do this better someway.. couldn't check for target field type..
        if (strpos($value, ':') !== false && strpos($value, '-') !== false) {
            $dt = \DateTime::createFromFormat(\DateTime::W3C, $value);
            if (count($dt->getLastErrors()['warnings']) === 0) {
                $ret = $dt;
            }
        }

        if (is_numeric($value)) {
            $ret = floatval($value);
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function orEq($field, $value)
    {
        $this->qb->addOr(
            $this->qb->expr()
                     ->field($field)
                     ->equals($this->roughTypeConvert($value))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function andNe($field, $value)
    {
        $this->qb->field($field)
                 ->notEqual($this->roughTypeConvert($value));
    }

    /**
     * {@inheritdoc}
     */
    public function orNe($field, $value)
    {
        $this->qb->addOr(
            $this->qb->expr()
                     ->field($field)
                     ->notEqual($this->roughTypeConvert($value))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function andGt($field, $value)
    {
        $this->qb->field($field)
                 ->gt($this->roughTypeConvert($value));
    }

    /**
     * {@inheritdoc}
     */
    public function orGt($field, $value)
    {
        $this->qb->addOr(
            $this->qb->expr()
                     ->field($field)
                     ->gt($this->roughTypeConvert($value))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function andGe($field, $value)
    {
        $this->qb->field($field)
                 ->gte($this->roughTypeConvert($value));
    }

    /**
     * {@inheritdoc}
     */
    public function orGe($field, $value)
    {
        $this->qb->addOr(
            $this->qb->expr()
                     ->field($field)
                     ->gte($this->roughTypeConvert($value))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function andLt($field, $value)
    {
        $this->qb->field($field)
                 ->lt($this->roughTypeConvert($value));
    }

    /**
     * {@inheritdoc}
     */
    public function orLt($field, $value)
    {
        $this->qb->addOr(
            $this->qb->expr()
                     ->field($field)
                     ->lt($this->roughTypeConvert($value))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function andLe($field, $value)
    {
        $this->qb->field($field)
                 ->lte($this->roughTypeConvert($value));
    }

    /**
     * {@inheritdoc}
     */
    public function orLe($field, $value)
    {
        $this->qb->addOr(
            $this->qb->expr()
                     ->field($field)
                     ->lte($this->roughTypeConvert($value))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function sort($fieldName, $direction = null)
    {
        if ($direction == null) {
            $direction = 'asc';
        }

        $this->qb->sort($fieldName, $direction);
    }
}
