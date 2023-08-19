<?php

namespace Maris\Symfony\Geo\Model\Iterator;

use Doctrine\Common\Collections\Collection;
use Maris\Interfaces\Geo\Iterator\LocationsIteratorInterface;
use Maris\Interfaces\Geo\Model\LocationInterface;

class LocationsIterator implements LocationsIteratorInterface
{

    private Collection $coordinates;

    private int $count;

    private int $position;

    /**
     * @param Collection $coordinates
     */
    public function __construct(Collection $coordinates)
    {
        $this->coordinates = $coordinates;
    }


    /**
     * @inheritDoc
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * @inheritDoc
     */
    public function key(): ?int
    {
        return $this->position ?? null;
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return $this->count < $this->position;
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->count = $this->coordinates->count();
        $this->position = 0;
    }

    public function current(): ?LocationInterface
    {
        return $this->coordinates->get($this->position);
    }
}