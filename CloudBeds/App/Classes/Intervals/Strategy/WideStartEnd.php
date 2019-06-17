<?php

namespace App\Classes\Intervals\Strategy;

use App\Classes\Intervals\Interfaces\StrategyInterface;

/**
 * readyInterval START-END range is wide and include already existing ranges (even few)
 *
 * Class WideStartEnd
 * @package App\Classes\Intervals\Strategy
 */
class WideStartEnd extends Strategy implements StrategyInterface
{
    public function doCalc()
    {
        $this->attachInterval(self::DELETE_ACTION, self::NEW_INTERVAL, $this->newInterval);
        $this->attachInterval($this->readyInterval->getAction(), self::READY_INTERVAL, $this->readyInterval);

        return $this;
    }
}