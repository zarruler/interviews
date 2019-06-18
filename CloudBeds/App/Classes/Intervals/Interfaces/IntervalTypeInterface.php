<?php

namespace App\Classes\Intervals\Interfaces;


interface IntervalTypeInterface
{
    const READY_INTERVAL = 1;
    const NEW_INTERVAL = 2;
    const AVAILABLE_INTERVALS = [self::READY_INTERVAL, self::NEW_INTERVAL];

}