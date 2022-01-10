<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of loader
 *
 * @author parallels
 */
class Loader {
    
    private  $plugins;
    
    private  $paths;
    
    public function __construct($paths, $plugins = __DIR__)
    {
        $this->set_paths($paths);
        $this->set_plugins($plugins);
        spl_autoload_register([$this,'autoload']);
    }
    public static function split($str,$noempty=TRUE) {
        return array_map('trim',
           preg_split('/[,;|]/', $str, 0, $noempty?PREG_SPLIT_NO_EMPTY:0) );
                
    }
    public static function fixslashes($str) {
        return $str ? strtr($str,'\\','/') : $str;
    }
    public function set_paths($paths) {
        $this->paths = $paths;
    }
    
    public function set_plugins($paths) 
    {
        $s = self::fixslashes($paths);
        if (substr($s,-1) !== '/') {
            $s .= '/';
        }
        $this->plugins = $s;
    }
    
    public function get_paths() {
        return $this->paths;
    }
    public function get_plugins() {
        return $this->plugins;
    }
    //put your code here
    protected function autoload($class) {
            $class=self::fixslashes(ltrim($class,'\\'));
            /** @var callable $func */
            $func=NULL;
            if (is_array($path = $this->paths) &&
                    isset($path[1]) && is_callable($path[1]))
                    list($path,$func)=$path;
            foreach (self::split($this->plugins.';'.$path) as $auto)
                    if (($func && is_file($file=$func($auto.$class).'.php')) ||
                            is_file($file=$auto.$class.'.php') ||
                            is_file($file=$auto.strtolower($class).'.php') ||
                            is_file($file=strtolower($auto.$class).'.php'))
                            return require($file);
    }
}
