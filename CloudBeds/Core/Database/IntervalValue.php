<?php
/*
 * should be created according to the table structure and columns type but in our case manually :)
 */

namespace Core\Database;

use App\Classes\Intervals\Interfaces\IntervalActionsInterface;
use Core\Exceptions\InvalidDateTimeFormat;
use Core\Interfaces\IntervalPriceInterface;

class IntervalValue extends ModelRecord implements IntervalPriceInterface, IntervalActionsInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $start_date;

    /**
     * @var \DateTime
     */
    private $end_date;

    /**
     * @var float
     */
    private $price;

    /**
     * no action until it is set
     * available action in IntervalActionsInterface
     * @var int
     */
    private $action = 0;

    /**
     * @return int
     */
    public function getAction(): int
    {
        return $this->action;
    }

    /**
     * @param int $action
     * @throws \Exception
     */
    public function setAction(int $action): void
    {
        if(!in_array($action, self::AVAILABLE_ACTIONS))
            throw new \Exception('Not available action received in ' . self::class);

        $this->action = $action;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return IntervalValue
     */
    public function setId(int $id) : IntervalValue
    {
        $this->modelFieldsList['id'] = $this->id = $id;
        return $this;
    }

    /**
     * @param string $toFormat
     * @return \DateTime|string
     * @throws InvalidDateTimeFormat
     */
    public function getStartDate(string $toFormat = '')
    {
        // a bit of trash coding :)
        if (!empty($toFormat) && !$formatted = $this->start_date->format($toFormat))
            throw new InvalidDateTimeFormat();

        return $formatted ?? $this->start_date;
    }

    /**
     * @param \DateTime $start_date
     * @return IntervalValue
     */
    public function setStartDate(\DateTime $start_date) : IntervalValue
    {
        $this->modelFieldsList['start_date'] = $start_date->format($this->dateFormat);
        $this->start_date = $start_date;
        return $this;
    }

    /**
     * @param string $toFormat
     * @return \DateTime|string
     * @throws InvalidDateTimeFormat
     */
    public function getEndDate(string $toFormat = '')
    {
        if (!empty($toFormat) && !$formatted = $this->end_date->format($toFormat))
            throw new InvalidDateTimeFormat();

        return $formatted ?? $this->end_date;
    }

    /**
     * @param \DateTime $end_date
     * @return IntervalValue
     */
    public function setEndDate(\DateTime $end_date) : IntervalValue
    {
        $this->modelFieldsList['end_date'] = $end_date->format($this->dateFormat);
        $this->end_date = $end_date;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return IntervalValue
     */
    public function setPrice(float $price) : IntervalValue
    {
        $this->modelFieldsList['price'] = $this->price = $price;

        return $this;
    }

}