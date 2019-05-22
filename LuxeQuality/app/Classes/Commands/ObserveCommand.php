<?php

namespace App\Classes\Commands;
use App\Interfaces\{CommandInterface, WorkerInterface};

/**
 * command class to observe executed process by means of appropriate worker
 * Class ObserveCommand
 * @package App\Classes\Commands
 */
class ObserveCommand implements CommandInterface
{
    protected $worker;

    /**
     * ObserveCommand constructor.
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