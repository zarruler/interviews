<?php

namespace App\Classes\Intervals\Strategy;

use App\Classes\Intervals\Interfaces\StrategyPriceInterface;

/**
 * readyInterval START JOINS existing END
 *
 * Class OuterStartNearEnd
 * @package App\Classes\Intervals\Strategy
 */
class OuterStartNearEnd extends Strategy implements StrategyPriceInterface
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
        // expanding newInterval range.
        // update newInterval END with the readyInterval END
        // delete readyInterval
        $this->newInterval->setEndDate($this->readyInterval->getEndDate());

        $this->attachInterval(self::UPDATE_ACTION, self::NEW_INTERVAL, $this->newInterval);
        $this->attachInterval(self::DELETE_ACTION, self::READY_INTERVAL, $this->readyInterval);
    }

    public function diffPriceCalc()
    {
        // readyInterval keep as is
        // newInterval keep existing action or do nothing action(just register in dispatcher)
        $newIntervalAction = $this->newInterval->getAction();
        $newIntervalAction = ($newIntervalAction > 0) ? $newIntervalAction : self::NOTHING_ACTION;

        $this->attachInterval($newIntervalAction, self::NEW_INTERVAL, $this->newInterval);
        $this->attachInterval($this->readyInterval->getAction(), self::READY_INTERVAL, $this->readyInterval);

    }

}