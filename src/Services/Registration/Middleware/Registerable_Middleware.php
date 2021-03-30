<?php

declare(strict_types=1);

/**
 * Registration_Middleware for all classes that implement the Resiterable
 * interface.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core\Registration
 * @since 0.4.0
 */

namespace PinkCrab\Core\Services\Registration\Middleware;

use PinkCrab\Loader\Loader;
use PinkCrab\Core\Interfaces\DI_Container;
use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Core\Interfaces\Registration_Middleware;

class Registerable_Middleware implements Registration_Middleware {

	/** @var Loader */
	protected $loader;

	public function __construct( Loader $loader ) {
		$this->loader = $loader;
	}

	/**
	 * Process the passed class
	 *
	 * @param object $class
	 * @return object
	 */
	public function process( $class ) {
		if ( in_array( Registerable::class, class_implements( $class ) ?: array(), true ) ) {
			/** @phpstan-ignore-next-line class must implement register for interface*/
			$class->register( $this->loader );
		}
		return $class;
	}
}
