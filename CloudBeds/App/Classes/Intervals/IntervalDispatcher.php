<?php

namespace App\Classes\Intervals;

use App\Classes\Intervals\Interfaces\IntervalActionsInterface;
use App\Classes\Intervals\Interfaces\IntervalTypeInterface;
use App\Classes\Intervals\Strategy\BetweenStartEnd;
use App\Classes\Intervals\Strategy\InnerEndIdentical;
use App\Classes\Intervals\Strategy\InnerStartEndIdentical;
use App\Classes\Intervals\Strategy\InnerStartIdentical;
use App\Classes\Intervals\Strategy\OuterEndNearStart;
use App\Classes\Intervals\Strategy\OuterEndStartIntersect;
use App\Classes\Intervals\Strategy\OuterStartEndIntersect;
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
        $this->readyIntervals[$newInterval->getUid()] = $newInterval;
    }

    /**
     *
     * @param IntervalPriceInterface $newInterval interval from the database
     */
    public function addInterval(IntervalPriceInterface $newInterval)
    {
        $breakFlag = 0;
        // we need to insert into $this->readyIntervals, to avoid infinite loop we copy it
        $loop = $this->readyIntervals;
        foreach ($loop as $id => $readyInterval)
        {
            if ($readyInterval->getAction() == self::DELETE_ACTION)
                continue;

            $intervals = $this->chooseStrategy($readyInterval, $newInterval);

            if(empty($intervals))
                continue;

            foreach($intervals as $interval)
            {
                if (key($interval) == self::READY_INTERVAL) {
                    // updating ready interval after the strategy calculations
                    $this->readyIntervals[$id] = $interval[self::READY_INTERVAL];
                }
                elseif (key($interval) == self::NEW_INTERVAL)
                {
                    // adding to the readyIntervals list newly added interval after the strategy calculations
                    // newInterval after the strategy assigned to the same $newInterval variable
                    // so on the next iteration in comparison with other readyIntervals will be used
                    // NOT initial $newInterval from the method signature BUT modified newInterval after the strategy
                    $newInterval = $interval[self::NEW_INTERVAL];

                    // if first time then will be added, if second or more time after few comparisons then will be changed
                    // to modified itself
                    $this->readyIntervals[$newInterval->getUid()] = $newInterval;


                    // if newInterval after the strategy got DELETE_ACTION no need to continue comparison
                    // with the rest of ready intervals
                    if ($newInterval->getAction() == self::DELETE_ACTION)
                        $breakFlag = 1;
                }
            }

            if ($breakFlag)
                break;
        }
    }

    public function chooseStrategy(IntervalPriceInterface $readyInterval, IntervalPriceInterface $newInterval)
    {
        $intervals = []; // if empty returned then appropriate strategy wasn't found
        $oneDayInterval = new \DateInterval('P1D');

        $readyStart = clone $readyInterval->getStartDate();
        $readyEnd = clone $readyInterval->getEndDate();
        $newStart = clone $newInterval->getStartDate();
        $newEnd = clone $newInterval->getEndDate();

        if ($readyStart <= $newStart && $readyEnd >= $newEnd)
        { // #6 readyInterval START-END range is wide and include already existing ranges ($newInterval)
            $alg = new WideStartEnd($readyInterval, $newInterval);
            $intervals = $alg->doCalc()->getIntervals();
        }
        elseif ($readyStart < $newStart && $readyEnd >= $newStart && $readyEnd <= $newEnd)
        { // #4 readyInterval END intersect or equal existing START
            $alg = new OuterEndStartIntersect($readyInterval, $newInterval);
            $intervals = $alg->doCalc()->getIntervals();
        }
        elseif ($readyStart->sub($oneDayInterval) == $newEnd)
        { // #3 readyInterval START JOINS existing END
            $alg = new OuterStartNearEnd($readyInterval, $newInterval);
            $intervals = $alg->doCalc()->getIntervals();
        }
        elseif ($readyEnd > $newEnd && $readyStart <= $newEnd && $readyStart >= $newStart)
        { // #2 readyInterval START intersect or equal existing END
            $alg = new OuterStartEndIntersect($readyInterval, $newInterval);
            $intervals = $alg->doCalc()->getIntervals();
        }
        elseif ($readyEnd->add($oneDayInterval) == $newStart)
        { // #5 readyInterval END JOINS existing START
            $alg = new OuterEndNearStart($readyInterval, $newInterval);
            $intervals = $alg->doCalc()->getIntervals();
        }
        elseif ($readyStart == $newStart && $readyEnd == $newEnd)
        { // #1 INNER: readyInterval and existingInterval start and end dates are identical
            $alg = new InnerStartEndIdentical($readyInterval, $newInterval);
            $intervals = $alg->doCalc()->getIntervals();
        }
        elseif ($readyStart == $newStart && $readyEnd < $newEnd)
        { // #1 INNER: readyInterval and existingInterval START identical but END dates different
            $alg = new InnerStartIdentical($readyInterval, $newInterval);
            $intervals = $alg->doCalc()->getIntervals();
        }
        elseif ($readyEnd = $newEnd && $readyStart > $newEnd)
        { // #1 INNER: readyInterval and existingInterval END identical but START dates different
            $alg = new InnerEndIdentical($readyInterval, $newInterval);
            $intervals = $alg->doCalc()->getIntervals();
        }
        elseif ($readyStart > $newStart && $readyEnd < $newEnd)
        { // #1 INNER: readyInterval somewhere between start and end of the newInterval
            $alg = new BetweenStartEnd($readyInterval, $newInterval);
            $intervals = $alg->doCalc()->getIntervals();
        }

        return $intervals;
    }

    public function getIntervals() : array
    {

        return $this->readyIntervals;
    }
}