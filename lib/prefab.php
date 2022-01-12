<?php

/**
 * Taken out of base
 */
abstract class Prefab {

	/**
	*	Return class instance
	*	@return static
	**/
	static function instance() {
		if (!Registry::exists($class=get_called_class())) {
			Registry::set($class,new $class);
		}
		return Registry::get($class);
	}

}