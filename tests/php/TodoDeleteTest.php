<?php

namespace WHTodo\Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use WHTodo\Includes\Todo;
use WP_Mock;

final class TodoDeleteTest extends TestCase {
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

	public function test_success_delete() {
		$todo = new Todo();

		$this->pass_nonce();

		$_POST['id'] = 2;

		WP_Mock::passthruFunction( 'wp_unslash' );
		WP_Mock::passthruFunction( 'sanitize_text_field' );

		WP_Mock::userFunction( 'get_option' )->with( $todo->get_option_name(), [] )->andReturn(
			[
				1 => [
					'text'   => 'Test1',
					'status' => 'actual',
				],
				2 => [
					'text'   => 'Test2',
					'status' => 'done',
				],
			]
		)->once();

		WP_Mock::userFunction( 'update_option' )->with(
			$todo->get_option_name(),
			[
				1 => [
					'text'   => 'Test1',
					'status' => 'actual',
				],
			]
		)->andReturn( true )->once();

		WP_Mock::userFunction( 'wp_send_json_success' )->with(
			[
				'updated' => true,
			]
		)->once();

		$todo->delete();
	}

	public function test_delete_no_id() {
		$todo = new Todo();

		$this->pass_nonce();

		WP_Mock::userFunction( 'wp_send_json_error' )->with( 'Missing todo ID.', 400 )->once();

		$todo->delete();
	}

	private function pass_nonce() {
		WP_Mock::userFunction( 'check_ajax_referer' )->with( 'wh_todo_nonce', 'todo_nonce' )->once();
	}
}
