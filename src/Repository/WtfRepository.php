<?php

namespace Repository;

class WtfRepository {
    public function getSqlOfAllWtfPoints() {
        return "SELECT pt.name, ST_AsGeoJSON(pt.way) FROM planet_osm_point pt, planet_osm_polygon p
                WHERE ST_Contains(p.way, pt.way) and p.name = 'Karlova Ves' and pt.shop = 'supermarket'";
    }
}