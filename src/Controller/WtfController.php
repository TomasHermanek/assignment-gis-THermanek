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

        $i = 0;
        while ($row = \pg_fetch_array($wtfPoints)) {
            $result[$i]['geometry'] = json_decode($row['st_asgeojson'], true);

            $result[$i]['properties'] = array();
            $result[$i]['properties']['title'] = $row['name'];
            $result[$i]['properties']['icon'] = 'fast-food';
            $i++;
        }

        $response = new JsonResponse();
        $response->prepare($request);
        $response->setCallback('handleResponse');
        $response->setContent(json_encode($result));

        $response->setStatusCode(JsonResponse::HTTP_OK);
        $response->send();
    }
}