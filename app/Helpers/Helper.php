<?php

/**
 * If $num is below $at_least, returns $at_least instead of $num
 *
 * @param float $num
 * @param float $at_least
 *
 * @return float
 */
function num_at_least(float $num, float $at_least) : float {
	if ($num < $at_least) {
		return $at_least;
	}

	return $num;
}
