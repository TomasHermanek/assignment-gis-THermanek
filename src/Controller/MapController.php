<?php

namespace Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class MapController
 * @package Controller
 */
class MapController {
    /**
     * Shows map main
     */
    public function showMapAction() {
        $content =  file_get_contents("public/html/map.html");

        $response = new Response();
        $response->setContent($content);
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');

        $response->send();
    }
}