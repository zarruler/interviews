<?php

namespace App\Classes\Commands;

use App\Classes\Workers\WorkerCheckStatus;
use App\Classes\Workers\WorkerLoop;
use App\Interfaces\CommandInterface;

class CommandFactory
{
    private $commands = [
        'run'   => 'thread',
        'check' => 'MCP',
    ];

    /**
     * Factory method which execute command depending on received entry command
     *
     * @param $command
     * @param $workerName
     * @return ObserveCommand|TurnOnCommand
     * @throws \Exception
     */
    public function factory($command, $workerName) : CommandInterface
    {
        if (empty($command) || empty($workerName))
            throw new \RuntimeException(__('errors', 'incorrect_input'));

        if ($command == $this->commands['run']) {
            $worker = new WorkerLoop($workerName);
            return new TurnOnCommand($worker);
        }

        if ($command == $this->commands['check']) {
            $worker = new WorkerCheckStatus($workerName);
            return new ObserveCommand($worker);
        }

        throw new \RuntimeException(__('errors', 'command_absent') .' '. $command);
    }

    /**
     * return list of available commands
     * @return array
     */
    public function getCommands() : array
    {
        return array_values($this->commands);
    }
}