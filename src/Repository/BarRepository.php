<?php

namespace Repository;

/**
 * Class BarRepository
 * @package Repository
 */
class BarRepository {
    /**
     * @param $lng
     * @param $lat
     * @return string
     */
    public function getBarPathSql($lng, $lat) {
        return "
        WITH RECURSIVE BAR(name, way, distance, bars) as (
            (SELECT name, way, cast(0 as double precision), array[osm_id] 
            FROM planet_osm_point 
            WHERE 
              amenity IN ('bar', 'pub', 'restaurant')
            ORDER BY ST_DISTANCE(way, ST_GeogFromText('SRID=4326;POINTM($lat $lng 1336176082171)'))
            LIMIT 1
            )
        UNION ALL
            (SELECT t2.name, t2.way, ST_DISTANCE(t1.way, t2.way) distance, t1.bars || t2.osm_id
            FROM bar t1
            CROSS JOIN planet_osm_point t2
            WHERE 
              t2.amenity IN ('bar', 'pub', 'restaurant') AND
            NOT t2.osm_id = any(t1.bars) 
            ORDER BY distance
            LIMIT 1 )
        )
        SELECT name, ST_AsGeoJSON(way) FROM bar LIMIT 10";
    }

    /**
     * @param $barName
     * @return string
     */
    public function getSqlBarParkingCoordinates($barName) {
        return "
        SELECT t1.name, ST_AsGeoJSON(t1.way) pub_way, ST_AsGeoJSON(t2.way) parking_way
        FROM
        (
            SELECT pb.osm_id id, MIN(ST_DISTANCE(pb.way, pk.way)) distnce
            FROM planet_osm_point pb, planet_osm_point pk
            WHERE
              pk.amenity = 'parking' and
              pb.amenity IN ('bar', 'pub', 'restaurant') and
              LOWER(pb.name) LIKE LOWER('$barName%')
            GROUP BY
              pb.osm_id
        ) as min 
        JOIN planet_osm_point t1 ON min.id = t1.osm_id 
        CROSS JOIN planet_osm_point t2
        WHERE 
           min.distnce = ST_DISTANCE(t1.way, t2.way)
        ";
    }

    /**
     * @return string
     */
    public function getSqlPopulation() {
        return "
            select DISTINCT(vl.name) village, ST_AsGeoJSON(ar.way), (CAST(vl.population AS bigint) / count(pb.amenity)) as diversity 
                from planet_osm_point vl
                cross join planet_osm_polygon ar
                cross join planet_osm_point pb
                where
                  vl.place = 'village' and
                  vl.population is not null and
                  vl.name = ar.name and
                  pb.amenity = 'pub' and
                  ST_CONTAINS(ar.way, pb.way)
                GROUP BY
                  village, ar.way, vl.population
                limit 500
            ";
    }
}