<?php

declare(strict_types=1);

/**
 * Trait to give access to DI Container for injectable dependencies.
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
 * @package PinkCrab\Perique\Container_Aware_Traits
 */

namespace PinkCrab\Perique\Services\Container_Aware_Traits;

use PinkCrab\Loader\Hook_Loader;

trait Inject_Hook_Loader_Aware {

	/**
	 * Access to the DI Container
	 *
	 * @var Hook_Loader
	 */
	protected $loader;

	/**
	 * Accepts the DI Container as a method injectable dependency.
	 *
	 * @param Hook_Loader $container
	 * @return void
	 */
	public function set_hook_loader( Hook_Loader $hook_loader ): void {
		$this->loader = $hook_loader;
	}
}

