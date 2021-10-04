<?php

declare(strict_types=1);

/**
 * App initialisation exception.
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
 * @package PinkCrab\Perique\Exceptions
 * @version 0.4.0
 */

namespace PinkCrab\Perique\Exceptions;

use Exception;

class App_Initialization_Exception extends Exception {

	/**
	 * Returns an exception if a DI Container is not bound to the app.
	 * @code 1
	 * @return App_Initialization_Exception
	 */
	public static function requires_di_container(): App_Initialization_Exception {
		$message = 'The Application must be populated with a DI_Container before booting.';
		return new App_Initialization_Exception( $message, 1 );
	}

	/**
	 * Returns an exception if attempting to overwrite the DI Container.
	 * @code 2
	 * @return App_Initialization_Exception
	 */
	public static function di_container_exists(): App_Initialization_Exception {
		$message = 'App already contains a DI Container, can not redeclare.';
		return new App_Initialization_Exception( $message, 2 );
	}

	/**
	 * Returns an exception if the Registration_Service is not defined.
	 * @code 3
	 * @return App_Initialization_Exception
	 */
	public static function requires_registration_service(): App_Initialization_Exception {
		$message = 'App has not defined Registration Service, this must be set before use.';
		return new App_Initialization_Exception( $message, 3 );
	}

	/**
	 * Returns an exception if App hasn't been initialised and its getters are accessed
	 * @code 4
	 * @param string $service The service which has been called without initialising the app.
	 * @return App_Initialization_Exception
	 */
	public static function app_not_initialized( string $service ): App_Initialization_Exception {
		$message = "App must be initialised before calling {$service}";
		return new App_Initialization_Exception( $message, 4 );
	}

	/**
	 * Returns an exception for trying to redefine the App_Config if its already been set.
	 * @code 5
	 * @return App_Initialization_Exception
	 */
	public static function app_config_exists(): App_Initialization_Exception {
		$message = 'Can not redeclare App_Config as its already set to the application';
		return new App_Initialization_Exception( $message, 5 );
	}

	/**
	 * Returns an exception for trying to redefine the Registration_Service if its already been set.
	 * @code 7
	 * @return App_Initialization_Exception
	 */
	public static function registration_exists(): App_Initialization_Exception {
		$message = 'Can not redeclare Registration_Service as its already set to the application';
		return new App_Initialization_Exception( $message, 7 );
	}

	/**
	 * Returns an exception for trying to boot application without defining required properties
	 * @code 6
	 * @param array<int,string> $errors
	 * @return App_Initialization_Exception
	 */
	public static function failed_boot_validation( array $errors ): App_Initialization_Exception {
		$message = sprintf(
			'App failed boot validation : %s',
			join( ',', $errors )
		);
		return new App_Initialization_Exception( $message, 6 );
	}

	/**
	 * Returns an exception for trying to redefine the Loader if its already been set.
	 * @code 8
	 * @return App_Initialization_Exception
	 */
	public static function loader_exists(): App_Initialization_Exception {
		$message = 'Can not redeclare Loader as its already set to the application';
		return new App_Initialization_Exception( $message, 8 );
	}

	/**
	 * Returns an exception for trying to create registration middleware that is not middleware.
	 * @code 9
	 * @param string $class
	 * @return App_Initialization_Exception
	 */
	public static function invalid_registration_middleware_instance( string $class ): App_Initialization_Exception {
		$message = sprintf(
			'%s is not a valid instance of Registration_Middleware',
			$class
		);
		return new App_Initialization_Exception( $message, 9 );
	}

}
