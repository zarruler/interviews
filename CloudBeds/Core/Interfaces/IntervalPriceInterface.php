<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 6/16/19
 * Time: 1:12 PM
 */

namespace Core\Interfaces;


interface IntervalPriceInterface extends IntervalInterface
{
    public function getPrice();
    public function setPrice(float $price);
}