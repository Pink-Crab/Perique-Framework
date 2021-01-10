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
	public function set( $id, object $service ): void;
}
