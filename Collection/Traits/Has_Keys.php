<?php

declare(strict_types=1);
/**
 *
 * Adds in a selection of methods for using a collection with keys.
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
 * @package PinkCrab\Core\Collection
 */

namespace PinkCrab\Core\Collection;

use OutOfRangeException;

trait Has_Keys {

	/**
	 * Gets a value from the passed index
	 *
	 * @param int $index
	 * @return mixed
	 * @throws OutOfRangeException
	 */
	public function get( int $index ) {
		if ( ! array_key_exists( $index, $this->data ) ) {
			throw new \OutOfRangeException();
		}

		return $this->data[ $index ];
	}

	/**
	 * Inserts a single or multiple items to an index
	 *
	 * @param integer $index
	 * @param mixed ...$values
	 * @return void
	 * @throws OutOfRangeException
	 */
	public function insert( int $index, ...$values ): self {
		if ( ! array_key_exists( $index, $this->data ) && $index !== count( $this ) ) {
			throw new \OutOfRangeException();
		}

		array_splice( $this->data, $index, 0, $values );
		return $this;
	}

	/**
	 * Sets a value at a defined inded
	 *
	 * @param integer $index
	 * @param mixed $value
	 * @return self
	 */
	public function set( int $index, $value ): self {
		$this->array[ $index ] = $value;
		return $this;
	}

	/**
	 * Searches for a simple value
	 * Uses array_search()
	 *
	 * @param mixed $value
	 * @return int|false
	 */
	public function find( $value ) {
		return array_search( $value, $this->data, true );
	}

	/**
	 * Removes an item based on its index.
	 *
	 * @param int $index
	 * @return void
	 * @throws OutOfRangeException if index doesnt exist.
	 */
	public function remove( int $index ) {
		if ( ! array_key_exists( $index, $this->data ) ) {
			throw new OutOfRangeException( sprintf( '%d index doesnt exist in %s', $index, get_class() ) );
		}

		// Get the value, unset and return the value.
		$value = $this->data[ $index ];
		unset( $this->data[ $index ] );
		return $value;
	}
}
