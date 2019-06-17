<?php

namespace App\Classes\Intervals\Strategy;


use App\Classes\Intervals\StrategyPriceInterface;
use Core\Interfaces\IntervalPriceInterface;
use Core\Model;

/**
 * if END dates identical then
 * adding new interval and
 * updating existing interval END date with the new interval start date
 *
 * Class InnerEndIdentical
 * @package App\Classes\Intervals\Strategy
 */
class InnerEndIdentical implements StrategyPriceInterface
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