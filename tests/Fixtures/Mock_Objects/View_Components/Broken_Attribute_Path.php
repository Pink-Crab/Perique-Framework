<?php

declare(strict_types=1);

/**
 * Component with an template attribute
 *
 * @since 1.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components;

use PinkCrab\Perique\Services\View\Component\Component;

/**
 * @view 
 * @since 1.0.0
 */
class Broken_Attribute_Path extends Component {

	public $name;

	/**
     * @param string $name
     */
	public function __construct( string $name ) {
		$this->name = $name;
	}
}