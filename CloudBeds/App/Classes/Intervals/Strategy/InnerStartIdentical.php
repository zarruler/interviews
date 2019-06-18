<?php

namespace App\Classes\Intervals\Strategy;


use App\Classes\Intervals\Interfaces\StrategyPriceInterface;

/**
 * start dates identical and different inner end dates
 *
 * Class InnerStartIdentical
 * @package App\Classes\Intervals\Strategy
 */
class InnerStartIdentical extends Strategy implements StrategyPriceInterface
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
        // readyInterval stays as is
        // newInterval (the database one) start date become ready interval end_date+1
        $updStartDate = clone $this->readyInterval->getEndDate();
        $updStartDate->add(new \DateInterval('P1D'));
        $this->newInterval->setStartDate($updStartDate);

        $this->attachInterval($this->readyInterval->getAction(), self::READY_INTERVAL, $this->readyInterval);
        $this->attachInterval(self::UPDATE_ACTION, self::NEW_INTERVAL, $this->newInterval);

    }
}