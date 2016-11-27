<?php

namespace Database;

/**
 * Interface DatabaseInterface
 * @package Database
 */
interface DatabaseInterface {
    /**
     * DatabaseInterface constructor.
     * @param $dbHost
     * @param $dbPort
     * @param $dbName
     * @param $dbUsername
     * @param $dbPassword
     */
    public function __construct($dbHost, $dbPort, $dbName, $dbUsername, $dbPassword);

    /**
     * @return mixed
     */
    public function connect();

    /**
     * @param $sql
     * @return mixed
     */
    public function query($sql);

    /**
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function queryParams($sql, array $params);

    /**
     * @return mixed
     */
    public function disconnect();
}