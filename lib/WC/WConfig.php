<?php

namespace WC;

use Zo\Meta\MReader;
use stdClass;

class WConfig extends stdClass implements \ArrayAccess
{

    static function replaceDefines(WConfig $f)
    {
        $map = get_defined_constants();
        $f->replaceVars($map);
    }

    function toArray(): array
    {
        return get_object_vars($this);
    }

    function exists($name)
    {
        return isset($this->$name);
    }

    function has($key): bool
    {
        return isset($this->$key);
    }

    function get($key, $default = null)
    {
        return isset($this->$key) ? $this->$key : $default;
    }

    static function updateValue(&$value, $map)
    {
        $matches = null;
        if (preg_match('/\${(\w+)}/', $value, $matches)) {
            $value = str_replace($matches[0], $map[$matches[1]], $value);
        }
    }

    static function replaceValues(&$arr, $map)
    {
        foreach ($arr as $key => &$value) {
            if (is_string($value)) {
                self::updateValue($value, $map);
            } else if (is_array($value) || is_object($value)) {
                self::replaceValues($value, $map);
            }
        }
    }

    public function __construct(?array $init = null)
    {
        if (!empty($init)) {
            $this->addArray($init);
        }
    }

    // create 
    static public function fromPhp($filename)
    {
        $values = require $filename;
        return self::fromArray($values);
    }

    static public function fromArray($a)
    {
        $cfg = new WConfig();
        return $cfg->addArray($a);
    }

    /*
      public function addXml( $filename) {
      $xml = new XmlPhp($this);
      return $xml->parseFile($filename);
      }

      static public function fromToml( $filename )
      {
      return \Toml\Input::parseFile($filename);
      }

     */

    static public function fromXml($filename)
    {
        $xml = new MReader();
        return $xml->parseFile($filename);
    }

    /**
     * indexed (0+) arrays are replaced
     * keyed hash arrays are merged
     * @return array
     * @param array $old
     * @param array $new
     */
    static public function merge_hash(array $old, array $new): array
    {
        if (isset($old[0]) || isset($new[0])) {
            return $new; // no merge algorithm yet
        }
        // both hash keys
        foreach ($new as $k => $v) {
            $oldval = $old[$k] ?? null;
            if (!empty($oldval) && is_array($oldval) && is_array($v)) {
                $old[$k] = static::merge_hash($oldval, $v);
            } else {
                $old[$k] = $v; // replace
            }
        }
        return $old;
    }

    public function addArray($root)
    {
        foreach ($root as $key => $val) {
            if (is_array($val)) {
                if (property_exists($this, $key)) {
                    $old = $this->$key;
                    if (is_array($old)) {
                        $this->$key = static::merge_hash($old, $val);
                        continue;
                    }
                }
            }
            $this->$key = $val;
        }
        return $this;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->$offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->$offset;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->$offset = null;
    }

    static function load(string $filename, ?string $ext = null)
    {
        $data = null;
        if ($ext === null) {
            $pinfo = pathinfo($filename);
            $ext = $pinfo['extension'];
        }
        switch ($ext) {
            case 'xml' :
                $data = static::fromXml($filename);
                break;
            case 'php' :
                $data = static::fromPhp($filename);
                break;
            /* case 'toml' :
              $data = static::fromToml($filename);
              break; */
        }
        return $data;
    }

    /**
     * Could return array, or some configuration object
     * @param type $filename
     * @return array or object
     * @throws Exception
     */
    static function serialCache($filename)
    {
        $pinfo = pathinfo($filename);
        if (!file_exists($filename)) {
            throw new Exception("File " . $filename . " not found");
        }
        $cache_name = $pinfo['filename'];
        if (substr($cache_name, 0, 1) !== '.') {
            $cache_name = '.' . $cache_name;
        }
        $cache_file = $pinfo['dirname'] . '/' . $cache_name
                . '_' . $pinfo['extension'] . '.ser';

        if (file_exists($cache_file)) {
            if (filemtime($cache_file) > filemtime($filename)) {
                return unserialize(file_get_contents($cache_file));
            }
        }
        $data = self::load($filename, $pinfo['extension']);

        if (!empty($data)) {
            file_put_contents($cache_file, serialize($data));
            return $data;
        } else {
            throw new Exception("Read error from " . $filename);
        }
    }

    /**
     * Replace @var1  substitutions for values,  if they exist
     */
    public static function replace(stdClass $obj, string $hpath): string
    {
        $path = preg_replace_callback('|@([a-zA-Z][\w\d]*)|',
                function ($matches) use ($obj) {
                    $key = $matches[1];
                    if (property_exists($obj, $key)) {
                        return $obj->$key;
                    } else {
                        return '@' . $key;
                    }
                }, $hpath, 1
        );
        return $path;
    }

}
