<?php

namespace WHTodo\Includes;

use Exception;

defined( 'ABSPATH' ) || exit;

trait Trait_Singleton {
	/**
	 * Instance of this static object.
	 *
	 * @var array
	 */
	private static $instance;

	/**
	 * Get singleton instance.
	 *
	 * @return object Current object instance.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor is private to prevent initiation with outer code.
	 */
	private function __construct() {}

	/**
	 * Prevent the instance from being cloned.
	 */
	private function __clone() {}

	/**
	 * Prevent from being unserialized.
	 *
	 * @throws Exception Exception.
	 */
	public function __wakeup() {
		throw new Exception( esc_html__( 'Cannot unserialize a singleton.', 'wh-todo' ) );
	}
}
