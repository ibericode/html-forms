<?php

use PHPUnit\Framework\TestCase;

use Brain\Monkey;
use Brain\Monkey\Functions;
use HTML_Forms\Form;
use HTML_Forms\Forms;

class EmailTest extends TestCase {

	protected function setUp() : void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown() : void  {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_process() {
		$action = new HTML_Forms\Actions\Email();

		$submission = new HTML_Forms\Submission();
		$form = new Form(1);

		Functions\when('wp_mail')->justReturn(true);
		Functions\when('get_option')->justReturn('');

		// empty settings should not send email
		$settings = array();
		self::assertFalse($action->process($settings, $submission, $form));

		// valid settings should send email
		$settings = array( 'to' => 'admin@wordpress.dev', 'message' => 'Hello' );
		self::assertTrue($action->process($settings, $submission, $form));
	}
}
