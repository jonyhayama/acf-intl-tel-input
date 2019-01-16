<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('jony_acf_field_intl_tel_input') ) :


class jony_acf_field_intl_tel_input extends acf_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct( $settings ) {
		
		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/
		
		$this->name = 'intl_tel_input';
		
		
		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/
		
		$this->label = __('International Telephone Input', 'acf-intl-tel-input');
		
		
		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/
		
		$this->category = 'jquery';
		
		
		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/
		
		$this->defaults = array(
			'separateDialCode' => false,
			'allowDropdown' => true,
			'excludeCountries' => '',
			'onlyCountries' => '',
			'preferredCountries' => '',
			'initialCountry' => 'auto'
		);
		
		
		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('intl_tel_input', 'error');
		*/
		
		$this->l10n = array(
			'error'	=> __('Error! Please enter a higher value', 'acf-intl-tel-input'),
		);
		
		
		/*
		*  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
		*/
		
		$this->settings = $settings;
		
		
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field_settings( $field ) {
		
		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/
		
// 		$this->defaults = array(
// 			'nationalMode'	=> true,
// 			'separateDialCode' => false,
// 			'allowDropdown' => true,
// 			'excludeCountries' => '',
// 			'onlyCountries' => '',
// 			'preferredCountries' => '',
// 			'initialCountry' => 'auto'
// 		);
		
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Separate Dial Code', 'acf-intl-tel-input'),
			'instructions'	=> '',
			'type'			=> 'true_false',
			'name'			=> 'separateDialCode',
			'ui'			  => 1
		) );
		
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Allow Drop Down', 'acf-intl-tel-input'),
			'instructions'	=> '',
			'type'			=> 'true_false',
			'name'			=> 'allowDropdown',
			'ui'			  => 1
		) );
		$countryCodeLink = '<a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2">ISO 3166-1 alpha-2</a>';
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Initial Country','acf-intl-tel-input' ),
			'instructions'	=> sprintf( __( 'Use "auto" to display user country (geo location based) or enter a country code (%s)', 'acf-intl-tel-input' ), $countryCodeLink ),
			'type'			=> 'text',
			'name'			=> 'initialCountry',
		));
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Exclude Countries','acf-intl-tel-input'),
			'instructions'	=> sprintf( __( 'Comma separated list of country codes (%s)', 'acf-intl-tel-input' ), $countryCodeLink ),
			'type'			=> 'textarea',
			'name'			=> 'excludeCountries',
			'rows'			=> 2
		));
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Only Countries','acf-intl-tel-input'),
			'instructions'	=> sprintf( __( 'Comma separated list of country codes (%s)', 'acf-intl-tel-input' ), $countryCodeLink ),
			'type'			=> 'textarea',
			'name'			=> 'onlyCountries',
			'rows'			=> 2
		));
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Preferred Countries','acf-intl-tel-input'),
			'instructions'	=> sprintf( __( 'Comma separated list of country codes (%s)', 'acf-intl-tel-input' ), $countryCodeLink ),
			'type'			=> 'textarea',
			'name'			=> 'preferredCountries',
			'rows'			=> 2
		));

	}
	
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field( $field ) {
		$attr[] = 'data-hiddenInput="' . esc_attr($field['name']) . '"';
		foreach( $this->defaults as $key => $value ){
			$value = $field[$key];
			switch( $key ){
				case 'preferredCountries':
				case 'excludeCountries':
				case 'onlyCountries':
					$value = str_replace(' ', '', $value );
					$value = str_replace(' ', '', apply_filters( "jony-acf-intl-tel-input/render_field/$key", $value, $field ) );
					break;
			}
			$attr[] = 'data-' . $key .'="' . $value . '"';
		}
		$attr = implode( ' ', $attr );
		
                ?><input type="tel" value="<?php echo esc_attr($field['value']) ?>" <?php echo $attr; ?> <?php echo $field['required'] ? 'required="required"' : ''; ?>><?php
	}
	
		
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/**/
	
	function input_admin_enqueue_scripts() {
		
		// vars
		$url = $this->settings['url'];
		$version = $this->settings['version'];
		$intlTelInputVersion = '12.1.0';
		$jsCookieVersion = '2.2.0';
		
		
		// register & include JS
		wp_register_script('intl-tel-input', "{$url}assets/intl-tel-input/js/intlTelInput.min.js", array('jquery'), $intlTelInputVersion, true);
		wp_register_script('intl-tel-input-util', "{$url}assets/intl-tel-input/js/utils.js", array('jquery'), $intlTelInputVersion, true);
		wp_register_script('js-cookie', "{$url}assets/js/js.cookie.js", array(), $jsCookieVersion, true);
		wp_register_script('acf-intl-tel-input', "{$url}assets/js/input.js", array('acf-input', 'jquery', 'intl-tel-input', 'intl-tel-input-util', 'js-cookie'), $version, true);
		wp_localize_script('acf-intl-tel-input', 'acf_intl_tel_input_obj', array(
			'COOKIEPATH' => COOKIEPATH,
			'COOKIE_DOMAIN' => COOKIE_DOMAIN,
		) );
		wp_enqueue_script('acf-intl-tel-input');
		
		
		// register & include CSS
		wp_register_style('intl-tel-input', "{$url}assets/intl-tel-input/css/intlTelInput.css", array(), $intlTelInputVersion);
		wp_register_style('acf-intl-tel-input', "{$url}assets/css/input.css", array('acf-input', 'intl-tel-input'), $version);
		wp_enqueue_style('acf-intl-tel-input');
		
	}
	
	
	
	
	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
		
	function input_admin_head() {
	
		
		
	}
	
	*/
	
	
	/*
   	*  input_form_data()
   	*
   	*  This function is called once on the 'input' page between the head and footer
   	*  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and 
   	*  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
   	*  seen on comments / user edit forms on the front end. This function will always be called, and includes
   	*  $args that related to the current screen such as $args['post_id']
   	*
   	*  @type	function
   	*  @date	6/03/2014
   	*  @since	5.0.0
   	*
   	*  @param	$args (array)
   	*  @return	n/a
   	*/
   	
   	/*
   	
   	function input_form_data( $args ) {
	   	
		
	
   	}
   	
   	*/
	
	
	/*
	*  input_admin_footer()
	*
	*  This action is called in the admin_footer action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_footer)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
		
	function input_admin_footer() {
	
		
		
	}
	
	*/
	
	
	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
	
	function field_group_admin_enqueue_scripts() {
		
	}
	
	*/

	
	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
	
	function field_group_admin_head() {
	
	}
	
	*/


	/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	
	/*
	
	function load_value( $value, $post_id, $field ) {
		
		return $value;
		
	}
	
	*/
	
	
	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is saved in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	
	/*
	
	function update_value( $value, $post_id, $field ) {
		
		return $value;
		
	}
	
	*/
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
		
	/*
	
	function format_value( $value, $post_id, $field ) {
		
		// bail early if no value
		if( empty($value) ) {
		
			return $value;
			
		}
		
		
		// apply setting
		if( $field['font_size'] > 12 ) { 
			
			// format the value
			// $value = 'something';
		
		}
		
		
		// return
		return $value;
	}
	
	*/
	
	
	/*
	*  validate_value()
	*
	*  This filter is used to perform validation on the value prior to saving.
	*  All values are validated regardless of the field's required setting. This allows you to validate and return
	*  messages to the user if the value is not correct
	*
	*  @type	filter
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$valid (boolean) validation status based on the value and the field's required setting
	*  @param	$value (mixed) the $_POST value
	*  @param	$field (array) the field array holding all the field options
	*  @param	$input (string) the corresponding input name for $_POST value
	*  @return	$valid
	*/
	
	/*
	
	function validate_value( $valid, $value, $field, $input ){
		
		// Basic usage
		if( $value < $field['custom_minimum_setting'] )
		{
			$valid = false;
		}
		
		
		// Advanced usage
		if( $value < $field['custom_minimum_setting'] )
		{
			$valid = __('The value is too little!','acf-intl-tel-input'),
		}
		
		
		// return
		return $valid;
		
	}
	
	*/
	
	
	/*
	*  delete_value()
	*
	*  This action is fired after a value has been deleted from the db.
	*  Please note that saving a blank value is treated as an update, not a delete
	*
	*  @type	action
	*  @date	6/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (mixed) the $post_id from which the value was deleted
	*  @param	$key (string) the $meta_key which the value was deleted
	*  @return	n/a
	*/
	
	/*
	
	function delete_value( $post_id, $key ) {
		
		
		
	}
	
	*/
	
	
	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0	
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/
	
	/*
	
	function load_field( $field ) {
		
		return $field;
		
	}	
	
	*/
	
	
	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/
	
	/*
	
	function update_field( $field ) {
		
		return $field;
		
	}	
	
	*/
	
	
	/*
	*  delete_field()
	*
	*  This action is fired after a field is deleted from the database
	*
	*  @type	action
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	n/a
	*/
	
	/*
	
	function delete_field( $field ) {
		
		
		
	}	
	
	*/
	
	
}


// initialize
new jony_acf_field_intl_tel_input( $this->settings );


// class_exists check
endif;

?>
