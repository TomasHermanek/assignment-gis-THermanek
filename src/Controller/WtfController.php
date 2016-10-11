<?php

namespace Controller;

use Database\DatabaseInterface;
use Repository\WtfRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WtfController {
    private $wtfRepository;
    private $database;

    public function __construct(DatabaseInterface $database, WtfRepository $wtfRepository) {
        $this->wtfRepository = $wtfRepository;
        $this->database = $database;
    }

    public function getAllWtfPointsAction(Request $request) {
        $wtfPoints = $this->database->query($this->wtfRepository->getSqlOfAllWtfPoints());
        $result = array();

        $row = \pg_fetch_array($wtfPoints);
        if ($row) {
            $result['coordinates'] = array();
            $rowArray = json_decode($row['st_asgeojson'], true);

            $result["type"] = $rowArray["type"];
            array_push($result['coordinates'], $rowArray['coordinates']);
        }

        while ($row = \pg_fetch_array($wtfPoints)) {
            $rowArray = json_decode($row['st_asgeojson'], true);
            array_push($result['coordinates'], $rowArray['coordinates']);
        }

        $response = new JsonResponse();
        $response->prepare($request);
        $response->setCallback('handleResponse');
        $response->setContent(json_encode($result));

        $response->setStatusCode(JsonResponse::HTTP_OK);
        $response->send();
    }
}