<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 6/6/19
 * Time: 3:32 AM
 */

namespace App\Models;
use Core\Database\IntervalValue;
use Core\Interfaces\ModelInterface;
use Core\Model;

class Interval extends Model implements ModelInterface
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
        /*
        * Todo: Clean query from the comments
        */
        $query = "
            SELECT *, 
            (CASE
                WHEN :start_date >= start_date AND :end_date <= end_date THEN 1 # NEW START-END range is between existing start-end range
                WHEN :start_date BETWEEN start_date AND end_date THEN 2         # NEW START intersect or equal existing END
                WHEN :start_date BETWEEN start_date AND end_date+1 THEN 3       # NEW START JOINS existing END
                WHEN :end_date BETWEEN start_date AND end_date THEN 4           # NEW END intersect or equal existing START
                WHEN :end_date BETWEEN start_date-1 AND end_date THEN 5         # NEW END JOINS existing START
                WHEN :start_date <= start_date AND :end_date >= end_date THEN 6 # NEW START-END range is wide and include already existing ranges (even few)
            END) as 'intersect'
            FROM " . $this->tableName . " 
            WHERE (:start_date BETWEEN start_date AND end_date + 1)
               OR (:end_date BETWEEN start_date - 1 AND end_date)
               OR (:start_date <= start_date AND :end_date >= end_date)
        ";

        $params = [
            ':start_date' => $startDate,
            ':end_date' => $endDate,
        ];
        $data = $this->fetchAll($query, $params)->get();
//var_dump($data);
        return $data;
    }

    public function add(IntervalValue $newRecord)
    {
        $query = "INSERT INTO " . $this->tableName . " (start_date, end_date, price) 
                                                VALUES (:start_date, :end_date, :price)
        ";
        $params = [
            ':start_date' => $newRecord->getStartDate(),
            ':end_date' => $newRecord->getEndDate(),
            ':price' => $newRecord->getPrice(),
        ];
        $this->insert($query, $params);
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
            ':start_date' => $updateRecord->getStartDate(),
            ':end_date' => $updateRecord->getEndDate(),
            ':price' => $updateRecord->getPrice(),
            ':id' => $updateRecord->getId(),
        ];
        $this->update($query, $params);
    }

}