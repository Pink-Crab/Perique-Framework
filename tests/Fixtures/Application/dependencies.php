<?php

use PinkCrab\Core\Tests\Fixtures\DI\Interface_A;
use PinkCrab\Core\Tests\Fixtures\DI\Dependency_E;

/**
 * Stub file for testing Dice Dependencies.
 */

return array(
	// Silence
    Interface_A::class => array(
		'instanceOf' => Dependency_E::class
	),
);