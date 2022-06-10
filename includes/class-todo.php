<?php

namespace WHTodo\Includes;

defined( 'ABSPATH' ) || exit;

class Todo {
	use Trait_Singleton;

	/**
	 * Option data name;
	 *
	 * @var string
	 */
	private $option_name;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->option_name = 'wh_data_' . get_current_user_id();

		add_action( 'wp_ajax_wh_todo_create', [ $this, 'create' ] );
		add_action( 'wp_ajax_wh_todo_delete', [ $this, 'delete' ] );
		add_action( 'wp_ajax_wh_todo_update', [ $this, 'update' ] );
		add_action( 'wp_ajax_wh_todo_change_status', [ $this, 'change_status' ] );
		add_action( 'admin_notices', [ $this, 'render_list' ] );
	}

	/**
	 * Save todo.
	 */
	public function create() {
		check_ajax_referer( 'wh_todo_nonce', 'todo_nonce' );

		$todo_data = get_option( $this->option_name );

		$id = array_key_last( $todo_data ) + 1;

		$todo_data[ $id ] = [
			'text'   => '',
			'status' => 'actual',
		];

		$updated = update_option( $this->option_name, $todo_data );

		wp_send_json_success(
			[
				'updated' => $updated,
				'id'      => array_key_last( $todo_data ),
			]
		);
	}

	/**
	 * Delete todo.
	 */
	public function delete() {
		check_ajax_referer( 'wh_todo_nonce', 'todo_nonce' );

		if ( empty( $_POST['id'] ) ) {
			wp_send_json_error( esc_html__( 'Missing todo ID.', 'wh-todo' ), 400 );
		}

		$id = sanitize_text_field( wp_unslash( $_POST['id'] ) );

		$todo_data = get_option( $this->option_name );

		unset( $todo_data[ $id ] );

		$updated = update_option( $this->option_name, $todo_data );

		wp_send_json_success(
			[
				'updated' => $updated,
			]
		);
	}

	/**
	 * Update todo.
	 */
	public function update() {
		check_ajax_referer( 'wh_todo_nonce', 'todo_nonce' );

		if ( empty( $_POST['id'] ) ) {
			wp_send_json_error( esc_html__( 'Missing todo ID.', 'wh-todo' ), 400 );
		}

		$text = '';

		if ( ! empty( $_POST['text'] ) ) {
			$text = sanitize_text_field( wp_unslash( $_POST['text'] ) );
		}

		$id = sanitize_text_field( wp_unslash( $_POST['id'] ) );

		$todo_data = get_option( $this->option_name );

		$todo_data[ $id ]['text'] = $text;

		$updated = update_option( $this->option_name, $todo_data );

		wp_send_json_success(
			[
				'updated' => $updated,
			]
		);
	}

	/**
	 * Make done.
	 */
	public function change_status() {
		check_ajax_referer( 'wh_todo_nonce', 'todo_nonce' );

		if ( empty( $_POST['id'] ) ) {
			wp_send_json_error( esc_html__( 'Missing todo ID.', 'wh-todo' ), 400 );
		}

		if ( empty( $_POST['status'] ) ) {
			wp_send_json_error( esc_html__( 'Missing todo status.', 'wh-todo' ), 400 );
		}

		$id     = sanitize_text_field( wp_unslash( $_POST['id'] ) );
		$status = sanitize_text_field( wp_unslash( $_POST['status'] ) );

		$todo_data = get_option( $this->option_name );

		$todo_data[ $id ]['status'] = $status;

		$updated = update_option( $this->option_name, $todo_data );

		wp_send_json_success(
			[
				'updated' => $updated,
			]
		);
	}

	/**
	 * Render todo list.
	 */
	public function render_list() {
		$todo_data = get_option( $this->option_name, [] );

		?>
		<div class="wh-todo-list">
			<div class="wh-todo-inner">
				<div class="wh-notices-area"></div>

				<div class="wh-todo-item-example wh-hidden">
					<input type="text" class="regular-text" aria-label="<?php esc_html_e( 'Todo text' ); ?>" value="">
					<a href="#" class="wh-todo-delete button">
						<?php esc_html_e( 'Delete' ); ?>
					</a>
					<a href="#" class="wh-todo-done button" data-status="actual">
						<?php esc_html_e( 'Actual' ); ?>
					</a>
					<a href="#" class="wh-todo-done button" data-status="done">
						<?php esc_html_e( 'Done' ); ?>
					</a>
				</div>

				<a href="#" class="wh-todo-create button button-primary" data-action="create">
					<?php esc_html_e( 'Create' ); ?>
				</a>

				<div class="wh-todo-items">
					<?php foreach ( $todo_data as $id => $todo_item ) : ?>
						<?php
						if ( $todo_item['status'] === 'actual' ) {
							$item_classes = ' wh-status-actual';
						} else {
							$item_classes = ' wh-status-done';
						}
						?>

						<div class="wh-todo-item<?php echo esc_attr( $item_classes ); ?>" data-id="<?php echo esc_attr( $id ); ?>">
							<input type="text" class="regular-text" aria-label="<?php esc_html_e( 'Todo text' ); ?>" value="<?php echo esc_attr( $todo_item['text'] ); ?>" <?php echo disabled( $todo_item['status'] === 'done' ); ?>>

							<a href="#" class="wh-todo-delete button">
								<?php esc_html_e( 'Delete' ); ?>
							</a>
							<a href="#" class="wh-todo-done button" data-status="actual">
								<?php esc_html_e( 'Actual' ); ?>
							</a>
							<a href="#" class="wh-todo-done button" data-status="done">
								<?php esc_html_e( 'Done' ); ?>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
