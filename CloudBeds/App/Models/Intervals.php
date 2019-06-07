<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 6/6/19
 * Time: 3:32 AM
 */

namespace App\Models;
use Core\Interfaces\ModelInterface;
use PDO;
use Core\Model;

class Intervals extends Model implements ModelInterface
{
    public function getAll()
    {
        $q = $this->db->query('SELECT * FROM intervals');

        return $q->fetchAll(PDO::FETCH_ASSOC);
    }
}