<?php
if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

/**
 * Class to create a custom text editor control
 */
class Estore_Text_Editor_Control extends WP_Customize_Control {

	/**
	 * Control's Type.
	 *
	 * @var string
	 */

	public $type = 'estore-editor';

	/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 */
	public function to_json() {
		parent::to_json();

		$this->json['default'] = $this->setting->default;
		if ( isset( $this->default ) ) {
			$this->json['default'] = $this->default;
		}
		$this->json['value'] = $this->value();

		$this->json['link']        = $this->get_link();
		$this->json['id']          = $this->id;
		$this->json['label']       = esc_html( $this->label );
		$this->json['description'] = $this->description;

	}

		/**
		 * An Underscore (JS) template for this control's content (but not its container).
		 *
		 * Class variables for this control class are available in the `data` JS object;
		 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
		 *
		 * @see    WP_Customize_Control::print_template()
		 *
		 * @access protected
		 */
	protected function content_template() {
		?>
			<# var editorID = data.id.replace( '[', '-' ).replace( ']', '' ) #>

			<div class="customizer-text">
				<# if ( data.label ) { #>
				<span class="customize-control-title">{{{ data.label }}}</span>
				<# } #>

				<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
				<# } #>
			</div>

			<textarea id="editor_{{{ editorID }}}" {{{ data.link }}}>{{ data.value }}</textarea>

		<?php
	}

		/**
		 * Don't render the control content from PHP, as it's rendered via JS on load.
		 */
	public function render_content() {
	}

}
