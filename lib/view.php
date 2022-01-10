<?php

/**
 * Taken out from base.php
 */

//! View handler
class View extends Prefab {

	private
		//! Temporary hive
		$hive_copy;

	protected
		//! Template file
		$file,
		//! Post-rendering handler
		$trigger,
		//! Nesting level
		$level=0;
        
                

	/** @var \Base Framework instance */
	protected $fw;


                
        function url($base, $params) {
            $ret = '/' . $base;
            if (is_array($params)) {
                $ret .= '?' . http_build_query($params);
            }
            return $ret;
        }
	function __construct() {
		$this->fw=\Base::instance();
	}

	/**
	*	Encode characters to equivalent HTML entities
	*	@return string
	*	@param $arg mixed
	**/
	function esc($arg) {
		return $this->fw->recursive($arg,
			function($val) {
				return is_string($val)?$this->fw->encode($val):$val;
			}
		);
	}

	/**
	*	Decode HTML entities to equivalent characters
	*	@return string
	*	@param $arg mixed
	**/
	function raw($arg) {
		return $this->fw->recursive($arg,
			function($val) {
				return is_string($val)?$this->fw->decode($val):$val;
			}
		);
	}

	/**
	*	Create sandbox for template execution
	*	@return string
	*	@param $hive array
	*	@param $mime string
	**/
	protected function sandbox(array $hive=NULL,$mime=NULL) {
		$fw=$this->fw;
		$implicit=FALSE;
		if (is_null($hive)) {
			$implicit=TRUE;
			$hive=$fw->hive();
		}

                if (!$mime) {
                    $mime = $fw->MIME;
                }

		if ($this->level<1 || $implicit) {
			if (!$fw->CLI && $mime && !headers_sent() &&
				!preg_grep ('/^Content-Type:/',headers_list()))
				header('Content-Type: '.$mime.'; '.
					'charset='.$fw->ENCODING);
			if ($fw->ESCAPE && (!$mime ||
					preg_match('/^(text\/html|(application|text)\/(.+\+)?xml)$/i',$mime)))
				//$hive=$this->esc($hive); // don't do this here!!
			if (isset($hive['ALIASES']))
				$hive['ALIASES']=$fw->build($hive['ALIASES']);
		}
		$this->hive_copy=$hive;
		unset($fw,$hive,$implicit,$mime);
		extract($this->hive_copy);
		
		++$this->level;
		ob_start();
		require($this->file);
		--$this->level;
                
                $this->hive_copy = null;
                
		return ob_get_clean();
	}

	/**
	*	Render template
	*	@return string
	*	@param $file string
	*	@param $hive array
	*	@param $ttl int
	**/
	function render($file, array $hive=NULL,$ttl=0) {
		$fw=$this->fw;
		$cache=Cache::instance();
		foreach (Loader::split($fw->UI) as $dir) {
			if ($cache->exists($hash=$fw->hash($dir.$file),$data))
				return $data;
			if (is_file($this->file=$fw->fixslashes($dir.$file))) {
				if (isset($_COOKIE[session_name()]) &&
					!headers_sent() && session_status()!=PHP_SESSION_ACTIVE)
					session_start();
				$fw->sync('SESSION');
				$data=$this->sandbox($hive);
				if (isset($this->trigger['afterrender']))
					foreach($this->trigger['afterrender'] as $func)
						$data=$fw->call($func,[$data, $dir.$file]);
				if ($ttl)
					$cache->set($hash,$data,$ttl);
				return $data;
			}
		}
		user_error(sprintf(Base::E_Open,$file),E_USER_ERROR);
	}

	/**
	*	post rendering handler
	*	@param $func callback
	*/
	function afterrender($func) {
		$this->trigger['afterrender'][]=$func;
	}
        
        function hive_copy() {
            return $this->hive_copy;
        }

}
