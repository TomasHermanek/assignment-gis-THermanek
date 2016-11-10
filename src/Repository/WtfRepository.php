<?php

namespace Repository;

class WtfRepository {
    public function getSqlOfAllWtfPoints2() {
        return "SELECT pt.name, ST_AsGeoJSON(pt.way) FROM planet_osm_point pt, planet_osm_polygon p
                WHERE ST_Contains(p.way, pt.way) and p.name = 'Karlova Ves' and pt.shop = 'supermarket'";
    }

    public function getSqlOfAllWtfPoints() {
        return "
        with recursive bar(i, name, way, distance) as (
        (select 0, name, way, cast(0 as double precision) 
        from planet_osm_point 
        where name = 'KC Dunaj')
        union all
        (select i+1,t2.name, t2.way, ST_Distance(t1.way, t2.way) as distance
        from bar t1
        cross join planet_osm_point t2
        where 
        t2.amenity = 'bar' and i<10
        order by distance
        limit 10)
        )
        select name, ST_AsGeoJSON(way) from bar";
    }
}