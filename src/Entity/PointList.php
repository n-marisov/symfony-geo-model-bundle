<?php

namespace Maris\Symfony\Geo\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Maris\Interfaces\Geo\Factory\FeatureFactoryInterface;
use Maris\Interfaces\Geo\Model\BoundsInterface;
use Maris\Interfaces\Geo\Model\FeatureInterface;
use Maris\Symfony\Geo\Embeddable\Model\Entity\Bounds;
use RuntimeException;

/***
 * Произвольная коллекция точек.
 */
class PointList extends Geometry
{

    /***
     * Внешнее кольцо полигона
     * @var Collection
     */
    protected Collection $coordinates;

    /**
     * Объект границ в которые входят все точки коллекции.
     * @var BoundsInterface
     */
    protected BoundsInterface $bounds;

    /**
     * @param Collection $coordinates
     */
    public function __construct( Collection $coordinates = new ArrayCollection() )
    {
        if( !$coordinates->forAll( fn( int $key, object $value ) => $value::class == Point::class ) )
            throw new RuntimeException("В объектах ".static::class." могут хранится только объекты ".Point::class.".");

        $this->coordinates = $coordinates;
        $this->bounds = $this->calculateBounds();
    }

    protected function calculateBounds():BoundsInterface
    {
        $latMin = 90.0;
        $latMax = -90.0;
        $lngMin = 180.0;
        $lngMax = -180.0;

        foreach ($this->coordinates as $point) {
            $latMin = min($point->getLatitude(), $latMin);
            $lngMin = min($point->getLongitude(), $lngMin);
            $latMax = max($point->getLatitude(), $latMax);
            $lngMax = max($point->getLongitude(), $lngMax);
        }

        return new Bounds( $latMax, $lngMin, $latMin, $lngMax );
    }

    public function getBounds(): BoundsInterface
    {
        return $this->bounds;
    }

    public function toArray(): array
    {
        return $this->coordinates->toArray();
    }

    public function toFeature(FeatureFactoryInterface $factory): FeatureInterface
    {
        return $factory->fromGeometry( $this );
    }

    public function jsonSerialize(): array
    {
        return [
            "type" => "MultiPoint",
            "coordinates" => $this->toArray()
        ];
    }
}