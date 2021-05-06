<?php

declare(strict_types=1);
/**
 * Typed collection mock
 *
 * @since 0.5.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Fixtures\Mock_Objects;

use PinkCrab\Core\Collection\Collection;
use PinkCrab\Core\Collection\Traits\Indexed;

class Sample_Class_Collection extends Collection {

	/**
	 * Ensure only instances of Sample_Class are populated.
	 *
	 * @param array<int|string, mixed> $data
	 * @return array<int|string, Sample_Class>
	 */
	protected function map_construct( array $data ): array {
		return array_filter(
			$data,
			function( $class ): bool {
				return is_a( $class, Sample_Class::class, false );
			}
		);
	}
}
