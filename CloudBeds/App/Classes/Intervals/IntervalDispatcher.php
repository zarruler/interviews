<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 6/16/19
 * Time: 12:58 PM
 */

namespace App\Classes\Intervals;


use App\Classes\Intervals\Interfaces\IntervalActionsInterface;
use App\Classes\Intervals\Interfaces\IntervalTypeInterface;
use App\Classes\Intervals\Strategy\OuterEndStartIntersect;
use App\Classes\Intervals\Strategy\OuterStartNearEnd;
use App\Classes\Intervals\Strategy\WideStartEnd;
use Core\Interfaces\IntervalPriceInterface;

/**
 * ready intervals - based on calculations of the user input new interval (from constructor)
 * newly incoming intervals - are intersected intervals from the database
 *
 * ready intervals have higher priority over newly incoming intervals
 *
 * Class IntervalDispatcher
 * @package App\Classes\Intervals
 */
class IntervalDispatcher implements IntervalActionsInterface, IntervalTypeInterface
{
    /**
     * @var array
     */
    private $readyIntervals = [];

    public function __construct(IntervalPriceInterface $newInterval)
    {
        $newInterval->setAction(self::INSERT_ACTION);
        $this->readyIntervals[0] = $newInterval; // doesnt have ID so it is always under '0' index
    }

    /**
     *
     *
     * @param IntervalPriceInterface $newInterval interval from the database
     */
    public function addInterval(IntervalPriceInterface $newInterval)
    {
        // we need to insert into $this->readyIntervals, to avoid infinite loop we copy it
        $loop = $this->readyIntervals;
//        var_dump(count($loop));
        foreach ($loop as $id => $readyInterval)
        {
            if ($readyInterval->getAction() == self::DELETE_ACTION)
                continue;

            $intervals = $this->chooseStrategy($readyInterval, $newInterval);
            if(empty($intervals))
                continue;

            // updating ready interval after the strategy calculations
            $this->readyIntervals[$id] = $intervals[self::READY_INTERVAL];

            // adding to the readyIntervals list newly added interval after the strategy calculations
            // newInterval after the strategy assigned to the same $newInterval variable
            // so on the next iteration in comparison with other readyIntervals will be used
            // NOT initial $newInterval from the method signature BUT modified newInterval after the strategy
            $newInterval = $intervals[self::NEW_INTERVAL];

            // if first time then will be added, if second or more time after few comparisons then will be changed
            // to modified itself
            $this->readyIntervals[$newInterval->getId()] = $newInterval;

            // if newInterval after the strategy got DELETE_ACTION no need to continue comparison
            // with the rest of ready intervals
            if ($newInterval->getAction() == self::DELETE_ACTION)
                break;

        }
//        echo 'after';
//        var_dump(count($loop));
//        var_dump($this->readyIntervals);
    }

    public function chooseStrategy(IntervalPriceInterface $readyInterval, IntervalPriceInterface $newInterval)
    {
        $intervals = []; // if empty returned then appropriate strategy wasnt found
        $oneDayInterval = new \DateInterval('P1D');

        $readyStart = clone $readyInterval->getStartDate();
        $readyEnd = clone $readyInterval->getEndDate();
        $newStart = clone $newInterval->getStartDate();
        $newEnd = clone $newInterval->getEndDate();

// TODO: RECREATE THE REST OF STRATEGIES TO THE NEW WAY
        if ($readyStart <= $newStart && $readyEnd >= $newEnd)
        { // #6 $readyInterval START-END range is wide and include already existing ranges ($newInterval)
            $alg = new WideStartEnd($readyInterval, $newInterval);
            $intervals = $alg->doCalc()->getIntervals();
        }
        elseif ($readyEnd >= $newStart && $readyEnd <= $newEnd)
        { // #4 $readyInterval END intersect or equal existing START
            $alg = new OuterEndStartIntersect($readyInterval, $newInterval);
            $intervals = $alg->doCalc()->getIntervals();
        }
        elseif ($readyStart->sub($oneDayInterval) <= $newEnd)
        { // #3 $readyInterval START JOINS existing END
            $alg = new OuterStartNearEnd($readyInterval, $newInterval);
            $intervals = $alg->doCalc()->getIntervals();
        }

        return $intervals;
    }

    public function getIntervals() : array
    {
        return $this->readyIntervals;
    }
}