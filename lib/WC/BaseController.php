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
}
