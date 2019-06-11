<?php
/*
 *  it should be an ORM class to return such an object but for this task is enough and this "hand made" :)
 */

namespace Core\Database;


class ModelRecord
{
    const DEFAULT_DATE_FORMAT = 'Y-m-d';
    protected $dateFormat = self::DEFAULT_DATE_FORMAT;

    protected $modelFieldsList = array();

    public function __construct(array $data)
    {
        foreach($data as $fieldName => $fieldValue)
        {
            if(is_numeric($fieldName))
                throw new \Exception('Data key should be of string type');

            $setter = $this->getSetterByFieldName($fieldName);
            $this->$setter($this->checkDate((string) $fieldValue));
        }
    }

    /**
     * @param string $format
     * @return ModelRecord
     */
    public function setDateFormat($format = self::DEFAULT_DATE_FORMAT) : ModelRecord
    {
        $this->dateFormat = $format;
        return $this;
    }

    /**
     * returning array of Model fields.
     * this array is filled by
     * 1) __call for unknown getters/setters
     * 2) regular setters
     *
     * @return array
     */
    public function toArray() : array
    {
        return $this->modelFieldsList;
    }

    /**
     * @param string $fieldName
     * @return string
     */
    public function getSetterByFieldName(string $fieldName)
    {
        return 'set' . implode('', array_map('ucfirst', explode('_', $fieldName)));
    }

    /**
     * check if $value is DateTime in proper format
     * if yes return DateTime in other case return initial value
     *
     * @param string $value
     * @return mixed|\DateTime
     */
    public function checkDate($value)
    {
        $dateTime = \DateTime::createFromFormat($this->dateFormat, $value);
        $errors = \DateTime::getLastErrors();

        if ($dateTime && empty($errors['warning_count']))
            return $dateTime;

        return $value;
    }


    public function __call($methodName, $params = null)
    {
        $methodPrefix = substr($methodName, 0, 3);
        $name = strtolower(substr($methodName, 3));

        if($methodPrefix == 'set' && count($params) == 1)
        {
            $this->modelFieldsList[$name] = $params[0];
        }
        elseif($methodPrefix == 'get' && array_key_exists($name, $this->modelFieldsList))
        {
                return $this->modelFieldsList[$name];
        }
        else
        {
            throw new \Exception('Only getters and setters are supported');
        }
    }
}