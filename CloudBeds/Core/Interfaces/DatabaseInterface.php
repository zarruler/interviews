<?php

namespace Core\Interfaces;

interface DatabaseInterface
{
    public function connect();
    public function getConnection();
}