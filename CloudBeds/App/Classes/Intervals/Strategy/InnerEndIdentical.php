<?php

namespace App\Classes\Intervals\Strategy;

use App\Classes\Intervals\Interfaces\StrategyPriceInterface;

/**
 * END dates identical and different INNER START dates
 *
 * Class InnerEndIdentical
 * @package App\Classes\Intervals\Strategy
 */
class InnerEndIdentical extends Strategy implements StrategyPriceInterface
{
    public function doCalc()
    {
        if ($this->newInterval->getPrice() == $this->readyInterval->getPrice()) {
            $this->samePriceCalc();
        } else {
            $this->diffPriceCalc();
        }
        return $this;
    }

    public function samePriceCalc()
    {
        // no changes to the existing
        // registering newInterval and mark it to do nothing with it at this step
        // readyInterval delete - it is smaller than existing newInterval
        $this->attachInterval(self::NOTHING_ACTION, self::NEW_INTERVAL, $this->newInterval);
        $this->attachInterval(self::DELETE_ACTION, self::READY_INTERVAL, $this->readyInterval);
    }

    public function diffPriceCalc()
    {
        // keep readyInterval unchanged
        // update newInterval END date to the readyInterval START_date-1

        $updEndDate = clone $this->readyInterval->getStartDate();
        $updEndDate->sub(new \DateInterval('P1D'));
        $this->newInterval->setEndDate($updEndDate);

        $this->attachInterval($this->readyInterval->getAction(), self::READY_INTERVAL, $this->readyInterval);
        $this->attachInterval(self::UPDATE_ACTION, self::NEW_INTERVAL, $this->newInterval);

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