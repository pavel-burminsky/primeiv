<?php
/*
Plugin Name: TMM
Plugin URI: https://tellmemore.com/
Description: Common Used TMM features
Version: 1.0.0
Author: Artem Lapkin
Author URI:
License: GPLv2 or later
Text Domain: tmm
*/


namespace TMM;

use TMM\Core\Module;
use TMM\Modules\ModulesProvider;


require_once __DIR__ . '/vendor/autoload.php';


class TMM {

    use Module;

    /** @var string */
    public $plugin_path;

    /** @var string */
    public $plugin_url;

    /**
     * @return void
     */
    public function init() {
        $this->plugin_path = __DIR__ . '/';

        $this->plugin_url = plugin_dir_url(__FILE__);

        ModulesProvider::get_instance();
        Infrastructure\InfrastructureProvider::get_instance();
    }
}

/**
 * @return mixed
 */
function tmm_plugin() {
    return TMM::get_instance();
}

// Init plugin
tmm_plugin();
