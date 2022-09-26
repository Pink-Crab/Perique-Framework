<?php

declare(strict_types=1);

/**
 * A base view model
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
 * @package PinkCrab\Perique\View
 * @since 1.2.0
 */

namespace PinkCrab\Perique\Services\View;

class View_Model {

	/**
	 * The path to the template.
	 *
	 * @var string
	 */
	private $template;

	/**
	 * The data to be used with the template.
	 *
	 * @var array<string, mixed>
	 */
	private $data = array();

	/** @param array<string, mixed> $data */
	public function __construct( string $template, array $data = array() ) {
		$this->template = $template;
		$this->data     = $data;
	}

	/**
	 * Returns the template path.
	 *
	 * @return string
	 */
	public function template(): string {
		return $this->template;
	}

	/**
	 * Returns the data array.
	 *
	 * @return array<string, mixed>
	 */
	public function data(): array {
		return $this->data;
	}

}
