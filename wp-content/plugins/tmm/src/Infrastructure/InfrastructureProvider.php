<?php
/**
 * 
 */

namespace TMM\Infrastructure;

use TMM\Core\Provider;


/**
 * Class ModulesProvider
 *
 * @package SveaKBT\Modules
 */
final class InfrastructureProvider {

    use Provider;

    /**
     * Instantiate modules
     *
     * @return array
     */
    protected function get_modules() {
        return [
        ];
    }
}
