<?php

namespace WHTodo\Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use WHTodo\Includes\Todo;
use WP_Mock;

final class TodoUpdateTest extends TestCase {
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

	public function test_success_update() {
		$todo = new Todo();

		$this->pass_nonce();

		$_POST['id']   = 1;
		$_POST['text'] = 'Test2';

		$this->pass_sanitize();

		WP_Mock::userFunction( 'get_option' )->with( $todo->get_option_name(), [] )->andReturn(
			[
				1 => [
					'text'   => 'Test1',
					'status' => 'actual',
				],
			]
		)->once();

		WP_Mock::userFunction( 'update_option' )->with(
			$todo->get_option_name(),
			[
				1 => [
					'text'   => 'Test2',
					'status' => 'actual',
				],
			]
		)->andReturn( true )->once();

		$this->send_success();

		$todo->update();
	}

	public function test_update_no_id() {
		$todo = new Todo();

		$this->pass_nonce();

		WP_Mock::userFunction( 'wp_send_json_error' )->with( 'Missing todo ID.', 400 )->once();

		$todo->update();
	}

	public function test_update_no_text() {
		$todo = new Todo();

		$this->pass_nonce();

		$_POST['id'] = 1;

		$this->pass_sanitize();

		WP_Mock::userFunction( 'get_option' )->with( $todo->get_option_name(), [] )->andReturn(
			[
				1 => [
					'text'   => 'Test1',
					'status' => 'actual',
				],
			]
		)->once();

		WP_Mock::userFunction( 'update_option' )->with(
			$todo->get_option_name(),
			[
				1 => [
					'text'   => '',
					'status' => 'actual',
				],
			]
		)->andReturn( true )->once();

		$this->send_success();

		$todo->update();
	}

	private function pass_nonce() {
		WP_Mock::userFunction( 'check_ajax_referer' )->with( 'wh_todo_nonce', 'todo_nonce' )->once();
	}

	private function pass_sanitize() {
		WP_Mock::passthruFunction( 'wp_unslash' );
		WP_Mock::passthruFunction( 'sanitize_text_field' );
	}

	private function send_success() {
		WP_Mock::userFunction( 'wp_send_json_success' )->with(
			[
				'updated' => true,
			]
		)->once();
	}
}
