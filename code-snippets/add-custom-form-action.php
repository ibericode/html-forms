<?php

use HTML_Forms\Actions\Action;
use HTML_Forms\Form;
use HTML_Forms\Submission;

class MyCustomAction extends Action {

	public $type  = 'pushbullet';
	public $label = '';

	public function __construct() {
		$this->label = __( 'Send to Pushbullet', 'html-forms' );
	}

	/**
	 * @return array
	 */
	private function get_default_settings() {
		return array(
			'url' => '',
		);
	}

	/**
	 * @param array $settings
	 * @param string|int $index
	 */
	public function page_settings( $settings, $index ) {
		$settings = array_merge( $this->get_default_settings(), $settings );
		?>
		<span class="hf-action-summary"><?php printf( 'Pushbullet' ); ?></span>
		<input type="hidden" name="form[settings][actions][<?php echo $index; ?>][type]" value="<?php echo $this->type; ?>" />
		<table class="form-table">
			<tr>
				<th><label><?php echo __( 'Pushbullet API URL', 'html-forms' ); ?> <span class="hf-required">*</span></label></th>
				<td>
					<input name="form[settings][actions][<?php echo $index; ?>][url]" value="<?php echo esc_attr( $settings['url'] ); ?>" type="text" class="regular-text" placeholder="http://...." required />
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Processes this action
	 *
	 * @param array $settings
	 * @param Submission $submission
	 * @param Form $form
	 */
	public function process( array $settings, Submission $submission, Form $form ) {
		// TODO: Send HTTP request to PushBullet API URL: $settings['url']
	}
}

$my_custom_action = new MyCustomAction();
$my_custom_action->hook();
