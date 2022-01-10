<?php
namespace WC\Mixin;

use WC\Services;
use Exception;
/**
 * Description of function
 *
 * @author michael
 */
trait PropertyService {
    protected $container = null;
    
    public function setDI(WConfig $obj) {
        $this->container = $obj;
    }
    
    public function getDI() : object {
        if ($this->container === null) {
            $this->setGlobalDI();
        }
        return $this->container;
 
    }
    
    public function hasService($name) {
        return $this->getDI()->has($name);
    }
    public function getService(string $name, ?string $msg = null) {
        $value = $this->getDI()->get($name);
        if ($value === null) {
            if ($msg === null) {
                $msg = "Require '" . $name . "' service";
            }
            throw new Exception($msg);
        }
        return $value;
    }
    
    public function setGlobalDI() {
        $this->container = Services::instance();
    }

    public function __get($name) {
        $container = $this->container;
        if ($container === null) {
            $container = Services::instance();
            $this->container = $container;
        }
        $obj = $container->get($name);
        if (!empty($obj)) {
            $this->$name = $obj;
            return $obj;
        }
        return $this->nullService($name);
    }
    
    // override for exception handling
    public function nullService(string $name) {
        // try specific getXxx
        $method = 'get' . str_camel($name);
        if (method_exists($this,$method)) {
            return $this->$method();
        }
        throw new Exception("Service $name not found");
    }
}
