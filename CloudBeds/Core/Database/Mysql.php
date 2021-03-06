<?php

namespace Core\Database;

use PDO;
use Core\Interfaces\DatabaseInterface;
use Psr\Container\ContainerInterface;

class Mysql implements DatabaseInterface
{
    public $container;

    protected $connection;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        if (!$this->connection)
            $this->connect();

    }

    public function connect()
    {
        $dsn = 'mysql:host=' . $this->container->get('db.host') .
                    ';dbname=' . $this->container->get('db.name') .
                    ';charset=' . $this->container->get('db.charset');
        $this->connection = new PDO($dsn, $this->container->get('db.user'), $this->container->get('db.password'));

        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $this;
    }


    public function getConnection()
    {
        return $this->connection;
    }

}