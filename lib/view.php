<?php

/**
 * Taken out from base.php
 */
//! View handler
class View extends Prefab {

    private
    // user parameters
            $inject;
    protected
    //! Template file
            $file,
            //! Post-rendering handler
            $trigger,
            //! Nesting level
            $level = 0,
            $mime;

    /** @var \Base Framework instance */
    protected $fw;

    function inject() {
        return $this->inject;
    }

    function set_mime($mime) {
        $this->mime = $mime;
    }

    function url($base, $params) {
        $ret = '/' . $base;
        if (is_array($params)) {
            $ret .= '?' . http_build_query($params);
        }
        return $ret;
    }

    function __construct() {
        $this->fw = \Base::instance();
    }

    /**
     * 	Encode characters to equivalent HTML entities
     * 	@return string
     * 	@param $arg mixed
     * */
    function esc($arg) {
        return $this->fw->recursive($arg,
                        function ($val) {
                            return is_string($val) ? $this->fw->encode($val) : $val;
                        }
        );
    }

    /**
     * 	Decode HTML entities to equivalent characters
     * 	@return string
     * 	@param $arg mixed
     * */
    function raw($arg) {
        return $this->fw->recursive($arg,
                        function ($val) {
                            return is_string($val) ? $this->fw->decode($val) : $val;
                        }
        );
    }

    protected function check_mime() {
        $fw = $this->fw;
        $mime = isset($this->mime) ? $this->mime : $fw->MIME;
        if (!$fw->CLI 
                && !headers_sent() 
                && !preg_grep('/^Content-Type:/', headers_list())) {
            header('Content-Type: ' . $mime . '; ' .
                    'charset=' . $fw->ENCODING);
        }
    }

    /**
     * 	Create sandbox for template execution
     * 	@return string
     * 	@param $data array
     * 	@param $mime string
     * */
    protected function sandbox(array $inject = NULL) {
        $fw = $this->fw;

        $hive = $fw->hive();

        if ($this->level < 1) {
            $this->check_mime();
            if (isset($hive['ALIASES']))
                $hive['ALIASES'] = $fw->build($hive['ALIASES']);
        }
        $this->inject = $inject;
        unset($fw, $hive);

        $this->hive_inject($inject);
        extract($inject);
        ++$this->level;
        ob_start();
        require($this->file);
        --$this->level;

        $this->inject = null;

        return ob_get_clean();
    }

    /**
     * 
     * @return array
     */
    protected function hive_inject(&$inject) {
        $inject_list = $this->fw->get("VIEW_INJECT");
        $hive = $this->fw->hive();
        if (is_array($inject_list)) {
            foreach ($inject_list as $name) {
                if (isset($hive[$name])) {
                    $inject[$name] = $hive[$name];
                }
            }
        }
    }

    /**
     * 	Render template
     * 	@return string
     * 	@param $file string
     * 	@param $hive array
     * 	@param $ttl int
     * */
    function render($file, array $inject = NULL, $ttl = 0) {
        $fw = $this->fw;
        $cache = Cache::instance();
        foreach (Loader::split($fw->UI) as $dir) {
            if ($cache->exists($hash = $fw->hash($dir . $file), $data))
                return $data;
            if (is_file($this->file = $fw->fixslashes($dir . $file))) {
                if (isset($_COOKIE[session_name()]) &&
                        !headers_sent() && session_status() != PHP_SESSION_ACTIVE)
                    session_start();
                $fw->sync('SESSION');
                $data = $this->sandbox($inject);
                if (isset($this->trigger['afterrender']))
                    foreach ($this->trigger['afterrender'] as $func)
                        $data = $fw->call($func, [$data, $dir . $file]);
                if ($ttl)
                    $cache->set($hash, $data, $ttl);
                return $data;
            }
        }
        user_error(sprintf(Base::E_Open, $file), E_USER_ERROR);
    }

    /**
     * 	post rendering handler
     * 	@param $func callback
     */
    function afterrender($func) {
        $this->trigger['afterrender'][] = $func;
    }

    function hive_copy() {
        return $this->hive_copy;
    }

}
