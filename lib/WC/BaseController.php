<?php

namespace WC;
/**
 *
 * @author michael
 */
use WC\Mixin\PropertyService;


class BaseController {
    use PropertyService;

    public function __construct() {
        $this->setGlobalDI();
    }
    
    public function prefixUI(string $path) {
        if (strrpos($path, DIRECTORY_SEPARATOR) !== strlen($path)-2) {
            $path .= DIRECTORY_SEPARATOR;
        }
        $f3 = $this->f3;
        $f3->prefix('UI', $path . ";");

    }
}
