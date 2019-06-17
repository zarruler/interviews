<?php

namespace App\Classes\Intervals\Strategy;


use App\Classes\Intervals\StrategyPriceInterface;
use Core\Database\IntervalValue;
use Core\Interfaces\IntervalPriceInterface;
use Core\Model;

/**
 * new interval somewhere between start and end of the existing interval
 * in this case we have 2 new intervals to add and one existing to modify
 *
 * Class BetweenStartEnd
 * @package App\Classes\Intervals\Strategy
 */
class BetweenStartEnd implements StrategyPriceInterface
{
    private $model;
    private $dbInterval;
    private $newInterval;

    public function __construct(Model $model, IntervalPriceInterface $dbInterval, IntervalPriceInterface $newInterval)
    {
        $this->model = $model;
        $this->dbInterval = $dbInterval;
        $this->newInterval = $newInterval;
    }

    public function doCalc()
    {
        if ($this->dbInterval->getPrice() == $this->newInterval->getPrice()) {
            $this->samePriceCalc();
        } else {
            $this->diffPriceCalc();
        }
    }

    public function samePriceCalc()
    {

    }

    public function diffPriceCalc()
    {
        // ORDER IS IMPORTANT !!!
        // firstly adding new interval
        $this->model->add($this->newInterval);

        // then creating another new interval based on first new interval END_date+1 as start date and
        // existing interval END date as end date
        $updStartDate = $this->newInterval->getEndDate()->add(new \DateInterval('P1D'));
        $secondNewInterval = new IntervalValue([
            'start_date' => $updStartDate,
            'end_date' => $this->dbInterval->getEndDate(),
            'price' => $this->dbInterval->getPrice()
        ]);
        $this->model->add($secondNewInterval);

        // updating existing interval
        $updEndDate = $this->newInterval->getStartDate()->sub(new \DateInterval('P1D'));
        $this->dbInterval->setEndDate($updEndDate);
        $this->model->edit($this->dbInterval);
    }
    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return IntervalPriceInterface
     */
    public function getDbInterval(): IntervalPriceInterface
    {
        return $this->dbInterval;
    }

    /**
     * @return IntervalPriceInterface
     */
    public function getNewInterval(): IntervalPriceInterface
    {
        return $this->newInterval;
    }
}