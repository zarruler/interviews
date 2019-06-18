<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 6/6/19
 * Time: 3:32 AM
 */

namespace App\Models;
use Core\Database\IntervalValue;
use Core\Model;

class Interval extends Model
{
    protected $tableName = 'intervals';

    public function getAll(int $fetchStyle = Model::FETCH_OBJ)
    {
        $query = "SELECT * FROM " . $this->tableName;
        $data = $this->fetchAll($query)->get($fetchStyle);
        return $data;
    }

    public function getOne(int $id, int $fetchStyle = Model::FETCH_OBJ)
    {
        $query = "SELECT * FROM " . $this->tableName . " WHERE id = :id";
        $params = [
            ':id' => $id
        ];
        $data = $this->fetch($query, $params)->get($fetchStyle);
        return $data[0];
    }


    /**
     * intersect types:
     * 1 # NEW START-END range is between existing start-end range
     * 2 # NEW START intersect or equal existing END
     * 3 # NEW START JOINS existing END
     * 4 # NEW END intersect or equal existing START
     * 5 # NEW END JOINS existing START
     * 6 # NEW START-END range is wide and include already existing ranges (even few)
     *
     * @param $startDate
     * @param $endDate
     * @return \Core\Database\IntervalValue[]
     * @throws \Exception
     */
    public function getIntervals($startDate, $endDate)
    {
        $query = "
            SELECT *
            FROM " . $this->tableName . " 
            WHERE (DATE_SUB(:start_date, INTERVAL 1 DAY) BETWEEN start_date AND end_date)
               OR (DATE_ADD(:end_date, INTERVAL 1 DAY) BETWEEN start_date AND end_date)
               OR (:start_date <= start_date AND :end_date >= end_date)
        ";

        $params = [
            ':start_date' => $startDate,
            ':end_date' => $endDate,
        ];
        $data = $this->fetchAll($query, $params)->get();

        return $data;
    }

    public function add(IntervalValue $newRecord)
    {

        $query = "INSERT INTO " . $this->tableName . " (start_date, end_date, price) 
                                                VALUES (:start_date, :end_date, :price)
        ";
        $params = [
            ':start_date' => $newRecord->getStartDate(IntervalValue::DEFAULT_DATE_FORMAT),
            ':end_date' => $newRecord->getEndDate(IntervalValue::DEFAULT_DATE_FORMAT),
            ':price' => $newRecord->getPrice(),
        ];
        $this->insert($query, $params, $newRecord);
    }

    public function edit(IntervalValue $updateRecord)
    {
        $query = "UPDATE " . $this->tableName . "
                  SET start_date = :start_date,
                      end_date = :end_date,
                      price = :price
                  WHERE id = :id            
        ";
        $params = [
            ':start_date' => $updateRecord->getStartDate(IntervalValue::DEFAULT_DATE_FORMAT),
            ':end_date' => $updateRecord->getEndDate(IntervalValue::DEFAULT_DATE_FORMAT),
            ':price' => $updateRecord->getPrice(),
            ':id' => $updateRecord->getId(),
        ];

        $this->update($query, $params, $updateRecord);

    }

    /**
     * accept array of integers, array of IntervalValue objects or mix of both
     * @param int[]|IntervalValue[] $data
     * @throws \Exception
     * @return int
     */
    public function delete(array $data) : int
    {
        $ids = [];
        $rowsAffected = 0;
        foreach ($data as $value) {
            $value instanceof IntervalValue ? $value = $value->getId() : null;

            if ($id = filter_var($value, FILTER_VALIDATE_INT))
                $ids[] = $id;
        }

        if(!empty($ids))
        {
            $query = "
                DELETE FROM " . $this->tableName . " 
                WHERE id in (" . str_repeat("?,", count($ids) - 1) . "?)
            ";

            $rowsAffected = $this->del($query, $ids);
            return $rowsAffected;
       }

        return $rowsAffected;
    }
}