<?php

namespace Controller;

use Symfony\Component\HttpFoundation\Response;

class MapController {
    public function showMapAction() {
        echo file_get_contents("public/html/map.html");

        $response = new Response();
        $response->setContent('<html><body><h1>Hello world!</h1></body></html>');
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');

        $response->send();
    }
}