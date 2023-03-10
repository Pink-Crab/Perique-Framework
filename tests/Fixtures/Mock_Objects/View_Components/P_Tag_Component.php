<?php

declare(strict_types=1);

/**
 * P Component model for templating.
 *
 * @since 1.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components;

use PinkCrab\Perique\Services\View\Component\Component;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\Span;

class P_Tag_Component extends Component {

	public $class;

	private $span;

	public function __construct( string $class, Span $span ) {
		$this->class = $class;
		$this->span  = $span;
	}
}
