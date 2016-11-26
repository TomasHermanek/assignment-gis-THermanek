<?php

namespace Controller;

use Database\DatabaseInterface;
use Repository\WtfRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxController {
    private $wtfRepository;
    private $database;
    private $userLng;
    private $userLat;

    public function __construct(DatabaseInterface $database, WtfRepository $wtfRepository) {
        $this->wtfRepository = $wtfRepository;
        $this->database = $database;
    }

    public function loadCoordinates(Request $request) {
        $this->userLat = $request->get('lat');
        $this->userLng = $request->get('lng');

        if ($this->userLng == null or $this->userLng == null)
            return false;
        return true;
    }

    public function getAllWtfPointsAction(Request $request) {
        $result = array();

        if (!$this->loadCoordinates($request)) {
            $response = new JsonResponse();
            $response->setStatusCode(JsonResponse::HTTP_NO_CONTENT);
            $response->send();
        }
        else {
            $wtfPoints = $this->database->query($this->wtfRepository->getSqlOfAllWtfPoints($this->userLat, $this->userLng));
            $i = 0;
            while ($row = \pg_fetch_array($wtfPoints)) {
                $result[$i]['geometry'] = json_decode($row['st_asgeojson'], true);

                $result[$i]['properties'] = array();
                $result[$i]['properties']['title'] = $i." ".$row['name'];
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

    public function finBarParking(Request $request) {
        $result = array();

        $barName = $request->get('barName');

        $sql = $this->wtfRepository->getSqlBarParkingCoordinates($barName);
        $wtfPoints = $this->database->query($sql);

        $i = 0;

        while ($row = \pg_fetch_array($wtfPoints)) {
            $result[$i]['geometry'] = json_decode($row['pub_way'], true);
            $result[$i]['properties'] = array();
            $result[$i]['properties']['title'] = $row['name'];
            $result[$i]['properties']['icon'] = 'bar';
            $i++;
            $result[$i]['geometry'] = json_decode($row['parking_way'], true);
            $result[$i]['properties'] = array();
            $result[$i]['properties']['title'] = 'parking place';
            $result[$i]['properties']['icon'] = 'car';
            $i++;
        }

        $response = new JsonResponse();
        $response->prepare($request);
        $response->setCallback('handleResponse');
        $response->setContent(json_encode($result));

        $response->setStatusCode(JsonResponse::HTTP_OK);
        $response->send();
    }

    /**
     * Diversity 0 .. 50, 51 ... 100, 101 ... 200, 201 ... 500, 501+        // ToDO change diversity into variable(array)
     *
     * @param Request $request
     */
    public function findBarPopulation(Request $request) {
        $result = array();

        $result['extraLow'] = array();
        $result['low'] = array();
        $result['medium'] = array();
        $result['high'] = array();
        $result['extraHigh'] = array();

        $sql = $this->wtfRepository->getSqlPopulation();

        $wtfPoints = $this->database->query($sql);

        $i = 0;
        while ($row = \pg_fetch_array($wtfPoints)) {
            $diversity = $row['diversity'];
            if ($diversity <= 50)
                $targetArray = 'extraLow';
            elseif ($diversity <= 100)
                $targetArray = 'low';
            elseif ($diversity <= 200)
                $targetArray = 'medium';
            elseif ($diversity <= 500)
                $targetArray = 'high';
            else
                $targetArray = 'extraHigh';

            $geoJsonObject = array();
            $geoJsonObject['geometry'] = json_decode($row['st_asgeojson'], true);
            $geoJsonObject['properties'] = array();
            $geoJsonObject['properties']['name'] = $row['village'].": ".$diversity. " people per bar";
            $geoJsonObject['properties']['title'] = $row['village'];
            $geoJsonObject['properties']['icon'] = 'fast-food';

            $result[$targetArray][] = $geoJsonObject;
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