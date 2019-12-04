(function($){
	// Initialize Cookies in no conflict mode.
	var $acf_intl_tel_input_cookies = Cookies.noConflict();
	
	/**
	 * intl_initialize_field
	 *
	 * This function will initialize the $field.
	 *
	 * @date	30/11/17
	 * @since	5.6.5
	 *
	 * @param	n/a
	 * @return	n/a
	 */
	
	function intl_initialize_field( $field ) {
		// get input field
		var $telInput = $field.find('input');
		// set telInput options
		var $telInputOptions = {
			autoPlaceholder: "aggressive",
			hiddenInput: $telInput.data('hiddeninput'),
			separateDialCode: ( $telInput.data('separatedialcode') == '1' ),
			allowDropdown: ( $telInput.data('allowdropdown') == '1' ),
			initialCountry : $telInput.data('initialcountry'),
			preferredCountries : ( $telInput.data('preferredcountries') != '' ) ? $telInput.data('preferredcountries').split(',') : '',
			onlyCountries : ( $telInput.data('onlycountries') != '' ) ? $telInput.data('onlycountries').split(',') : '',
			excludeCountries : ( $telInput.data('excludecountries') != '' ) ? $telInput.data('excludecountries').split(',') : '',
			// placeholderNumberType: "MOBILE", 
		};
		// If initialCountry is set to Auto, we need to lookup user location
		if( $telInput.data('initialcountry') == 'auto' ){
			$telInputOptions.geoIpLookup = function(callback) {
				// Check if we have user location saved in cookies
				var $countryCodeCookie = $acf_intl_tel_input_cookies.get( 'acf_intl_tel_input_countryCode' );
				if( $countryCodeCookie === undefined ){
					// If Location is not saved, let's get it!
					$.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
						var countryCode = (resp && resp.country) ? resp.country : "";
						// Save location to Cookies
						$acf_intl_tel_input_cookies.set( 'acf_intl_tel_input_countryCode', countryCode, { expires : 7, path : acf_intl_tel_input_obj.COOKIEPATH, domain : acf_intl_tel_input_obj.COOKIE_DOMAIN } );
						// Execute the callback
						callback(countryCode);
					});
				} else{
					// Execute the callback
					callback($countryCodeCookie);
				}
			};
		}
		// Make input render the mask when user focus out
		$telInput.on('blur', function(){
			if( $telInput.intlTelInput("isValidNumber") ){
				$telInput.intlTelInput("setNumber", $telInput.intlTelInput( 'getNumber' ) );
				$telInput.prev('input[type="hidden"]').val($telInput.intlTelInput('getNumber'));
			} else { // Clear input if number is not valid
				$telInput.val('');
				$telInput.prev('input[type="hidden"]').val('');
			}
		})
		// Initialize Input	
		.intlTelInput( $telInputOptions );
	}
	
	
	if( typeof acf.add_action !== 'undefined' ) {
	
		/**
		 * ready & append (ACF5)
		 *
		 * These two events are called when a field element is ready for initizliation.
		 * - ready: on page load similar to $(document).ready()
		 * - append: on new DOM elements appended via repeater field or other AJAX calls
		 *
		 * @param	n/a
		 * @return	n/a
		 */
		
		acf.add_action('ready_field/type=intl_tel_input', intl_initialize_field);
		acf.add_action('append_field/type=intl_tel_input', intl_initialize_field);
	
	}

		/**
		 * Apply intl_initialize_field in Frontend acf_form
		 */
		if(! $('body').hasClass('wp-admin')) {
			$('[data-type="intl_tel_input"]').each(function () {
					intl_initialize_field($(this));
			});
		}
	
})(jQuery);
