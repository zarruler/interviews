<?php
namespace Core\Interfaces;

use Core\Database\IntervalValue;

interface ModelInterface
{
    public function getAll();
    public function getOne(int $id);
    public function add(IntervalValue $newInterval);
    public function edit(IntervalValue $newInterval);
}