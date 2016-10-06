<?php

namespace Controller;

use Database\DatabaseInterface;
use Repository\WtfRepository;

class WtfController {
    private $wtfRepository;
    private $database;

    public function __construct(DatabaseInterface $database, WtfRepository $wtfRepository) {
        $this->wtfRepository = $wtfRepository;
        $this->database = $database;
    }

    public function getAllWtfPointsAction() {
        $wtfPoints = $this->database->query($this->wtfRepository->getSqlOfAllWtfPoints());
        $result = array();
        while ($row = \pg_fetch_array($wtfPoints)) {
            array_push($result, $row);
        }
        return $result;
    }
}