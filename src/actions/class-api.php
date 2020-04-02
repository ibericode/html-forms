<?php

namespace HTML_Forms\Actions;

use HTML_Forms\Form;
use HTML_Forms\Submission;

class Api extends Action {

	public $type  = 'api';
	public $label = 'Api';

	public function __construct() {
		$this->label = __( 'GET/POST to API', 'html-forms' );
	}

	/**
	* @return array
	*/
	private function get_default_settings() {
		$defaults = array(
			'method'       => 'GET',
			'url'          => '',
			'content_type' => 'text/html',
			'cenvert'      => '',
		);
		return $defaults;
	}

	/**
	* @param array $settings
	* @param string|int $index
	*/
	public function page_settings( $settings, $index ) {
		$settings = array_merge( $this->get_default_settings(), $settings );
		?>
	   <span class="hf-action-summary"><?php printf( 'Method %s. URL %s.', $settings['method'], $settings['url'] ); ?></span>
	   <input type="hidden" name="form[settings][actions][<?php echo $index; ?>][type]" value="<?php echo $this->type; ?>" />
	   <table class="form-table">
		   <tr>
			   <th><label><?php echo __( 'Method', 'html-forms' ); ?> <span class="hf-required">*</span></label></th>
			   <td>
				   <select name="form[settings][actions][<?php echo $index; ?>][method]" required>
					  <option <?php selected( $settings['method'], 'GET' ); ?>>GET</option>
					  <option <?php selected( $settings['method'], 'POST' ); ?>>POST</option>
					</select>
			   </td>
		   </tr>
		   <tr>
			   <th><label><?php echo __( 'URL', 'html-forms' ); ?> <span class="hf-required">*</span></label></th>
			   <td>
				   <input name="form[settings][actions][<?php echo $index; ?>][url]" value="<?php echo esc_attr( $settings['url'] ); ?>" type="text" class="regular-text" placeholder="http://dummy.url.org" required />
			   </td>
		   </tr>
		   <tr>
			   <th><label><?php echo __( 'Content Type', 'html-forms' ); ?> <span class="hf-required">*</span></label></th>
			   <td>
				   <select name="form[settings][actions][<?php echo $index; ?>][content_type]" required>
					  <option <?php selected( $settings['content_type'], 'application/x-www-form-urlencoded' ); ?>>application/x-www-form-urlencoded</option>
					  <option <?php selected( $settings['content_type'], 'application/json' ); ?>>application/json</option>
				   </select>
			   </td>
		   </tr>
		   <tr>
			   <th><label><?php echo __( 'Convert arrays', 'html-forms' ); ?></label></th>
			   <td>
				   <input name="form[settings][actions][<?php echo $index; ?>][convert]" value="on" <?php echo ( $settings['convert'] == 'on' ) ? 'checked' : ''; ?> type="checkbox" />
				   <span> i.e. encode <code>value[0]=1&amp;value[1]=2</code> as <code>value=1,2</code></span>
			   </td>
		   </tr>
		   <tr>
			   <th><label><?php echo __( 'Additional headers', 'html-forms' ); ?></label></th>
			   <td>
				   <textarea name="form[settings][actions][<?php echo $index; ?>][headers]" rows="4" class="widefat" placeholder="<?php echo esc_attr( 'SomeToken: 1234' ); ?>"><?php echo esc_textarea( $settings['headers'] ); ?></textarea>
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

		if ( empty( $settings['method'] ) || empty( $settings['url'] ) || empty( $settings['content_type'] ) ) {
			return false;
		}
		if ( ! empty( $settings['convert'] ) ) {
			foreach ( $submission->data as $name => $value ) {
				if ( is_array( $value ) ) {
					$submission->data[ $name ] = implode( ',', array_filter( $value ) );
				}
			}
		}
		$curl = curl_init( $settings['url'] );
		if ( $settings['method'] == 'POST' ) {
			curl_setopt( $curl, CURLOPT_POST, true );
		}
		switch ( $settings['content_type'] ) {
			case 'application/x-www-form-urlencoded':
				$postdata = http_build_query( $submission->data );
				break;
			case 'application/json':
				$postdata = json_encode( $submission->data );
				break;
		}
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $postdata );
		$headers = array(
			'Content-Type: ' . $settings['content_type'],
			'Content-Length: ' . strlen( $postdata ),
		);
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
		if ( ! empty( $settings['headers'] ) ) {
			$headers = explode( PHP_EOL, hf_replace_data_variables( $settings['headers'], $submission->data, 'strip_tags' ) );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
		}
		curl_setopt( $curl, CURLOPT_HEADER, true );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		$response = curl_exec( $curl );
		$info = curl_getinfo( $curl );
		$result = array(
			'url'      => $settings['url'],
			'method'   => $settings['method'],
			'response' => $info['http_code'],
			'body'     => substr( $response, $info['header_size'] ),
		);
		$submission->actions[] = array(
			'type'      => $this->type,
			'result'    => $result,
		);
		curl_close( $curl );
		return $info['http_code'] == 200;
	}

}
