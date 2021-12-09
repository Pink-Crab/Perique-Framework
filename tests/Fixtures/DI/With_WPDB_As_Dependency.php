<?php

declare(strict_types=1);
/**
 * Class which has WPDB as a dependency.
 *
 * @since 1.0.7
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\DI;

class With_WPDB_As_Dependency {

	/** @var \wpdb */
	public $wpdb;

	public function __construct( \wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

}
