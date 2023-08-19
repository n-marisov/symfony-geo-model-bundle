<?php

namespace Maris\Symfony\Geo\Model\Entity;



use Maris\Interfaces\Geo\Aggregate\LocationAggregateInterface;
use Maris\Interfaces\Geo\Model\LocationInterface;
use Maris\Symfony\Geo\Embeddable\Model\Entity\Location;

/**
 * Точка на карте.
 * Определяется значениями координат в градусах.
 */
class Point implements LocationAggregateInterface
{
    private Location $location;

    /**
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct( float $latitude, float $longitude )
    {
        $this->location = new Location( $latitude, $longitude );
    }

    public function getLocation(): LocationInterface
    {
        return $this->location;
    }
}