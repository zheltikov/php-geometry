<?php

namespace Zheltikov\Geometry;

/**
 *
 */
class Point
{
    /**
     * @var float
     */
    private float $x;

    /**
     * @var float
     */
    private float $y;

    /**
     * @param float $x
     * @param float $y
     */
    public function __construct(
        float $x = 0,
        float $y = 0
    ) {
        $this->y = $y;
        $this->x = $x;
    }

    /**
     * @return float
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * @return float
     */
    public function getY(): float
    {
        return $this->y;
    }

    /**
     * @param float $x
     * @return $this
     */
    public function setX(float $x): self
    {
        $this->x = $x;
        return $this;
    }

    /**
     * @param float $y
     * @return $this
     */
    public function setY(float $y): self
    {
        $this->y = $y;
        return $this;
    }
}
