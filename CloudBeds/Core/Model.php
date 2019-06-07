<?php

namespace Core;

use PDO;
use Psr\Container\ContainerInterface;

/**
 * Base model
 *
 */
class Model
{
    const DB_NAMESPACE = '\\Core\\Database\\';
    const MODEL_NAMESPACE = '\\App\\Models\\';
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var PDO
     */
    protected $db;


    public function __construct(ContainerInterface $container)
    {
        $dbType = ucfirst($container->get('db.type'));
        $class = self::DB_NAMESPACE . $dbType;

        try {
            $this->db = $container->get($class)->getConnection();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

/*
    public function __construct()
    {
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    public function getConnection()
    {
        $dbType = ucfirst($this->container->get('db.type'));

        $class = self::DB_NAMESPACE . $dbType;

        try {
            $this->db = $this->container->get($class)->getConnection();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $this->db;
    }
*/
}
