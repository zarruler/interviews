<?php

namespace App\Classes\Commands;
use App\Interfaces\{CommandInterface, WorkerInterface};

/**
 * command class to start process by means of appropriate worker
 * Class TurnOnCommand
 * @package App\Classes\Commands
 */
class TurnOnCommand implements CommandInterface
{
    protected $worker;

    /**
     * TurnOnCommand constructor.
     * @param WorkerInterface $worker
     */
    public function __construct(WorkerInterface $worker)
    {
        $this->worker = $worker;
    }

    /**
     * executing worker
     */
    public function execute() : void
    {
        $this->worker->doWork();
    }
}