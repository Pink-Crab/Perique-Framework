<?php

declare(strict_types=1);
/**
 *
 * Adds in a selection of methods for using a collection as a sequence.
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

namespace PinkCrab\Core\Collection\Traits;

use UnderflowException;


trait Sequence {

	/**
	 * Reverses the order of the squence.
	 *
	 * @return self
	 */
	public function reverse(): self {
		$this->data = array_reverse( $this->data );
		return $this;
	}

	/**
	 * Returns a new collection with the sequence reversed.
	 *
	 * @return self
	 */
	public function reversed(): self {
		return new static( array_reverse( $this->data ) );
	}

	/**
	 * Roates the sequence based on count
	 *
	 * @param int $step (if negaive roate counterclockwise, else clockwise).
	 * @return self
	 */
	public function rotate( int $step ): self {

		if ( $step > 0 ) {
			for ( $i = 0; $i < $step; $i++ ) {
				$this->push( $this->shift() );
			}
		} elseif ( $step < 0 ) {
			for ( $i = 0; $i < ( -1 * $step ); $i++ ) {
				$this->unshift( $this->pop() );
			}
		}

		return $this;
	}

	/**
	 * Returns the first value from the collection.
	 *
	 * @return mixed
	 * @throws UnderflowException if Collection is empty.
	 */
	public function first() {
		if ( empty( $this->data ) ) {
			throw new UnderflowException( 'Collection is empty, can not get first value' );
		}
		return array_values( $this->data )[0];
	}

		/**
	 * Returns the last value from the collection.
	 *
	 * @return mixed
	 * @throws UnderflowException if Collection is empty.
	 */
	public function last() {
		if ( empty( $this->data ) ) {
			throw new UnderflowException( 'Collection is empty, can not get last value' );
		}
		return array_values( $this->data )[ count( $this->data ) - 1 ];
	}

	/**
	 * Returns a sum of all the elements.
	 *
	 * @return int|float
	 */
	public function sum() {
		return array_sum( $this->data );
	}
}
