<?php

use PHPUnit\Framework\TestCase;

use Brain\Monkey;
use Brain\Monkey\Functions;
use HTML_Forms\Form;

class FormTest extends TestCase {

	protected function setUp() {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_constructor() {
		$form = new Form(1);
		self::assertEquals( 1, $form->ID );
	}

	public function test_get_markup() {
		$form = new Form(1);
		self::assertEquals( '', $form->get_markup() );
	}

	public function test_get_email_fields() {
		$form = new Form(1);
		$form->settings['email_fields'] = 'EMAIL,OTHER_EMAIL';
		self::assertEquals( array( 'EMAIL', 'OTHER_EMAIL' ), $form->get_email_fields() );
	}

	public function test_get_required_fields() {
		$form = new Form(1);
		$form->settings['required_fields'] = 'EMAIL,NAME';
		self::assertEquals( array( 'EMAIL', 'NAME' ), $form->get_required_fields() );
	}

	public function test_get_field_count() {
		$form = new Form(1);
		$form->markup = '<input type="hidden" name="foo" value="bar" />';
		self::assertEquals(4, $form->get_field_count()); 

		$form->markup = '<input type="email" name="EMAIL" />' . PHP_EOL;
		$form->markup .= '<textarea></textarea>' . PHP_EOL;
		$form->markup .= '<TEXTAREA NAME="MESSAGE"></textarea>';
		self::assertEquals(5, $form->get_field_count()); 
	}

	public function test_get_message() {
		$form = new Form(1);
		$form->messages = $messages = array( 
			'error' => 'Error!'
		);

		self::assertEquals('', $form->get_message('unexisting_message_code'));
		self::assertEquals($messages['error'], $form->get_message('error'));
	}

	public function test_get_data_attributes() {
		$form = new Form(1);
		$form->title = 'Title';
		$form->slug = 'slug';
		$form->messages = $messages = array( 
			'error' => 'Error!'
		);

		self::assertEquals('data-id="1" data-title="Title" data-slug="slug" data-message-error="Error!"', $form->get_data_attributes());
	}
}
