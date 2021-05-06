<?php

declare(strict_types=1);
/**
 * Base Collection.
 *
 * Can be extended and used with supplied traits.
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

use Countable;
use TypeError;
use UnderflowException;

class Collection implements Countable {


	/**
	 * Datasource.
	 *
	 * @var array<int|string, mixed>
	 */
	protected $data = array();

	/**
	 * Creates an instance of the collection with predefined data.
	 *
	 * @param array<int|string, mixed> $data
	 */
	final public function __construct( array $data = array() ) {
		$this->data = $this->map_construct( $data );
	}

	/**
	 * Overwrite this method in any extended classes, to modify the inital data.
	 *
	 * @param array<int|string, mixed> $data
	 * @return array<int|string, mixed>
	 */
	protected function map_construct( array $data ): array {
		return $data;
	}

	/**
	 * Static contrcutor
	 *
	 * @param array<int|string, mixed> $data
	 * @return static
	 */
	public static function from( array $data = array() ) {
		return new static( $data );
	}


	/**
	 * Apply a function to the collection of items.
	 *
	 * @param callable $function
	 * @return self
	 */
	public function apply( callable $function ): self {
		foreach ( $this->data as &$element ) {
			$element = $function( $element );
		}
		return $this;
	}

	/**
	 * Apply a function to the collection of items.
	 *
	 * @param callable $function
	 * @return self
	 */
	public function each( callable $function ): self {
		foreach ( $this->data as $key => $value ) {
			$function( $value, $key );
		}
		return $this;
	}

	/**
	 * Apply a filter function to the contents.
	 *
	 * @param callable $function
	 * @return self
	 */
	public function filter( callable $function, int $mode = 0 ): self {
		return new static( array_filter( $this->data, $function, $mode ) );
	}

	/**
	 * Apply a map function to the contents.
	 *
	 * @param callable $function
	 * @return self
	 */
	public function map( callable $function ): self {
		return new static( array_map( $function, $this->data ) );
	}

	/**
	 * Apply a function to reduce the contents of the internal array.
	 *
	 * @param callable $function
	 * @param string $inital
	 * @return mixed
	 */
	public function reduce( callable $function, $inital = '' ) {
		return array_reduce(
			$this->data,
			function( $carry, $value ) use ( $function ) {
				return $function( $carry, $value );
			},
			$inital
		);
	}

	/**
	 * Merges with another array or collection
	 *
	 * @param Collection|array<int|string, mixed> $data
	 * @return self
	 * @throws TypeError If not an arrya or Collection.
	 */
	public function merge( $data ): self {
		if ( ! is_array( $data ) && ! is_a( $data, static::class ) ) {
			throw new TypeError( 'Can only merge with other Collections or Arrays.' );
		}
		return new static(
			array_merge(
				$this->data,
				is_object( $data ) && is_a( $data, static::class ) ? $data->to_array() : $data
			)
		);
	}

	/**
	 * Pushes an item to the collection and reutrns a new instance.
	 *
	 * @param mixed ...$data
	 * @return self
	 */
	public function push( ...$data ): self {
		foreach ( $this->map_construct( $data ) as $value ) {
			$this->data[] = $value;
		}
		return $this;
	}

	/**
	 * Returns the last value from the collection
	 *
	 * @throws UnderflowException If emtpy.
	 * @return mixed
	 */
	public function pop() {
		if ( empty( $this->data ) ) {
			throw new \UnderflowException( 'Collection is empty, can not extract value.', 1 );
		}
		return \array_pop( $this->data );
	}

	/**
	 * Returns the last value from the collection
	 *
	 * @throws UnderflowException If emtpy.
	 * @return mixed
	 */
	public function shift() {
		if ( empty( $this->data ) ) {
			throw new \UnderflowException( 'Collection is empty, can not extract value.', 1 );
		}
		return \array_shift( $this->data );
	}

	/**
	 * Adds an item to the head of the array
	 *
	 * @param mixed ...$items
	 * @return self
	 */
	public function unshift( ...$items ): self {
		foreach ( $this->map_construct( $items ) as $value ) {
			array_unshift( $this->data, $value );
		}
		return $this;
	}

	/**
	 * Returns the current contents as an array.
	 *
	 * @return array<int|string, mixed>
	 */
	public function to_array(): array {
		return $this->data;
	}

	/**
	 * Checks if empty.
	 *
	 * @return bool
	 */
	public function is_empty(): bool {
		return empty( $this->data );
	}

	/**
	 * Checks if the array contains all of the values.
	 *
	 * @param mixed ...$values
	 * @return bool
	 */
	public function contains( ...$values ): bool {
		foreach ( $values as $value ) {
			if ( ! in_array( $value, $this->data, true ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Joins the collection as a string.
	 *
	 * @param string $glue
	 * @return string
	 */
	public function join( string $glue = '' ): string {
		return join( $glue, $this->data );
	}

	/**
	 * Returns the count of the collection.
	 *
	 * @return int
	 */
	public function count(): int {
		return count( $this->data );
	}

	/**
	 * Clears the contents of the collection
	 * Returns the same instance.
	 *
	 * @return self
	 */
	public function clear(): self {
		$this->data = array();
		return $this;
	}

	/**
	 * Create a copy of the existing collection.
	 *
	 * @return self
	 */
	public function copy(): self {
		return new static( $this->data );
	}

	/**
	 * Sorts the existing collection and returns the current instance.
	 *
	 * @param callable|null $function
	 * @return self
	 */
	public function sort( ?callable $function = null ): self {
		if ( $function ) {
			usort( $this->data, $function );
		} else {
			natsort( $this->data );
		}

		return $this;
	}

	/**
	 * Sorts a new instance of the collection.
	 *
	 * @param callable|null $function
	 * @return self
	 */
	public function sorted( ?callable $function = null ): self {
		return ( new static( $this->data ) )->sort( $function );
	}

	/**
	 * Slices a collection into a new sub collection.
	 *
	 * @param int $offset
	 * @param int|null $length
	 * @return self
	 */
	public function slice( int $offset, ?int $length = null ): self {
		return new static( array_slice( $this->data, $offset, $length ?? count( $this->data ) ) );
	}

	/**
	 * Returns a new collection of differences between another collection or array.
	 *
	 * @param array<int|string, mixed>|Collection $data
	 * @return self
	 * @throws TypeError
	 */
	public function diff( $data ):self {

		if ( ! is_array( $data ) && ! is_a( $data, static::class ) ) {
			throw new \TypeError( 'Can only merge with other Collections or Arrays.' );
		}

		return new static(
			array_diff(
				$this->data,
				is_object( $data ) && is_a( $data, static::class ) ? $data->to_array() : $data
			)
		);
	}

	/**
	 * Returns a collection of same values from another array or collection.
	 *
	 * @param array<int|string, mixed>|Collection $data
	 * @return self
	 */
	public function intersect( $data ):self {

		if ( ! is_array( $data ) && ! is_a( $data, static::class ) ) {
			throw new \TypeError( 'Can only merge with other Collections or Arrays.' );
		}

		return new static(
			array_intersect(
				$this->data,
				is_object( $data ) && is_a( $data, static::class ) ? $data->to_array() : $data
			)
		);
	}



}
