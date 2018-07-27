<?php

use PHPUnit\Framework\TestCase;

use Brain\Monkey;
use Brain\Monkey\Functions;

class FunctionsTest extends TestCase {

	protected function setUp() {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * @covers \hf_template
	 */
	public function test_hf_template() {
		Functions\when('is_user_logged_in')->justReturn(false);

		// existing replacer: no parameter, fallback value, parameter with dot
		self::assertEquals( hf_template( 'Hello {{user}}' ), "Hello " );
		self::assertEquals( hf_template( 'Hello {{user||visitor}}' ), "Hello visitor" );
		self::assertEquals( hf_template( 'Hello {{user.name||visitor}}' ), "Hello visitor" );
		self::assertEquals( hf_template( 'Hello {{user.nested.key||visitor}}' ), "Hello visitor" );

		// whitespace variations
		self::assertEquals( hf_template( 'Hello {{ user.name || visitor }}' ), "Hello visitor" );
		self::assertEquals( hf_template( 'Hello {{ user.name   }}' ), "Hello " );
		self::assertEquals( hf_template( 'Hello {{    user.name ||     visitor}}' ), "Hello visitor" );

		// multiple replacements
		self::assertEquals( hf_template( "Hello {{user.name || visitor}}. \nFrom {{user.location || Europe}}?" ), "Hello visitor. \nFrom Europe?" );

		// unexisting replacer: dot in parameter, fallback value
		self::assertEquals( hf_template( 'Hello {{ foobar }}' ), "Hello {{ foobar }}" );
		self::assertEquals( hf_template( 'Hello {{ foobar.foo.bar }}' ), 'Hello {{ foobar.foo.bar }}' );
		self::assertEquals( hf_template( 'Hello {{ foobar.foo.bar || visitor}}' ), 'Hello {{ foobar.foo.bar || visitor}}' );
		self::assertEquals( hf_template( 'Hello {{}}' ), "Hello {{}}" );
	}

	/**
	 * @covers \hf_replace_data_variables
	 */
	public function test_hf_replace_data_variables() {
		self::assertEquals( 'Hi ', hf_replace_data_variables( 'Hi [NAME]' ) );
		self::assertEquals( 'Hi John', hf_replace_data_variables( 'Hi [NAME]', array( 'NAME' => 'John' ) ) );
		self::assertEquals( 'Hi John', hf_replace_data_variables( 'Hi [USER.NAME]', array( 'USER' => array( 'NAME' => 'John' ) ) ) );

		self::assertEquals( 'Hi John, Eric', hf_replace_data_variables( 'Hi [NAMES]', array( 'NAMES' => array( 'John', 'Eric' ) ) ) );
	}

	/**
	 * @covers \hf_array_get
	 */
	public function test_hf_array_get() {
		self::assertEquals( 'bar', hf_array_get( array( 'foo' => 'bar' ), 'foo' ) );
		self::assertEquals( 'foobar', hf_array_get( array( 'foo' => array( 'bar' => 'foobar' ) ), 'foo.bar' ) );
		self::assertEquals( 'default', hf_array_get( array( 'foo' => array( 'bar' => 'foobar' ) ), 'bar', 'default' ) );
	}

	public function test_hf_is_date() {
		$true = array(
			'2017/01/01',
			'2017-01-01',
			'01-01-2017',
			'01/01/2017',
		);
		foreach( $true as $date ) {
			self::assertTrue( hf_is_date( $date ) );
		}

		$false = array(
			'',
			'johndoe@email.com',
			'06-12345678',
			'2017-2017',
			'2017-2017-22',
		);

		foreach( $false as $date ) {
			self::assertFalse( hf_is_date( $date ) );
		}
	}


}
