<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 6/15/19
 * Time: 6:01 PM
 */

namespace App\Classes\Intervals\Strategy;


use App\Classes\Intervals\StrategyPriceInterface;
use Core\Interfaces\IntervalPriceInterface;
use Core\Model;

/**
 * checking if both start and end dates are identical then
 * update existing interval with the new data from the new interval
 *
 * Class InnerStartEndIdentical
 * @package App\Classes\Intervals\Strategy
 */
class InnerStartEndIdentical implements StrategyPriceInterface
{
    private $model;
    private $dbInterval;
    private $newInterval;

    public function __construct(Model $model, IntervalPriceInterface $dbInterval, IntervalPriceInterface $newInterval)
    {
        $this->model = $model;
        $this->dbInterval = $dbInterval;
        $this->newInterval = $newInterval;
    }

    public function doCalc()
    {
        if ($this->dbInterval->getPrice() == $this->newInterval->getPrice()) {
            $this->samePriceCalc();
        } else {
            $this->diffPriceCalc();
        }
    }

    public function samePriceCalc()
    {

    }

    public function diffPriceCalc()
    {
        $this->dbInterval->setPrice($this->newInterval->getPrice());
        $this->model->edit($this->dbInterval);
    }
    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return IntervalPriceInterface
     */
    public function getDbInterval(): IntervalPriceInterface
    {
        return $this->dbInterval;
    }

    /**
     * @return IntervalPriceInterface
     */
    public function getNewInterval(): IntervalPriceInterface
    {
        return $this->newInterval;
    }
}