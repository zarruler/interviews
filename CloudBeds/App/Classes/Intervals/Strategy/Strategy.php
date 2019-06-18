<?php

namespace App\Classes\Intervals\Strategy;


use App\Classes\Intervals\Interfaces\IntervalActionsInterface;
use App\Classes\Intervals\Interfaces\IntervalTypeInterface;
use Core\Interfaces\IntervalPriceInterface;

class Strategy implements IntervalActionsInterface, IntervalTypeInterface
{
    protected $newInterval;
    protected $readyInterval;

    private $intervalActions;

    public function __construct(IntervalPriceInterface $readyInterval, IntervalPriceInterface $newInterval)
    {
        $this->readyInterval = clone $readyInterval;
        $this->newInterval = clone $newInterval;
    }

    protected function attachInterval(int $action, int $intervalType, IntervalPriceInterface $interval)
    {
        if (!in_array($action, self::AVAILABLE_ACTIONS))
            throw new \Exception('Wrong Action in '. self::class);
        if (!in_array($intervalType, self::AVAILABLE_INTERVALS))
            throw new \Exception('Wrong Interval type in '. self::class);

        $interval->setAction($action);
        //as a result of algorithm could be many new intervals
        $this->intervalActions[][$intervalType] = $interval;
    }

    public function getIntervals()
    {
        return $this->intervalActions;
    }

}