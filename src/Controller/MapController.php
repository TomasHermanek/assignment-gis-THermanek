<?php

namespace Controller;


class MapController {
    public function showMapAction() {
        echo file_get_contents("public/html/map.html");
    }
}