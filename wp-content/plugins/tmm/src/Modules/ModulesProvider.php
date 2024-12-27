<?php
/**
 * 
 */

namespace TMM\Modules;


use TMM\Core\Provider;


/**
 * Class ModulesProvider
 *
 * @package TMM\Modules
 */
final class ModulesProvider {

	use Provider;

	/**
	 * Instantiate modules
	 *
	 * @return array
	 */
	protected function get_modules() {
		return [
            MultipleAdminEmails::class,
		];
	}
}
