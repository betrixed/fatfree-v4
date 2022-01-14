<?php

/**
 * Description of loader
 *
 * @author Michael Rynn
 */
class Loader {
    
    /**
     * 
     * @var string
     */
    private  $core;
    
    /**
     * 
     * @var string
     */
    private  $auto_str;
    
    /**
     * 
     * @var array
     */
    private  $paths;
    
    /**
     * @var callable
     */
    
    private $func;
    /**
     * 
     * @param string $auto_str
     * @param string $core
     */
    public function __construct($auto_str, $core = __DIR__)
    {
        $this->set_autoload($auto_str);
        $this->set_core($core);
        $this->paths = null;
        spl_autoload_register([$this,'autoload']);
    }
    
    public static function bootstrap() {
        $loader = new Loader("./");
        Registry::set(Loader::class, $loader);
    }
    public static function instance() {
        return Registry::get(self::class);
    }
    /**
     * 
     * @param string $str
     * @param boolean $noempty
     * @return type
     */
    public static function split($str,$noempty=TRUE) {
        return array_map('trim',
           preg_split('/[,;|]/', $str, 0, $noempty?PREG_SPLIT_NO_EMPTY:0) );
                
    }
    /**
     * 
     * @param string $str
     * @return string
     */
    public static function fixslashes($str) {
        return $str ? strtr($str,'\\','/') : $str;
    }
    /**
     * 
     * @param string $auto_str
     * 
     */
    public function set_autoload($auto_str) {
        $this->auto_str = $auto_str;
    }
    
    /**
     * 
     * @param string $core
     */
    public function set_core($core = __DIR__) 
    {
        $s = self::fixslashes($core);
        if (substr($s,-1) !== '/') {
            $s .= '/';
        }
        $this->core = $s;
    }
    
    /**
     * 
     * @return string
     */
    public function get_autoload() {
        return $this->auto_str;
    }
    /**
     * 
     * @return string
     */
    public function get_core() {
        return $this->core;
    }
    
    /**
     * @return array
     */
    public function get_paths() {
        if (empty($this->paths)) {
            $path = $this->auto_str;
            $func = null;
            if (is_array($path)) {
                if (isset($path[1]) && is_callable($path[1])) {
                    list($path, $func) = $path;
                    $this->func = $func;
                }
            }
            $this->paths = self::split($this->core . ";" . $this->auto_str);
        }
        return $this->paths;
    }

    /**
     * 
     * @param type $class
     * @return mixed
     */
    public function autoload($class) {
            $class=self::fixslashes(ltrim($class,'\\'));
            
            $paths = $this->get_paths();
            // $this->func is reset by get_paths()
            $func= $this->func;
            foreach ($paths as $auto) {
                    if (($func && is_file($file=$func($auto.$class).'.php')) ||
                            is_file($file=$auto.$class.'.php') ||
                            is_file($file=$auto.strtolower($class).'.php') ||
                            is_file($file=strtolower($auto.$class).'.php'))
                    {
                            return require($file);
                    }
            }
            return false;
    }
}

Loader::bootstrap();