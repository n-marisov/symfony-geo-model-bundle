<?php

namespace Maris\Symfony\Geo\Model\Entity;

use Maris\Interfaces\Geo\Model\GeometryInterface;

/**
 * Определяет любую фигуру на карте
 */
abstract class Geometry implements GeometryInterface
{
    /**
     * ID в базе данных.
     * @var int|null
     */
    protected ?int $id = null;

    /**
     * Возвращает ID записи в БД.
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /***
     * Сбрасываем ID при клонировании.
     * @return void
     */
    public function __clone(): void
    {
        $this->id = null;
    }
}