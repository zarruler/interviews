<?php

namespace App\Classes\Intervals\Strategy;

use App\Classes\Intervals\Interfaces\StrategyPriceInterface;
use Core\Database\IntervalValue;
use Core\Database\ModelRecord;

/**
 * new interval somewhere between start and end of the existing interval
 * in this case we have 2 new intervals to add and one existing to modify
 *
 * Class BetweenStartEnd
 * @package App\Classes\Intervals\Strategy
 */
class BetweenStartEnd extends Strategy implements StrategyPriceInterface
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
        // keep readyInterval as is
        $this->attachInterval($this->readyInterval->getAction(), self::READY_INTERVAL, $this->readyInterval);

        // creating brand new interval with the newInterval START and readyInterval START_date-1 as END date
        $brandNewEndDate = clone $this->readyInterval->getStartDate();
        $brandNewEndDate->sub(new \DateInterval('P1D'));

        $brandNewInterval = new IntervalValue([
            'start_date' => $this->newInterval->getStartDate(ModelRecord::DEFAULT_DATE_FORMAT),
            'end_date' => $brandNewEndDate->format(ModelRecord::DEFAULT_DATE_FORMAT),
            'price' => $this->newInterval->getPrice()
        ]);
        $this->attachInterval(self::INSERT_ACTION, self::NEW_INTERVAL, $brandNewInterval);


        // updating newInterval START date with the readyInterval END_date+1
        $updStartDate = clone $this->readyInterval->getEndDate();
        $updStartDate->add(new \DateInterval('P1D'));
        $this->newInterval->setStartDate($updStartDate);

        $this->attachInterval(self::UPDATE_ACTION, self::NEW_INTERVAL, $this->newInterval);

    }
}