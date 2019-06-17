<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 6/16/19
 * Time: 1:02 PM
 */

namespace Core\Interfaces;


interface IntervalInterface
{
    public function getId();
    public function setId(int $id);
    public function getStartDate();
    public function setStartDate(\DateTime $start_date);
    public function getEndDate();
    public function setEndDate(\DateTime $end_date);

    /**
     * Action store what should be done with the interval (insert/delete/update)
     * @return int
     */
    public function getAction() : int;
    public function setAction(int $actionId);
}