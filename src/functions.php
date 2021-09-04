<?php

namespace Zheltikov\Geometry;

use function Zheltikov\TypeAssert\as_;

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

/**
 * @param array $points
 * @return \Zheltikov\Geometry\Point[]|null
 * @throws \Zheltikov\Exceptions\InvariantException
 * @throws \Zheltikov\Exceptions\TypeAssertionException
 */
function convex_hull(array $points): ?array
{
    // Check the input values
    $type = sprintf(
        "
            array<
                shape('x' => num, 'y' => num)
                | tuple(num, num)
                | %s
            > & nonempty
        ",
        Point::class
    );
    as_($points, $type);

    foreach ($points as &$point) {
        $point = Point::from($point);
    }

    // A global point needed for  sorting points with reference
    // to  the first point Used in compare function of qsort()
    $p0 = null;

    // A utility function to find next to top in a stack
    $nextToTop = function (array &$S): Point {
        $p = end($S);
        array_pop($S);
        $res = end($S);
        $S[] = $p;
        return $res;
    };

    // A utility function to return square of distance
    // between p1 and p2
    $distSq = function (Point $p1, Point $p2): int {
        return ($p1->getX() - $p2->getX()) * ($p1->getX() - $p2->getX()) +
               ($p1->getY() - $p2->getY()) * ($p1->getY() - $p2->getY());
    };

    // To find orientation of ordered triplet (p, q, r).
    // The function returns following values
    // 0 --> p, q and r are collinear
    // 1 --> Clockwise
    // 2 --> Counterclockwise
    $orientation = function (Point $p, Point $q, Point $r): int {
        $val = ($q->getY() - $p->getY()) * ($r->getX() - $q->getX()) -
               ($q->getX() - $p->getX()) * ($r->getY() - $q->getY());
        $val = (int) $val;

        if ($val === 0) {
            return 0;
        }  // collinear
        return ($val > 0) ? 1 : 2; // clock or counterclock wise
    };

    // A function used by library function qsort() to sort an array of
    // points with respect to the first point
    $compare = function (Point $p1, Point $p2) use ($distSq, $orientation, &$p0): int {
        // Find orientation
        $o = $orientation($p0, $p1, $p2);
        if ($o === 0) {
            return ($distSq($p0, $p2) >= $distSq($p0, $p1)) ? -1 : 1;
        }

        return ($o === 2) ? -1 : 1;
    };

    $n = count($points);

    // Find the bottommost point
    $ymin = $points[0]->getY();
    $min = 0;
    for ($i = 1; $i < $n; $i++) {
        $y = $points[$i]->getY();

        // Pick the bottom-most or chose the left
        // most point in case of tie
        if (
            ($y < $ymin)
            || ($ymin === $y
                && $points[$i]->getX() < $points[$min]->getX())
        ) {
            $ymin = $points[$i]->getY();
            $min = $i;
        }
    }

    // Place the bottom-most point at first position
    [$points[0], $points[$min]] = [$points[$min], $points[0]];

    // Sort n-1 points with respect to the first point.
    // A point p1 comes before p2 in sorted output if p2
    // has larger polar angle (in counterclockwise
    // direction) than p1
    $p0 = $points[0];
    $to_sort = array_slice($points, 1, $n - 1);
    usort($to_sort, $compare);
    $points = [$p0, ...$to_sort];
    unset($to_sort);

    // If two or more points make same angle with p0,
    // Remove all but the one that is farthest from p0
    // Remember that, in above sorting, our criteria was
    // to keep the farthest point at the end when more than
    // one points have same angle.
    $m = 1; // Initialize size of modified array
    for ($i = 1; $i < $n; $i++) {
        // Keep removing i while angle of i and i+1 is same
        // with respect to p0
        while ($i < $n - 1
               && $orientation(
                      $p0,
                      $points[$i],
                      $points[$i + 1]
                  ) === 0) {
            $i++;
        }


        $points[$m] = $points[$i];
        $m++;  // Update size of modified array
    }

    // If modified array of points has less than 3 points,
    // convex hull is not possible
    if ($m < 3) {
        return null;
    }

    // Create an empty stack and push first three points
    // to it.
    /** @var \Zheltikov\Geometry\Point[] $S */
    $S = [];
    $S[] = $points[0];
    $S[] = $points[1];
    $S[] = $points[2];

    // Process remaining n-3 points
    for ($i = 3; $i < $m; $i++) {
        // Keep removing top while the angle formed by
        // points next-to-top, top, and points[i] makes
        // a non-left turn
        while (count($S) > 1 && $orientation($nextToTop($S), end($S), $points[$i]) !== 2) {
            array_pop($S);
        }
        $S[] = $points[$i];
    }

    /*// Now stack has the output points, print contents of stack
    while (!empty($S)) {
        $p = end($S);
        echo '(' . $p->getX() . ', ' . $p->getY() . ")\n";
        array_pop($S);
    }*/

    return $S;
}
