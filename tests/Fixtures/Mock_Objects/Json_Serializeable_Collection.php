<?php

declare(strict_types=1);
/**
 * Collection mock using the JsonSerializable trait.
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Fixtures\Mock_Objects;

use JsonSerializable;
use PinkCrab\Core\Collection\Collection;
use PinkCrab\Core\Collection\Traits\JsonSerialize;

class Json_Serializeable_Collection extends Collection implements JsonSerializable {
	use JsonSerialize;
}
