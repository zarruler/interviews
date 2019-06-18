<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 6/15/19
 * Time: 6:01 PM
 */

namespace App\Classes\Intervals\Strategy;


use App\Classes\Intervals\Interfaces\StrategyPriceInterface;

/**
 * checking if both start and end dates are identical then
 * update existing interval with the new data from the new interval
 *
 * Class InnerStartEndIdentical
 * @package App\Classes\Intervals\Strategy
 */
class InnerStartEndIdentical extends Strategy implements StrategyPriceInterface
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
        // no changes
        // registering newInterval and mark it to delete
        // readyInterval stays as is without changes
        $this->attachInterval(self::DELETE_ACTION, self::NEW_INTERVAL, $this->newInterval);
        $this->attachInterval($this->readyInterval->getAction(), self::READY_INTERVAL, $this->readyInterval);

    }

    public function diffPriceCalc()
    {
        // newInterval delete
        // readyInterval has priority over existing so keep it price and it stays without changes
        $this->attachInterval(self::DELETE_ACTION, self::NEW_INTERVAL, $this->newInterval);
        $this->attachInterval($this->readyInterval->getAction(), self::READY_INTERVAL, $this->readyInterval);
    }

}