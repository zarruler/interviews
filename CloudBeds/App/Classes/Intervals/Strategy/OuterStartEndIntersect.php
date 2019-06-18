<?php

namespace App\Classes\Intervals\Strategy;


use App\Classes\Intervals\Interfaces\StrategyPriceInterface;

/**
 * readyInterval START intersect or equal newInterval END
 *
 * Class OuterStartEndIntersect
 * @package App\Classes\Intervals\Strategy
 */
class OuterStartEndIntersect extends Strategy implements StrategyPriceInterface
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
        // update newInterval end with the end from the readyInterval
        // delete readyInterval as not needed
        $this->newInterval->setEndDate($this->readyInterval->getEndDate());

        $this->attachInterval(self::UPDATE_ACTION, self::NEW_INTERVAL, $this->newInterval);
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
}