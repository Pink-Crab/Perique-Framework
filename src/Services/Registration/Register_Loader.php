<?php

declare(strict_types=1);

/**
 * Registration loader
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
 */

namespace PinkCrab\Core\Services\Registration;

use PinkCrab\Core\Application\App;
use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Core\Services\Registration\Loader;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Register_Loader {


	/**
	 * Loops through all classes for regisration and regiter
	 * if they have the Registerable interface.
	 *
	 * @param PinkCrab\Core\App $app
	 * @param array $registerable_classes
	 * @param PinkCrab\Core\Services\Registration\Loader $loader
	 * @return void
	 */
	public static function initalise( App $app, array $registerable_classes, Loader $loader ): void {
		foreach ( $registerable_classes as $class ) {
			if ( in_array( Registerable::class, class_implements( $class ), true ) ) {
				$app::make( $class )->register( $loader );
			}
		}
	}
}
