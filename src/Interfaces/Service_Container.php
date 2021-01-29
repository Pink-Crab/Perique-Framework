<?php

declare(strict_types=1);
/**
 * Interface for custom ServiceContainer
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Interfaces;

use Psr\Container\ContainerInterface;

interface Service_Container extends ContainerInterface {

	/**
	 * Binds an object to the constainer
	 *
	 * @param string $id
	 * @param object $service
	 * @return void
	 */
	public function set( $id, $service ): void;
}
