<?php

namespace App\Classes\Intervals\Interfaces;


interface StrategyPriceInterface extends StrategyInterface
{
    public function samePriceCalc();
    public function diffPriceCalc();
}