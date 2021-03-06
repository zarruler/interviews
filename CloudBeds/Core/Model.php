<?php

namespace Core;

use Core\Database\ModelRecord;
use PDO;
use Psr\Container\ContainerInterface;

/**
 * Base model
 *
 */
abstract class Model
{
    const DB_NAMESPACE = '\\Core\\Database\\';
    const MODEL_NAMESPACE = '\\App\\Models\\';

    /**
     * return data as a collection of Objects
     */
    const FETCH_OBJ = 1;

    /**
     * return data as a collection of Arrays
     * as it is stored in object
     */
    const FETCH_ARR = 2;


    /**
     * array to store data collections
     * @var \Core\Database\ModelRecord[]
     */
    protected $collection = array();

    /**
     * @var PDO
     */
    protected $db;

    /**
     * data fetch mode stored here
     * we can set it outside model before the data receive
     * @var int
     */
    protected $fetchMode = self::FETCH_OBJ;


    public function __construct(ContainerInterface $container)
    {
        $dbType = ucfirst($container->get('db.type'));
        $class = self::DB_NAMESPACE . $dbType;

        try {
            $this->db = $container->get($class)->getConnection();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * filling model collection with the result objects
     *
     * @param array $data
     * @throws \ReflectionException
     */
    protected function fillCollection(array $data) : void
    {
        $this->collection = [];
        $recordValueObj = self::DB_NAMESPACE . ((new \ReflectionClass($this))->getShortName()) . 'Value';
        foreach($data as $key => $dataArr) {
            $this->collection[] = new $recordValueObj($dataArr);
        }
    }


    /**
     * fetch One record
     *
     * @param string $query
     * @param array $params
     * @return $this
     * @throws \ReflectionException
     */
    public function fetch(string $query, array $params = [])
    {
        $data = [];
        $q = $this->db->prepare($query);
        if(!$q->execute($params))
            throw new \Exception('Query Failed');
        if($q->rowCount() == 1) {
            $data[0] = $q->fetch(PDO::FETCH_ASSOC);
            $this->fillCollection($data);
        }

        return $this;
    }

    /**
     * fetch All records
     *
     * @param string $query
     * @param array $params
     * @return $this
     * @throws \ReflectionException
     */
    public function fetchAll(string $query, array $params = [])
    {
        $q = $this->db->prepare($query);
        if(!$q->execute($params))
            throw new \Exception('Query Failed');
        if($q->rowCount() > 0) {
            $data = $q->fetchAll(PDO::FETCH_ASSOC);
            $this->fillCollection($data);
        }

        return $this;
    }

    /**
     * Updating record
     *
     * @param string $query
     * @param array $params
     * @param ModelRecord $record
     * @return bool
     * @throws \Exception
     */
    public function update(string $query, array $params, ModelRecord $record) : bool
    {
        $q = $this->db->prepare($query);

        if(!$q->execute($params))
            throw new \Exception('Query Failed');

        $this->updateCollection($record);

        return true;
    }


    /**
     * Inserting Record
     *
     * @param string $query
     * @param array $params
     * @param ModelRecord $record
     * @return int
     * @throws \Exception
     */
    public function insert(string $query, array $params, ModelRecord $record) : int
    {
        $q = $this->db->prepare($query);

        if(!$q->execute($params))
            throw new \Exception('Query Failed');

        $id = $this->db->lastInsertId();
        $record->setId($id);
        $this->updateCollection($record);

        return $id;
    }

    /**
     * Deleting Record
     *
     * @param string $query
     * @param array $ids in format [1,2,5,7..xxx]
     * @return int
     * @throws \Exception
     */
    public function del(string $query, array $ids = []) : int
    {
        $q = $this->db->prepare($query);

        if(!$q->execute($ids))
            throw new \Exception('Query Failed');

        $rowsAffected = $q->rowCount();
        if($rowsAffected > 0)
            $this->cleanCollection($ids);

        return $rowsAffected;
    }


    /**
     * start transaction
     */
    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    /**
     * commit transaction
     */
    public function commit()
    {
        $this->db->commit();
    }

    /**
     * rollback transaction
     */
    public function rollBack()
    {
        $this->db->rollBack();
    }

    /**
     * this function fetchStyle has higher priority than model $this->fetchMode
     * if $overwriteStyle > 0 then $this->fetchMode will be overwritten with the $fetchStyle
     *
     * @param int $fetchStyle
     * @param int $overwriteStyle
     * @return array
     * @throws \Exception
     */
    public function get(int $fetchStyle = 0, $overwriteStyle = 0)
    {
        if(!$this->fetchMode && $fetchStyle == 0)
            $fetchStyle = $this->fetchMode = self::FETCH_OBJ;
        elseif($fetchStyle > 0 && $overwriteStyle)
            $this->fetchMode = $fetchStyle;
        elseif($fetchStyle == 0)
            $fetchStyle = $this->fetchMode;

        $data = [];
        switch ($fetchStyle)
        {
            case self::FETCH_OBJ:
                $data = $this->collection;
            break;
            case self::FETCH_ARR:
                foreach($this->collection as $key => $dataObj) {
                    $data[] = $dataObj->toArray();
                }
            break;
            default:
                throw new \Exception('Requested wrong data format');
        }


        return $data;
    }

    /**
     * updating collection after the database insert or update
     * @param ModelRecord $entityToUpdate
     */
    protected function updateCollection(ModelRecord $entityToUpdate) : void
    {
        foreach ($this->collection as $key => $entity)
            if ($entity->getId() == $entityToUpdate->getId())
                $this->collection[$key] = $entityToUpdate;
    }

    /**
     * delete from the collection elements provided in array
     *
     * @param array $ids
     */
    protected function cleanCollection(array $ids) : void
    {
        foreach ($this->collection as $key => $entity)
            if(in_array($entity->getId(), $ids))
                unset($this->collection[$key]);
    }

    /**
     * setting format of data to receive objects or arrays
     *
     * @param int $fetchMode
     * @return $this
     * @throws \Exception
     */
    public function setFetchMode(int $fetchMode) : Model
    {
        if(!in_array($fetchMode, [self::FETCH_OBJ, self::FETCH_ARR]))
            throw new \Exception('Requested wrong data format');

        $this->fetchMode = $fetchMode;

        return $this;
    }

    /**
     * @return int
     */
    public function getFetchMode() : int
    {
        return $this->fetchMode;
    }
}
