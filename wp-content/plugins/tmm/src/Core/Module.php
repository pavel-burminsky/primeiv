<?php

namespace TMM\Core;

/**
 * Trait Module
 *
 * @package TMM\Core
 */
trait Module {

    use Singleton;

    /**
     * Module constructor.
     */
    private function __construct() {
        $this->init();
    }

    /**
     * @return void
     */
    abstract public function init();
}