<?php

namespace App\Classes\Intervals\Strategy;


use App\Classes\Intervals\StrategyPriceInterface;
use Core\Model;

/**
 * if start dates identical then
 * adding new interval and
 * updating existing interval start date with the new interval end date
 *
 * Class InnerStartIdentical
 * @package App\Classes\Intervals\Strategy
 */
class InnerStartIdentical implements StrategyPriceInterface
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
        $this->model->add($this->newInterval);

        $updStartDate = $this->newInterval->getEndDate()->add(new \DateInterval('P1D'));
        $this->dbInterval->setStartDate($updStartDate);
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