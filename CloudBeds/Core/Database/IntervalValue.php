<?php
/*
 * should be created according to the table structure and columns type but in our case manually :)
 */

namespace Core\Database;

use Core\Exceptions\InvalidDateTimeFormat;

class IntervalValue extends ModelRecord
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var \DateTime
     */
    public $start_date;

    /**
     * @var \DateTime
     */
    public $end_date;

    /**
     * @var float
     */
    public $price;

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