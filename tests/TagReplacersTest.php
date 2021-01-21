<?php

use PHPUnit\Framework\TestCase;

use Brain\Monkey;
use Brain\Monkey\Functions;
use HTML_Forms\TagReplacers;

class TagReplacersTest extends TestCase {

	protected function setUp() : void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown() : void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_user() {
		$replacers = new TagReplacers();

		Functions\when('is_user_logged_in')->justReturn(false);
		self::assertEquals('', $replacers->user('display_name'));

		Functions\when('is_user_logged_in')->justReturn(true);
		Functions\when('wp_get_current_user')->justReturn((object) array( 'display_name' => 'John'));
		self::assertEquals('John', $replacers->user('display_name'));
	}

	public function test_post() {
		$replacers = new TagReplacers();

		// post not set
		self::assertEquals('', $replacers->post('post_name'));

		// existing prop
		$post = $this->getMockBuilder('\WP_Post')->getMock();
		$post->post_name = 'foobar';
		$GLOBALS['post'] = $post;
		self::assertEquals('foobar', $replacers->post('post_name'));
		unset($GLOBALS['post']);

		// // unexisting prop
		$post = $this->getMockBuilder('\WP_Post')->getMock();
		$GLOBALS['post'] = $post;
		self::assertEquals('', $replacers->post('unexisting_property'));
		unset($GLOBALS['post']);
	}

	public function test_url_params() {
		$replacers = new TagReplacers();

		$_GET['foo'] = 'bar';
		self::assertEquals('', $replacers->url_params('unexisting_key'));
		self::assertEquals('bar', $replacers->url_params('foo'));
	}
}
