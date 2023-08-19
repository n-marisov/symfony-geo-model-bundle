<?php

namespace Maris\Symfony\Geo\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Maris\Interfaces\Geo\Calculator\AreaCalculatorInterface;
use Maris\Interfaces\Geo\Calculator\DistanceCalculatorInterface;
use Maris\Interfaces\Geo\Determinant\IntersectionDeterminantInterface;
use Maris\Interfaces\Geo\Iterator\LocationsIteratorInterface;
use Maris\Interfaces\Geo\Model\GeometryInterface;
use Maris\Interfaces\Geo\Model\PolygonInterface;
use Maris\Symfony\Geo\Model\Iterator\LocationsIterator;


/**
 * Замкнутая линия определяющая фигуру на карте.
 */
class Polygon extends PointList implements PolygonInterface
{
    /**
     * Внутренние кольца полигона.
     * @var Collection<Polygon>
     */
    private Collection $internal;


    /***
     * Внешнее кольца полигона
     * если полигон является внутренним кольцом.
     * @var Polygon|null
     */
    private ?Polygon $parent;

    /**
     * @param Collection $coordinates
     * @param Collection $internal
     * @param Polygon|null $parent
     */
    public function __construct(Collection $coordinates = new ArrayCollection(), Collection $internal = new ArrayCollection(), ?Polygon $parent = null)
    {
        parent::__construct( $coordinates );
        $internal->map(fn ( Polygon $polygon ) => $polygon->parent = $this );
        $this->internal = $internal;
        $this->parent = $parent;
    }

    /**
     * @return void
     */
    public function __clone(): void
    {
        parent::__clone();
        $this->parent = null;
    }

    /**
     * @return Polygon|null
     */
    public function getParent(): ?Polygon
    {
        return $this->parent ?? null;
    }


    public function jsonSerialize(): array
    {
        /**
         * Формируем внешнее кольцо
         */
        $coordinates = [ $this->coordinates->toArray() ];

        /**
         * Формируем все внутренние кольца.
         */
        /**@var Polygon $internal **/
        foreach ($this->internal as $internal)
            $coordinates[] = $internal->coordinates->toArray();

        return [
            "type" => "Polygon",
            "bbox" => $this->getBounds(),
            "coordinates" => $coordinates
        ];
    }

    public function count(): int
    {
        return $this->coordinates->count();
    }

    public function getPerimeter(DistanceCalculatorInterface $calculator): float
    {
        $distance = 0.0;

        for ( $i=0, $j = 1; $this->coordinates->offsetExists($j); $i= $j, $j++)
            $distance += $calculator->calculateDistance( $this->coordinates->get($i), $this->coordinates->get($j) );

        $distance += $calculator->calculateDistance(
            $this->coordinates->first(),
            $this->coordinates->last()
        );

        return $distance;
    }

    public function getArea(AreaCalculatorInterface $calculator, bool $isFull): float
    {
        return $calculator->calculateArea(...$this->coordinates);
    }

    public function contains( GeometryInterface $geometry ): bool
    {
        return $this->coordinates->contains( $geometry );
    }

    public function intersects(IntersectionDeterminantInterface $determinant, GeometryInterface $geometry): bool
    {
        return $determinant->intersects($this,$geometry);
    }

    public function getIterator(): LocationsIteratorInterface
    {
        return new LocationsIterator( $this->coordinates );
    }
}