<?php

declare(strict_types=1);

/**
 * Module Manager Exceptions
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
 * @version 2.0.0
 */

namespace PinkCrab\Perique\Exceptions;

use Exception;

class Module_Manager_Exception extends Exception {

	/**
	 * Cast any value to a string for exception message.
	 *
	 * @param mixed $value
	 * @return string
	 */
	private static function cast_to_string( $value ): string {
		if ( is_object( $value ) ) {
			return get_class( $value );
		}

		if ( is_array( $value ) ) {
			return \wp_json_encode( $value ) ?: 'FAILED_TO_CAST ARRAY';
		}

		if ( is_null( $value ) ) {
			return 'NULL';
		}

		if ( is_bool( $value ) ) {
			return $value ? 'BOOL::true' : 'BOOL::false';
		}

		return (string) $value;
	}

	/**
	 * Returns an exception if a module being added is not a valid module.
	 * @code 20
	 * @param string $module
	 * @return Module_Manager_Exception
	 */
	public static function invalid_module_class_name( string $module ): Module_Manager_Exception {
		$message = "{$module} must be an instance of the Module interface";
		return new Module_Manager_Exception( $message, 20 );
	}

	/**
	 * Failed to create Registration_Middleware, none instance created.
	 *
	 * @code 21
	 * @param mixed $created
	 * @return Module_Manager_Exception
	 */
	public static function failed_to_create_registration_middleware( $created ): Module_Manager_Exception {

		$created = self::cast_to_string( $created );

		$message = "Failed to create Registration_Middleware, invalid instance created. Created: {$created}";
		return new Module_Manager_Exception( $message, 21 );
	}

	/**
	 * Registration_Middleware is not a valid instance of the Registration_Middleware Interface.
	 *
	 * @code 22
	 * @param mixed $created
	 * @return Module_Manager_Exception
	 */
	public static function invalid_registration_middleware( $created ): Module_Manager_Exception {
		$created = self::cast_to_string( $created );

		$message = "{$created} was returned as the modules Middleware, but this does not implement Registration_Middleware interface";
		return new Module_Manager_Exception( $message, 22 );
	}

	/**
	 * None class added to registration service.
	 *
	 * @code 23
	 * @param mixed $passed
	 * @return Module_Manager_Exception
	 */
	public static function none_class_string_passed_to_registration( $passed ): Module_Manager_Exception {
		$passed = self::cast_to_string( $passed );

		$message = "None class-string \"{$passed}\" passed to the registration class list";
		return new Module_Manager_Exception( $message, 23 );
	}

}
