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

namespace PinkCrab\Core\Collection;


trait Sequence {

	/**
	 * Reverses the order of the squence.
	 *
	 * @return self
	 */
	public function reverse(): self {
		return new static( array_reverse( $this->data ) );
	}

	/**
	 * Roates the sequence based on count
	 *
	 * @param int $count (if negaive roate counterclockwise, else clockwise).
	 * @return self
	 */
	public function rotate( int $count ): self {

		if ( $count > 0 ) {
			for ( $i = 0; $i < $count; $i++ ) {
				$this->push( $this->shift() );
			}
		} elseif ( $count < 0 ) {
			for ( $i = 0; $i < ( -1 * $count ); $i++ ) {
				$this->unshift( $this->pop() );
			}
		}

		return $this;
	}

	/**
	 * Returns a sum of all the elements.
	 *
	 * @return void
	 */
	public function sum() {
		return array_sum(
			array_map(
				static function( $e ) {
					return (int) $e;
				},
				$this->data
			)
		);
	}
}
