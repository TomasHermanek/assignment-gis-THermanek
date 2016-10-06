<?php

namespace Database;

interface DatabaseInterface {
    public function __construct($dbHost, $dbPort, $dbName, $dbUsername, $dbPassword);
    public function connect();
    public function query($sql);
    public function disconnect();
}