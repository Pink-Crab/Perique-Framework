<?php

declare(strict_types=1);
/**
 * Helper class for working with paths and urls for App_Config.
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
 * @package PinkCrab\Perique
 * @since 0.4.0
 */

namespace PinkCrab\Perique\Utils;

class App_Config_Path_Helper {

	/**
	 * Normalises a path.
	 *
	 * Removes all trailing slashes, either forward or back.
	 *
	 * @param string $path
	 * @return string
	 */
	public static function normalise_path( string $path ): string {
		return rtrim( $path, '/\\' );
	}

	/**
	 * Generate the assumed views paths from the base path.
	 *
	 * @param string $base_path
	 * @return string
	 */
	public static function assume_view_path( string $base_path ): string {
		// Remove any trailing slash.
		$base_path = self::normalise_path( $base_path );
		return $base_path . \DIRECTORY_SEPARATOR . 'views';
	}

	/**
	 * Generate the assumed base url from the base path.
	 *
	 * @param string $base_path
	 * @return string
	 */
	public static function assume_base_url( string $base_path ): string {
		// Remove any trailing slash.
		$base_path = self::normalise_path( $base_path );
		return plugins_url( basename( $base_path ) );
	}

	/**
	 * Generate the assumed assets url from both the base path and base view path.
	 *
	 * @param string $base_path
	 * @param string $view_path
	 * @return string
	 */
	public static function assume_view_url( string $base_path, string $view_path ): string {

		$base_path = self::normalise_path( $base_path );
		$view_path = self::normalise_path( $view_path );

		// Remove any trailing slash.
		$diff = ltrim( str_replace( $base_path, '', $view_path ), '/\\' );

		// Return the base url with the diff.
		return self::assume_base_url( $base_path ) . '/' . $diff;
	}
}
