<?php

declare(strict_types=1);
/**
 * Interface for auto registering hooks.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Interfaces;

use PinkCrab\Core\Services\Registration\Loader;

interface Registerable {
	public function register( Loader $loader ): void;
}
