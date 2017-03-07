<?php

if ( class_exists( 'Thim_Singleton' ) ) {
	return;
}

/**
 * Class Thim_Singleton.
 *
 * @since 0.8.5
 */
abstract class Thim_Singleton {
	/**
	 * @var null
	 *
	 * @since 0.8.5
	 */
	static protected $instances = array();

	/**
	 * Thim_Singleton constructor.
	 *
	 * @since 0.8.5
	 */
	abstract protected function __construct();

	/**
	 * @since 0.8.5
	 *
	 * @return null
	 */
	static public function instance() {
		$class = get_called_class();
		if ( ! array_key_exists( $class, self::$instances ) ) {
			self::$instances[ $class ] = new $class();
		}

		return self::$instances[ $class ];
	}
}