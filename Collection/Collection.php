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

class Collection {


	/**
	 * Datasource.
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Creates an instance of the collection with predefined data.
	 *
	 * @param array $data
	 */
	public function __construct( array $data = array() ) {
		$this->data = $data;
	}

	/**
	 * Static contrcutor
	 *
	 * @param mixed ...$data
	 * @return self
	 */
	public static function from( array $data = array() ): self {
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
	public function filter( callable $function ): self {
		return new static( array_filter( $this->data, $function ) );
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
	 * @param Collection|array $data
	 * @return self
	 */
	public function merge( $data ): self {
		if ( ! is_array( $data ) && ! is_a( $data, Collection::class ) ) {
			throw new \TypeError( 'Can only merge with other Collections or Arrays.' );
		}
		return new static(
			array_merge(
				$this->data,
				is_a( $data, Collection::class ) ? $data->to_array() : $data
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
		foreach ( $data as $value ) {
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
	 * @param mixed $item
	 * @return self
	 */
	public function unshift( $item ): self {
		array_unshift( $this->data, $item );
		return $this;
	}

	/**
	 * Returns the current contents as an array.
	 *
	 * @return array
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

	// @TODO SORT, SLICE, COUNT, CLEAR, DIF, UDIFF, COPY



}
