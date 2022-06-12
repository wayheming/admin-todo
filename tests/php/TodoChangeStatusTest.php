<?php

namespace WHTodo\Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use WHTodo\Includes\Todo;
use WP_Mock;

final class TodoChangeStatusTest extends TestCase {
	public function setUp(): void {
		$_POST = [];
		parent::setUp();
		WP_Mock::setUp();

		WP_Mock::userFunction( 'get_current_user_id', 2 );
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
		Mockery::close();
		parent::tearDown();
	}

	public function test_success_change_status() {
		$todo = new Todo();

		WP_Mock::userFunction( 'check_ajax_referer' )->with( 'wh_todo_nonce', 'todo_nonce' )->once();

		$_POST['id']     = 1;
		$_POST['status'] = 'done';

		WP_Mock::passthruFunction( 'wp_unslash' );
		WP_Mock::passthruFunction( 'sanitize_text_field' );

		WP_Mock::userFunction( 'get_option' )->with( $todo->get_option_name() )->andReturn( [
			1 => [
				'text'   => 'Test1',
				'status' => 'actual',
			],
		] )->once();

		WP_Mock::userFunction( 'update_option' )->with( $todo->get_option_name(), [
			1 => [
				'text'   => 'Test1',
				'status' => 'done',
			],
		] )->andReturn( true )->once();

		WP_Mock::userFunction( 'wp_send_json_success' )->with( [
			'updated' => true,
		] )->once();

		$todo->change_status();
	}

	public function test_change_status_no_id() {
		$todo = new Todo();

		WP_Mock::userFunction( 'check_ajax_referer' )->with( 'wh_todo_nonce', 'todo_nonce' )->once();

		WP_Mock::userFunction( 'wp_send_json_error' )->with( 'Missing todo ID.', 400 )->once();

		$todo->change_status();
	}

	public function test_update_no_status() {
		$todo = new Todo();

		$_POST['id'] = 1;

		WP_Mock::userFunction( 'check_ajax_referer' )->with( 'wh_todo_nonce', 'todo_nonce' )->once();

		WP_Mock::userFunction( 'wp_send_json_error' )->with( 'Missing todo status.', 400 )->once();

		$todo->change_status();
	}
}