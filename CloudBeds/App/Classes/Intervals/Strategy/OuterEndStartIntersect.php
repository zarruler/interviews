<?php

namespace App\Classes\Intervals\Strategy;

use App\Classes\Intervals\Interfaces\StrategyPriceInterface;

/**
 * readyInterval END intersect or equal new START
 *
 * Class OuterEndStartIntersect
 * @package App\Classes\Intervals\Strategy
 */
class OuterEndStartIntersect extends Strategy implements StrategyPriceInterface
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
        // intervals joined.
        // update newInterval START with the START from the readyInterval remaining newInterval END
        // delete readyInterval as not needed
        $this->newInterval->setStartDate($this->readyInterval->getStartDate());

        $this->attachInterval(self::UPDATE_ACTION, self::NEW_INTERVAL, $this->newInterval);
        $this->attachInterval(self::DELETE_ACTION, self::READY_INTERVAL, $this->readyInterval);

        // alternative joining
        // expand readyInterval END to the END of the newInterval and keep update/insert Action as is
        // delete new interval
        /*
        $this->readyInterval->setEndDate($this->newInterval->getEndDate());

        $this->attachInterval($this->readyInterval->getAction(), self::READY_INTERVAL, $this->readyInterval);
        $this->attachInterval(self::DELETE_ACTION, self::NEW_INTERVAL, $this->newInterval);
        */
    }

    public function diffPriceCalc()
    {
        // keep readyInterval unchanged
        // update newInterval START date to the readyInterval END_date+1

        $updStartDate = clone $this->readyInterval->getEndDate();
        $updStartDate->add(new \DateInterval('P1D'));
        $this->newInterval->setStartDate($updStartDate);

        $this->attachInterval($this->readyInterval->getAction(), self::READY_INTERVAL, $this->readyInterval);
        $this->attachInterval(self::UPDATE_ACTION, self::NEW_INTERVAL, $this->newInterval);
    }
}