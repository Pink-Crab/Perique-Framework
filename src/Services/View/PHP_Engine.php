<?php declare(strict_types=1);
/**
 * Basic PHP engine for using the Renderable interface.
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
 * @package PinkCrab\Core\View
 */

namespace PinkCrab\Core\Services\View;

use Exception;
use PinkCrab\Core\Interfaces\Renderable;

class PHP_Engine implements Renderable {


	/**
	 * Renders a template with data.
	 *
	 * @param string $file_path
	 * @param iterable<string, mixed> $data
	 * @return void|string
	 * @throws Exception If invalid filepath.
	 */
	public function render( string $file_path, iterable $data, bool $print = true ) {
		// Check file exists.
		if ( ! file_exists( $file_path ) ) {
			throw new Exception( sprintf( 'View %s not found', $file_path ) );
		}

		if ( $print ) {
			print( $this->render_buffer( $file_path, $data ) );
			return;
		} else {
			return $this->render_buffer( $file_path, $data );
		}
	}

	/**
	 * Builds the view.
	 *
	 * @param string $file_path
	 * @param iterable<string, mixed> $data
	 * @return string
	 */
	private function render_buffer( string $file_path, iterable $data ): string {
		$output = '';
		ob_start();

		// Set all the data values a parameters.
		foreach ( $data as $key => $value ) {
			if ( is_string( $key ) ) {
				${esc_html( $key )} = $value;
			}
		}

		include $file_path;
		$output = ob_get_contents();
		ob_end_clean();
		return $output ?: '';
	}
}
