<?php

namespace Database;

class PostgressDb implements DatabaseInterface{
    private $dbHost;
    private $dbPort;
    private $dbName;
    private $dbUsername;
    private $dbPassword;
    private $conn = null;

    public function __construct($dbHost, $dbPort, $dbName, $dbUsername, $dbPassword) {
        $this->dbHost = $dbHost;
        $this->dbPort = $dbPort;
        $this->dbName = $dbName;
        $this->dbUsername = $dbUsername;
        $this->dbPassword = $dbPassword;
    }

    public function connect() {
        if (!$this->conn) {
            $connectionString = sprintf("host=%s port=%s dbname=%s user=%s password=%s",
                $this->dbHost, $this->dbPort, $this->dbName, $this->dbUsername, $this->dbPassword);
            $this->conn = \pg_connect($connectionString);
            if (!$this->conn)
                throw new \Exception('Database connection failed');
        }
    }

    public function query($sql) {
        if (!$this->conn)
            throw new \Exception("Database is disconected", 500);
        return \pg_query($this->conn, $sql);
    }

    public function disconnect() {
        if ($this->conn) {
            \pg_close($this->conn);
            $this->conn = null;
        }
    }
}