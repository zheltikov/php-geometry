<?php

namespace Zheltikov\Geometry;

use InvalidArgumentException;

use function Zheltikov\TypeAssert\is_;

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
     * @param array|Point $point
     * @return Point
     * @throws \Zheltikov\Exceptions\InvariantException
     */
    public static function from($point): Point
    {
        if ($point instanceof Point) {
            return $point;
        }

        if (is_($point, "shape('x' => num, 'y' => num)")) {
            return new self($point['x'], $point['y']);
        }

        if (is_($point, "tuple(num, num)")) {
            return new self($point[0], $point[1]);
        }

        throw new InvalidArgumentException('Invalid argument supplied for Point::from()');
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
