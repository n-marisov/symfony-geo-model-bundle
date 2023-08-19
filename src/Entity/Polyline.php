<?php

namespace Maris\Symfony\Geo\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Maris\Interfaces\Geo\Aggregate\LocationAggregateInterface;
use Maris\Interfaces\Geo\Calculator\BearingCalculatorInterface;
use Maris\Interfaces\Geo\Calculator\DistanceCalculatorInterface;
use Maris\Interfaces\Geo\Determinant\IntersectionDeterminantInterface;
use Maris\Interfaces\Geo\Determinant\OrientationDeterminantInterface;
use Maris\Interfaces\Geo\Finder\IntermediateLocationFinderInterface;
use Maris\Interfaces\Geo\Finder\MidLocationFinderInterface;
use Maris\Interfaces\Geo\Iterator\LocationsIteratorInterface;
use Maris\Interfaces\Geo\Model\GeometryInterface;
use Maris\Interfaces\Geo\Model\LocationInterface;
use Maris\Interfaces\Geo\Model\PolylineInterface;
use Maris\Symfony\Geo\Model\Iterator\LocationsIterator;

/**
 * Ломаная линия на карте.
 */
class Polyline extends PointList implements PolylineInterface
{
    /**
     * @param Collection $coordinates
     */
    public function __construct( Collection $coordinates = new ArrayCollection() )
    {
        parent::__construct( $coordinates );
    }

    public function count(): int
    {
        return $this->coordinates->count();
    }

    public function getStart(): LocationInterface|LocationAggregateInterface|null
    {
        return $this->coordinates->first();
    }

    public function getEnd(): LocationInterface|LocationAggregateInterface|null
    {
        return $this->coordinates->last();
    }

    public function add(LocationInterface|LocationAggregateInterface $location): PolylineInterface
    {
        $this->coordinates->add( $location );
        return $this;
    }

    public function contains(LocationInterface|LocationAggregateInterface $location): bool
    {
        return $this->coordinates->contains( $location );
    }

    public function get(int $position): LocationInterface|LocationAggregateInterface|null
    {
        return $this->coordinates->get( $position );
    }

    public function remove(LocationInterface|int|LocationAggregateInterface $locationOrPosition): LocationInterface|LocationAggregateInterface|null
    {
        if(is_numeric($locationOrPosition))
            return $this->coordinates->remove( $locationOrPosition );

        if( $this->coordinates->removeElement($locationOrPosition) )
            return $locationOrPosition;

        return null;
    }

    public function addUnique(LocationInterface|LocationAggregateInterface $location): bool
    {
        if($this->contains($location))
            return false;
        $this->add( $location );
        return true;
    }

    public function isSection(): bool
    {
        return $this->count() <= 2;
    }

    public function getSections(): array
    {
        $sections = [];
        for ($i = 0, $j = 1; isset($this->coordinates[$j]); $i = $j, $j++ )
            $sections[] = (new static())->add($this->coordinates[$i])->add($this->coordinates[$j]);
        return $sections;
    }

    public function getLength(DistanceCalculatorInterface $calculator): float
    {
        if($this->isSection())
            return $calculator->calculateDistance( $this->getStart(), $this->getEnd() );

        $distance = 0.0;
        foreach ($this->getSections() as $section)
            $distance += $section->getLength( $calculator );
        return $distance;
    }

    public function getInitialBearing(BearingCalculatorInterface $calculator): float
    {
        return $calculator->calculateInitialBearing( $this->getStart(), $this->getEnd() );
    }

    public function getFinalBearing(BearingCalculatorInterface $calculator): float
    {
        return $calculator->calculateFinalBearing( $this->getStart(), $this->getEnd() );
    }

    public function getMidLocation(MidLocationFinderInterface $finder): LocationInterface|LocationAggregateInterface|null
    {
        return $finder->findMidLocation( $this->getStart(), $this->getEnd() );
    }

    public function getIntermediateLocation(IntermediateLocationFinderInterface $finder, float $percent): LocationInterface|LocationAggregateInterface|null
    {
        return $finder->findIntermediateLocation( $this->getStart(), $this->getEnd(), $percent );
    }

    public function getReverse(): PolylineInterface
    {
        return new static(new ArrayCollection(array_reverse($this->coordinates->toArray())));
    }

    public function getIterator():LocationsIteratorInterface
    {
        return new LocationsIterator( $this->coordinates );
    }

    public function intersects(IntersectionDeterminantInterface $determinant, GeometryInterface $geometry): bool
    {
        return $determinant->intersects( $this, $geometry );
    }

    public function getOrientation(OrientationDeterminantInterface $determinant, LocationInterface|LocationAggregateInterface $location): int
    {
        return $determinant->determineOrientation( $this, $location );
    }

    public function jsonSerialize(): array
    {
        return [
            "type" => "LineString",
            "coordinates" => $this->toArray()
        ];
    }
}