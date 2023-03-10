<?php

declare(strict_types=1);

/**
 * Input Component model for templating.
 *
 * @since 1.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components;

use PinkCrab\Perique\Services\View\Component\Component;

class Input extends Component {

	public $name;

	private $id;

	protected $value;

	private $type;


	public function __construct( string $name, string $id, string $value, string $type = 'text' ) {
		$this->name  = $name;
		$this->id    = $id;
		$this->value = $value;
		$this->type  = $type;
	}
}
