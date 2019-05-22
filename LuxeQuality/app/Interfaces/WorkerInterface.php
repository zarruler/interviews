<?php

namespace App\Interfaces;

interface WorkerInterface
{
    public function doWork();
    public function getName();
}