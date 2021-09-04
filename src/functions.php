<?php

namespace Zheltikov\Geometry;

/**
 * @param \Zheltikov\Geometry\Point $a
 * @param \Zheltikov\Geometry\Point $b
 * @return float
 */
function euclidean_distance(Point $a, Point $b): float
{
    $x_delta = $a->getX() - $b->getX();
    $y_delta = $a->getY() - $b->getY();

    return sqrt(pow($x_delta, 2) + pow($y_delta, 2));
}
