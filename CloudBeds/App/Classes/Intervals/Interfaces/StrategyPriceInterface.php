<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 6/16/19
 * Time: 12:24 AM
 */

namespace App\Classes\Intervals\Interfaces;


interface StrategyPriceInterface extends StrategyInterface
{
    public function samePriceCalc();
    public function diffPriceCalc();
}