<?php

namespace App\GFS;

use Location\Polygon;
use Location\Coordinate;

class GFServiceManager
{
    public Polygon $geofence;

    public function __construct()
    {
        $this->geofence = new Polygon();
        $this->addGeofencePoints();
    }

    public function geofenceCoordinatesClusters(): array
    {
        return [
            [
                // Geofence #1
                [0.3155285327999627,  32.568200608343524],
                [0.31562929007953755, 32.56796816475977],
                [0.3157988815379863,  32.56803300953636],
                [0.31573503534219505, 32.56826445550816]
            ],
            // Geofence #2
            [
                [0.314782943114851,   32.56899688079217],
                [0.3147575811313584,  32.5691279129288],
                [0.3144485968071288,  32.56895326698913],
                [0.31443113247572085, 32.56909835746407]
            ],
        ];
    }

    public function addGeofencePoints()
    {
        $this->geofence->addPoint(new Coordinate(0.31562929007953755, 32.56796816475977));
        $this->geofence->addPoint(new Coordinate(0.3157988815379863, 32.56803300953636));
        $this->geofence->addPoint(new Coordinate(0.31573503534219505, 32.56826445550816));
    }

    /**
     * 
     */
    public function coordinateInGeofences(Coordinate $point)
    {
        $geofenceLookupResults = [];

        foreach ($this->geofenceCoordinatesClusters() as $geofenceCoordinatesCluster) {
            $geofencePolygon = new Polygon;
            // Adding the geofence coordinates 
            foreach ($geofenceCoordinatesCluster as $geofenceCoordinates) {
                // Create coordinate objects and then add them to the polygon
                [$lat, $lng] = $geofenceCoordinates;
                $geofencePolygon->addPoint($this->createCoordinate($lat, $lng));
            }

            $geofenceLookupResults[] = $geofencePolygon->contains($point);
        }

        return $geofenceLookupResults;
    }

    public function createCoordinate(float $lat, float $lng): Coordinate
    {
        return new Coordinate($lat, $lng);
    }

    public function coordinateInGeofence(Coordinate $point)
    {
        return $this->geofence->contains($point);
    }
}
