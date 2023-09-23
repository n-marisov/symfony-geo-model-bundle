<?php

namespace Maris\Symfony\Geo\Model\Entity;



use Maris\Interfaces\Geo\Aggregate\LocationAggregateInterface;
use Maris\Interfaces\Geo\Factory\FeatureFactoryInterface;
use Maris\Interfaces\Geo\Model\BoundsInterface;
use Maris\Interfaces\Geo\Model\FeatureInterface;
use Maris\Interfaces\Geo\Model\LocationInterface;
use Maris\Symfony\Geo\Embeddable\Model\Entity\Location;

/**
 * Точка на карте.
 * Определяется значениями координат в градусах.
 */
class Point extends Geometry implements LocationAggregateInterface
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

    public function getBounds(): BoundsInterface
    {
        return $this->location->getBounds();
    }

    public function toArray(): array
    {
        return $this->location->toArray();
    }

    public function toFeature(FeatureFactoryInterface $factory): FeatureInterface
    {
        return $this->location->toFeature( $factory );
    }

    public function jsonSerialize(): array
    {
        return $this->location->jsonSerialize();
    }

    public function getGeometry(): ?LocationInterface
    {
        return $this->location;
    }
}