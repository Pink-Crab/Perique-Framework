<?php

declare(strict_types=1);

/**
 * Span Component model for templating.
 *
 * @since 1.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components;

use PinkCrab\Perique\Services\View\Component\Component;

class Span extends Component {

	public $class;

	private $contents;

	// constructor
	public function __construct( string $class, string $contents ) {
		$this->class    = $class;
		$this->contents = $contents;
	}
}
