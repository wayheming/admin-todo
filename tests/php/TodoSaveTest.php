<?php

namespace WHTodo\Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use WHTodo\Includes\Todo;
use WP_Mock;

final class TodoSaveTest extends TestCase {
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

	public function test_success_save() {
		$todo = new Todo();

		$this->pass_nonce();

		WP_Mock::userFunction( 'get_option' )->with( $todo->get_option_name(), [] )->andReturn( [] )->once();

		WP_Mock::userFunction( 'update_option' )->with(
			$todo->get_option_name(),
			[
				1 => [
					'text'   => '',
					'status' => 'actual',
				],
			]
		)->andReturn( true )->once();

		WP_Mock::userFunction( 'wp_send_json_success' )->with(
			[
				'updated' => true,
				'id'      => 1,
			]
		)->once();

		$todo->create();
	}

	private function pass_nonce() {
		WP_Mock::userFunction( 'check_ajax_referer' )->with( 'wh_todo_nonce', 'todo_nonce' )->once();
	}
}
