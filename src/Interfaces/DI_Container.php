<?php

declare(strict_types=1);

/**
 * PinkCrab Dependency Inject Container Interface.
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
 */

namespace PinkCrab\Perique\Interfaces;

use PinkCrab\Loader\Hook_Loader;
use Psr\Container\ContainerInterface;

interface DI_Container extends ContainerInterface {

	/**
	 * Add a single rule.
	 *
	 * @param string $name
	 * @param array<string, array<mixed>> $rule
	 * @return DI_Container
	 */
	public function addRule( string $name, array $rule ): DI_Container;

	/**
	 * Add multiple rules
	 *
	 * @param array<string, array<mixed>> $rules
	 * @return DI_Container
	 */
	public function addRules( array $rules ): DI_Container;

	/**
	 * Create an instance of a class, with optional parameters.
	 *
	 * @param string $name
	 * @param array<mixed> $args
	 * @return object|null
	 */
	public function create( string $name, array $args = array() );
}
