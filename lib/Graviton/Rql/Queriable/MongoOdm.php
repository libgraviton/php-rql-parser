<?php

namespace Graviton\Rql\Queriable;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Types\Type;
use Graviton\Rql\QueryInterface;

/**
 * Mongo ODM Queriable.
 * As an example, this is a partial Queriable implementation for applying
 * the queries in a RQL query to a Mongo ODM document repository. To use, just
 * construct the class with a valid Doctrine DocumentRepository instance.
 *
 * As MongoDB is type-sensitive on queries; we'll try our best to find out the defined
 * type of a field through its metadata and convert the php value to the defined value type.
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
     * The class metadata
     *
     * @var \Doctrine\ODM\MongoDb\Mapping\ClassMetadata
     */
    private $classMetadata;

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
        $this->classMetadata = $repository->getClassMetadata();

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
                     ->equals($this->roughTypeConvert($value, $field))
        );
    }

    /**
     * Some basic conversion for string to bool/null types..
     *
     * @param string $value Value
     *
     * @return bool|null The converted value
     */
    protected function roughTypeConvert($value, $fieldName)
    {
        // get the field type and convert..
        $fieldType = $this->classMetadata->getTypeOfField($fieldName);

        /**
         * this is an attempt to support type detecting on dot-notated (obj.obj.field) fields in the query.
         * i didn't find any way in doctrine odm to get the type directly, to i try to traverse the
         * relations and find it out this way..
         *
         * i don't want this to crash in any way (user enters non-existent documents), that's why it's
         * try-catched as doctrine will throw an exception if it doesn't exist.. i see this stuff as
         * best effort; so it should not crash the whole thing..
         */
        if (is_null($fieldType) && strpos($fieldName, '.') !== false) {
            try {
                $hierarchy = explode('.', $fieldName);

                // traverse doc metadata (not last; this is property name)
                $currentClass = $this->classMetadata;
                for ($i = 0; $i < count($hierarchy) - 1; $i++) {
                    $classMapping = $currentClass->getFieldMapping($hierarchy[$i]);

                    if (is_array($classMapping) && isset($classMapping['targetDocument'])) {
                        $currentClass = $this->repository->getDocumentManager()->getClassMetadata(
                            $classMapping['targetDocument']
                        );
                    } else {
                        // throw this so we stop; I'm aware it gets cached anyway..
                        throw new \Exception('could not resolve mapping');
                    }
                }

                // now we should have the deepest one in $currentClass..
                $fieldType = $currentClass->getTypeOfField($hierarchy[$i]);
            } catch (\Exception $e) {
                // just be silent..
            }
        }

        // if we don't find anything, return input
        $ret = $value;

        switch ($fieldType) {
            case Type::STRING:
                $ret = (string) $value;
                break;
            case Type::INTEGER:
                $ret = (int) $value;
                break;
            case Type::FLOAT:
                $ret = (float) $value;
                break;
            case Type::BOOLEAN:
                $ret = (bool) $value;
                break;
            case Type::DATE:
                // -> we only accept W3C format.. (i.e. 2001-12-31T15:00:00Z)
                $dt = \DateTime::createFromFormat(\DateTime::W3C, $value);
                if (count($dt->getLastErrors()['warnings']) === 0) {
                    $ret = $dt;
                }
                break;
        }

        // catch string null..
        if ($ret === 'null') {
            $ret = null;
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
                     ->equals($this->roughTypeConvert($value, $field))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function andNe($field, $value)
    {
        $this->qb->field($field)
                 ->notEqual($this->roughTypeConvert($value, $field));
    }

    /**
     * {@inheritdoc}
     */
    public function orNe($field, $value)
    {
        $this->qb->addOr(
            $this->qb->expr()
                     ->field($field)
                     ->notEqual($this->roughTypeConvert($value, $field))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function andGt($field, $value)
    {
        $this->qb->field($field)
                 ->gt($this->roughTypeConvert($value, $field));
    }

    /**
     * {@inheritdoc}
     */
    public function orGt($field, $value)
    {
        $this->qb->addOr(
            $this->qb->expr()
                     ->field($field)
                     ->gt($this->roughTypeConvert($value, $field))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function andGe($field, $value)
    {
        $this->qb->field($field)
                 ->gte($this->roughTypeConvert($value, $field));
    }

    /**
     * {@inheritdoc}
     */
    public function orGe($field, $value)
    {
        $this->qb->addOr(
            $this->qb->expr()
                     ->field($field)
                     ->gte($this->roughTypeConvert($value, $field))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function andLt($field, $value)
    {
        $this->qb->field($field)
                 ->lt($this->roughTypeConvert($value, $field));
    }

    /**
     * {@inheritdoc}
     */
    public function orLt($field, $value)
    {
        $this->qb->addOr(
            $this->qb->expr()
                     ->field($field)
                     ->lt($this->roughTypeConvert($value, $field))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function andLe($field, $value)
    {
        $this->qb->field($field)
                 ->lte($this->roughTypeConvert($value, $field));
    }

    /**
     * {@inheritdoc}
     */
    public function orLe($field, $value)
    {
        $this->qb->addOr(
            $this->qb->expr()
                     ->field($field)
                     ->lte($this->roughTypeConvert($value, $field))
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
