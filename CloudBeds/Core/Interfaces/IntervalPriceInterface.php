<?php

namespace Core\Interfaces;


interface IntervalPriceInterface extends IntervalInterface
{
    public function getPrice();
    public function setPrice(float $price);
}