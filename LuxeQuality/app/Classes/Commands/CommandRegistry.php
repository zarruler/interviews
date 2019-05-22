<?php

namespace App\Classes\Commands;
use App\Interfaces\CommandInterface;

class CommandRegistry
{
    private $registry = [];

    /**
     * adding command to registry
     *
     * @param CommandInterface $command
     * @param $type
     */
    public function add(CommandInterface $command, $type) : void
    {
        $this->registry[$type] = $command;
    }

    /**
     * receiving command from registry
     *
     * @param $type
     * @return mixed
     * @throws \Exception
     */
    public function get($type) : CommandInterface
    {
        if (!isset($this->registry[$type])) {
            throw new \RuntimeException(__('errors', 'command_absent') .' '. $type);
        }
        return $this->registry[$type];
    }

}