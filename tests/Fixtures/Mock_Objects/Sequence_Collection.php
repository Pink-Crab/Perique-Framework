<?php

declare(strict_types=1);
/**
 * Collection mock using the Sequence trait.
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Fixtures\Mock_Objects;

use PinkCrab\Core\Collection\Collection;
use PinkCrab\Core\Collection\Traits\Sequence;

class Sequence_Collection extends Collection {
	use Sequence;
}
