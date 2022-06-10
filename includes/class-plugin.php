<?php

namespace WHTodo\Includes;

defined( 'ABSPATH' ) || exit;

final class Plugin {
	use Trait_Singleton;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		Todo::get_instance();
	}

	/**
	 * Enqueue styles for the admin area.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'wh-admin-styles',
			WH_TODO_URL . 'assets/css/styles.min.css',
			[],
			WH_TODO_VERSION,
		);
	}

	/**
	 * Enqueue JavaScript scripts for the admin area.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'wh-admin-scripts',
			WH_TODO_URL . 'assets/js/scripts.min.js',
			[ 'jquery' ],
			WH_TODO_VERSION,
			true
		);

		wp_localize_script(
			'wh-admin-scripts',
			'whConfig',
			[
				'adminAjaxUrl' => admin_url( 'admin-ajax.php' ),
				'todoNonce'    => wp_create_nonce( 'wh_todo_nonce' ),
			]
		);
	}
}
