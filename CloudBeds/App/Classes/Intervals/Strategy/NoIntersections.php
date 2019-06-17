<?php

namespace App\Classes\Intervals\Strategy;


use App\Classes\Intervals\StrategyInterface;
use Core\Interfaces\IntervalPriceInterface;
use Core\Model;

/**
 * NEW START-END range is wide and include already existing ranges (even few)
 *
 * Class WideStartEnd
 * @package App\Classes\Intervals\Strategy
 */
class NoIntersections implements StrategyInterface
{
    private $model;
    private $newInterval;

    public function __construct(Model $model, IntervalPriceInterface $newInterval)
    {
        $this->model = $model;
        $this->newInterval = $newInterval;
    }

    public function doCalc()
    {
        // just adding new interval
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
    public function getNewInterval(): IntervalPriceInterface
    {
        return $this->newInterval;
    }
}