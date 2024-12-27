<?php
/**
 * 
 */

namespace TMM\Core;

/**
 * Trait Provider
 *
 * @package TMM\Core
 */
trait Provider {
	use Module;

	/**
	 * @return void
	 */
	public function init() {
		foreach ($this->get_modules() as $module) {
			$module::get_instance();
		}
	}

	/**
	 * Return an array of modules
	 * @return array
	 */
	abstract protected function get_modules();
}