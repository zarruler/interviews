<?php
/*
 * should be created according to the table structure and columns type but in our case manually :)
 */

namespace Core\Database;


class IntervalValue extends ModelRecord
{
    const DEFAULT_DATE_FORMAT = 'Y-m-d';
    protected $dateFormat = self::DEFAULT_DATE_FORMAT;
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
     * @param string $format
     * @return IntervalValue
     */
    public function setDateFormat($format = self::DEFAULT_DATE_FORMAT) : IntervalValue
    {
        $this->dateFormat = $format;
        return $this;
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
     * @param string $format
     * @return string
     */
    public function getStartDate(string $format = self::DEFAULT_DATE_FORMAT): string
    {
        return $this->start_date->format($format);
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
     * @param string $format
     * @return string
     */
    public function getEndDate(string $format = self::DEFAULT_DATE_FORMAT): string
    {
        return $this->end_date->format($format);
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