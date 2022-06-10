<?php

namespace WHTodo;

defined( 'ABSPATH' ) || exit;

class Autoload {
	/**
	 * Namespace.
	 *
	 * @var string
	 */
	private $namespace = 'WHTodo';

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
	 * Constructor.
	 */
	public function __construct() {
		spl_autoload_register( [ $this, 'load' ] );
	}

	/**
	 * Load rules.
	 *
	 * @param string $class Class name.
	 *
	 * @return void
	 */
	public function load( $class ) {
		if ( ! str_contains( $class, $this->namespace ) ) {
			return;
		}

		$class        = str_replace( $this->namespace, '', $class );
		$plugin_parts = explode( '\\', $class );
		$name         = array_pop( $plugin_parts );
		$name         = preg_match( '/^(Interface|Trait)/', $name )
			? $name . '.php'
			: 'class-' . $name . '.php';
		$local_path   = implode( '', $plugin_parts ) . '/' . $name;
		$local_path   = strtolower( str_replace( [ '\\', '_' ], [ '/', '-' ], $local_path ) );

		$path = WH_TODO_PATH . $local_path;

		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
}

Autoload::get_instance();
