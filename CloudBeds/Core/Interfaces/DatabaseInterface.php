<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 6/6/19
 * Time: 2:33 AM
 */

namespace Core\Interfaces;

interface DatabaseInterface
{
    public function connect();
    public function getConnection();
}