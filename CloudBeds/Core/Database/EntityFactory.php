<?php
namespace Core\Database;


class EntityFactory
{
     public function __construct(ModelRecord $recordValue, array $data)
     {

         foreach($data as $fieldName => $fieldValue)
         {
             $setter = $this->getSetterByFieldName($fieldName);
             $recordValue->$setter($this->checkDate((string) $fieldValue));
         }

         return $recordValue;
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
}