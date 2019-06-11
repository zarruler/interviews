<?php
/*
 *  it should be an ORM class to return such an object but for this task is enough and this "hand made" :)
 */

namespace Core\Database;


class ModelRecord
{
    protected $modelFieldsList = array();

    public function __construct(array $data)
    {
//var_dump($data);
        foreach($data as $fieldName => $fieldValue)
        {
            if(is_numeric($fieldName))
                throw new \Exception('Data key should be of string type');

            $setter = $this->getSetterByFieldName($fieldName);
            $this->$setter($this->checkDate((string) $fieldValue));
        }
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
        $dateTime = \DateTime::createFromFormat('Y-m-d', $value);
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