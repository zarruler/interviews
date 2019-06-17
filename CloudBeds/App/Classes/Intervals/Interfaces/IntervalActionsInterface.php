<?php
namespace App\Classes\Intervals\Interfaces;


interface IntervalActionsInterface
{
    const INSERT_ACTION = 1;
    const DELETE_ACTION = 2;
    const UPDATE_ACTION = 3;
    const NOTHING_ACTION = 4;

    const AVAILABLE_ACTIONS = [self::INSERT_ACTION, self::DELETE_ACTION, self::UPDATE_ACTION, self::NOTHING_ACTION];
}