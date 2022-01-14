<?php

namespace WC {

final class Services {
    
    static private $i_me = null;

    public static function instance() {
        if (!self::$i_me) {
            self::$i_me = new Services();
        }
        return self::$i_me;
    }
    public function __construct(
        private array $shared = [],
        private array $coded = [])
    {
    }
    
    public function has(string $name) : bool
    {
        return isset($this->shared[$name])
               || isset($this->coded[$name]);
    }
    public function set(string $name, $value) {
        $this->coded[$name] = $value;
    }
    
    public function get(string $name) {
        $value = $this->coded[$name] ?? null;
        if ($value === null) {
            return $this->getShared($name);
        }
        if (is_callable($value)) {
            return $value($this);
        }
        return $value;
    }
    public function setShared(string $name, $value) {
        $this->shared[$name] = $value;
    }
    
    public function getShared(string $name) {
        $value = $this->shared[$name] ?? null;
        if ($value === null) {
            //throw new \Exception("Shared service ($name) is not configured");
            return null;
        }
        if (is_string($value)) {
            $obj = new $value();
            $this->shared[$name] = $obj;
            return $obj;
        }
        if (is_callable($value)) {
            $result = $value($this);
            $this->shared[$name] = $result;
            return $result;
        }
        return $value;
    }
    
    public function __get($name) {
        return $this->get($name);
    }
}

}

