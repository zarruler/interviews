<?php

namespace App\Classes\Workers;
use App\Interfaces\WorkerInterface;

/**
 * worker class which do loop work
 * Class WorkerLoop
 * @package App\Classes\Workers
 */
class WorkerLoop implements WorkerInterface
{
    private $name,
            $repeat,
            $sleep;

    public function __construct(string $name, int $repeat = 5, int $sleep = 60)
    {
        $this->name = $name;
        $this->repeat = $repeat;
        $this->sleep = $sleep;
    }

    /**
     * doing some loop work. registering process only when the work started
     * @return bool
     */
    public function doWork() : bool
    {
        $this->announce();
        for ($i=1; $i<=$this->repeat; $i++) {
            sleep($this->sleep);
        }
        return true;
    }

    /**
     * receiving worker name
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * assigning current process some title
     * @return bool
     */
    private function announce() : bool
    {
        cli_set_process_title($this->name);
        return true;
    }

}