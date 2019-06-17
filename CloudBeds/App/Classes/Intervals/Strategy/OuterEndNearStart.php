<?php

namespace App\Classes\Intervals\Strategy;

use App\Classes\Intervals\StrategyPriceInterface;
use Core\Interfaces\IntervalPriceInterface;
use Core\Model;

/**
 * NEW END JOINS existing START
 *
 * Class OuterEndNearStart
 * @package App\Classes\Intervals\Strategy
 */
class OuterEndNearStart implements StrategyPriceInterface
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
        // expanding existing range. existing START update with the new START
        $this->dbInterval->setStartDate($this->newInterval->getStartDate());
        $this->model->edit($this->dbInterval);
    }

    public function diffPriceCalc()
    {
        $this->model->add($this->newInterval);
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