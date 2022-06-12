<?php

namespace WHTodo\Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use WHTodo\Includes\Todo;
use WP_Mock;

final class TodoAddHooksTest extends TestCase {
	public function setUp(): void {
		parent::setUp();
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
		Mockery::close();
		parent::tearDown();
	}

	public function test_add_hooks() {
		$todo = new Todo();

		WP_Mock::expectActionAdded( 'wp_ajax_wh_todo_create', [ $todo, 'create' ] );
		WP_Mock::expectActionAdded( 'wp_ajax_wh_todo_delete', [ $todo, 'delete' ] );
		WP_Mock::expectActionAdded( 'wp_ajax_wh_todo_update', [ $todo, 'update' ] );
		WP_Mock::expectActionAdded( 'wp_ajax_wh_todo_change_status', [ $todo, 'change_status' ] );
		WP_Mock::expectActionAdded( 'admin_notices', [ $todo, 'render_list' ] );

		$todo->hooks();
	}
}
