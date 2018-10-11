(function($){
	// Initialize Cookies in no conflict mode.
	var $acf_intl_tel_input_cookies = Cookies.noConflict();
	
	/**
	*  initialize_field
	*
	*  This function will initialize the $field.
	*
	*  @date	30/11/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize_field( $field ) {
		// get input field
		var $telInput = $field.find('input');
		// set telInput options
		var $telInputOptions = {
       autoPlaceholder: "aggressive",
       hiddenInput: $telInput.attr('data-hiddenInput'),
			 separateDialCode: ( $telInput.attr('data-separateDialCode') == '1' ),
			 allowDropdown: ( $telInput.attr('data-allowDropdown') == '1' ),
			 initialCountry : $telInput.attr('data-initialCountry'),
			 preferredCountries : ( $telInput.attr('data-preferredCountries') != '' ) ? $telInput.attr('data-preferredCountries').split(',') : '',
			 onlyCountries : ( $telInput.attr('data-onlyCountries') != '' ) ? $telInput.attr('data-onlyCountries').split(',') : '',
			 excludeCountries : ( $telInput.attr('data-excludeCountries') != '' ) ? $telInput.attr('data-excludeCountries').split(',') : '',
      // placeholderNumberType: "MOBILE", 
    };
		// If initialCountry is set to Auto, we need to lookup user location
		if( $telInput.attr('data-initialCountry') == 'auto' ){
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
	
		/*
		*  ready & append (ACF5)
		*
		*  These two events are called when a field element is ready for initizliation.
		*  - ready: on page load similar to $(document).ready()
		*  - append: on new DOM elements appended via repeater field or other AJAX calls
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		acf.add_action('ready_field/type=intl_tel_input', initialize_field);
		acf.add_action('append_field/type=intl_tel_input', initialize_field);
	
	}

    /**
     * Apply the initialize_fields in Frontend acf_form
     */
    $('[data-type="intl_tel_input"]').each(function () {
        initialize_field($(this));
    });
	
})(jQuery);
