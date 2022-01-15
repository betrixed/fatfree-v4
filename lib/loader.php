<?php

/**
 * Loader calculates an
 * array of root paths to
 * search for namespace class files.
 * For case sensitive file systems, either 
 * the case in names matches exactly,
 * or folder and file names are lower-cased.
 * 
 * The core() paths are always searched before the
 * autostr(). 
 * Ordered path search is returned by get_paths()
 *
 * @author Michael Rynn
 */
class Loader {

    /**
     * 
     * @var string
     */
    private string $core;

    /**
     * 
     * @var string
     */
    private string $auto_str;

    /**
     * 
     * @var array
     */
    private ?array $paths;

    /**
     * @var callable
     */
    private $func;

    /**
     * @var Loader
     */
    private static Loader $i_me;

    /**
     * Assume that chdir() will not be called
     * after call to __construct.
     * Core path is relative to getcwd().
     * @param string $auto_str
     * @param string $core
     */
    public function __construct(string $auto_str, string $core = __DIR__)
    {
        $wd = getcwd();
        if (str_starts_with($core, $wd))
        {
            $core = substr($core, strlen($wd) +
                    1);
        }
        $this->auto_str = $auto_str;
        $this->core = $core;
        $this->paths = null;
        spl_autoload_register([$this, 'autoload']);
    }

    public static function instance(): Loader
    {
        if (!isset(self::$i_me))
        {
            self::$i_me = new Loader("./");
        }
        return self::$i_me;
    }

    /**
     * Return array of string
     * Separators are ,;|
     * @param string $str
     * @param boolean $noempty
     * @return array  
     */
    public static function split(string $str, $noempty = TRUE): array
    {
        return array_map('trim',
                preg_split('/[,;|]/', $str, 0,
                        $noempty
                        ? PREG_SPLIT_NO_EMPTY
                        : 0));
    }

    /**
     * Separate each path, condition each with end slash.
     * @param string $paths
     * @return array
     */
    public static function path_list(string $paths): array
    {
        return array_map(
                function ($s) {
                    return Loader::fix_path($s);
                }, preg_split('/[,;|]/', $paths, 0, PREG_SPLIT_NO_EMPTY)
        );
    }

    /**
     * Ensure last character is DIRECTORY_SEPARATOR
     * @param string $str
     * @return string
     */
    public static function endslash(string $str): string
    {
        $len = strlen($str);
        if (($len ===
                0) ||
                (strrpos($str, DIRECTORY_SEPARATOR,
                        $len -
                        1) !==
                $len
                -
                1))
            $str .= DIRECTORY_SEPARATOR;
        return $str;
    }

    /**
     * Convert all backslash to forward slash
     * @param string $str
     * @return string
     */
    public static function fixslashes(string $str): string
    {
        return $str
                ? strtr($str, '\\', '/')
                : $str;
    }

    /**
     * Path all forward slash, ends in slash.
     * @param string $path
     * @return string
     */
    public static function fix_path(string $path): string
    {
        return self::endslash(self::fixslashes(trim($path)));
    }

    /**
     * 
     * @param string $auto_str
     * 
     */
    public function set_autoload(string $auto_str): void
    {
        $this->auto_str = $auto_str;
        $this->paths = null;
    }

    /**
     * 
     * @param string $core
     */
    public function set_core(string $auto_str = __DIR__): void
    {
        $this->core = $auto_str;
        $this->paths = null;
    }

    /**
     * 
     * @return string
     */
    public function get_autoload(): string
    {
        return $this->auto_str;
    }

    /**
     * 
     * @return string
     */
    public function get_core(): string
    {
        return $this->core;
    }

    /**
     * @return array
     */
    public function get_paths()
    {
        if (empty($this->paths))
        {
            $path = $this->auto_str;
            $func = null;
            if (is_array($path))
            {
                if (isset($path[1]) &&
                        is_callable($path[1]))
                {
                    list($path, $func) = $path;
                    $this->func = $func;
                }
            }
            $paths = self::split($this->core . ";" . $this->auto_str);
            $this->paths = array_map(
                    function ($val) {
                        return Loader::fix_path($val);
                    }, $paths);
        }
        return $this->paths;
    }

    /**
     * 
     * @param type $class
     * @return mixed
     */
    public function autoload(string $class): mixed
    {
        $cpath = self::fixslashes(ltrim($class, '\\'));
        $paths = $this->get_paths();
        // $this->func is reset by get_paths()
        $func = $this->func;
        foreach ($paths as $auto)
        {
            $fid = $auto . $cpath;
            if (($func &&
                    is_file($file = $func($fid) . '.php')) ||
                    is_file($file = $fid . '.php') ||
                    is_file($file = $auto . strtolower($cpath) . '.php') ||
                    is_file($file = strtolower($fid) . '.php'))
            {
                return require($file);
            }
        }
        return false;
    }

}

return Loader::instance();
