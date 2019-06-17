<?php

namespace App\Classes\Intervals\Strategy;


use App\Classes\Intervals\StrategyPriceInterface;
use Core\Interfaces\IntervalPriceInterface;
use Core\Model;

/**
 * NEW START intersect or equal existing END
 *
 * Class OuterStartEndIntersect
 * @package App\Classes\Intervals\Strategy
 */
class OuterStartEndIntersect implements StrategyPriceInterface
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
        // intervals joined. update existing interval end with the end from the new interval
        // remaining existing interval start
        $this->dbInterval->setEndDate($this->newInterval->getEndDate());
        $this->model->edit($this->dbInterval);

    }

    public function diffPriceCalc()
    {
        // if prices different, then inserting new interval and updating existing END with the NEW_start-1
        $this->model->add($this->newInterval);

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