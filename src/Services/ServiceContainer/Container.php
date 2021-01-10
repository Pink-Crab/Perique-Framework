<?php

declare(strict_types=1);
/**
 * Handles the registration of all classes passed.
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
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core\Services
 */


namespace PinkCrab\Core\Services\ServiceContainer;

use Exception;
use PinkCrab\Core\Interfaces\Service_Container;
use PinkCrab\Core\Services\ServiceContainer\ServiceNotRegisteredException;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Container implements Service_Container {

	/**
	 * Holds all of the bound services.
	 *
	 * @var array
	 */
	protected $services = array();

	/**
	 * Gets an item from the defined servives.
	 *
	 * @throws ServiceNotRegisteredException
	 * @param string $id
	 * @return mixed
	 */
	public function get( $id ) {
		if ( ! $this->has( $id ) ) {
			throw new ServiceNotRegisteredException( "{$id} not defined in container", 1 );
		}
		return $this->services[ $id ];
	}

	/**
	 * Does key exist.
	 *
	 * @param string $id
	 * @return mixed
	 */
	public function has( $id ) {
		return array_key_exists( $id, $this->services );
	}

	/**
	 * Sets an item to the
	 *
	 * @param string $id
	 * @param object $service
	 * @return void
	 */
	public function set( $id, object $service ): void {
		if ( $this->has( $id ) ) {
			throw new Exception( "{$id} already defined in container" );
		}
		$this->services[ (string) $id ] = $service;
	}

}
