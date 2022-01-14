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
    /**
     * Ensure directory path is searched first.
     * Append end DIRECTORY_SEPARATOR if missing.
     * @return string  Reference to the hive UI value
     * @param string $path
     */
    public function &prefixUI(string $path) : string
    {
        if (strrpos($path, DIRECTORY_SEPARATOR) !== strlen($path)-2) {
            $path .= DIRECTORY_SEPARATOR;
        }
        $f3 = $this->f3;
        return $f3->prefix('UI', $path . ";");
    }
}
