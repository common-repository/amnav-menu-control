;(function($) {

	$('.amnav_menu_logged_in_out_field').each(function(i){ 

		var $field = $(this);

		var id = $field.find('input.amnav-menu-id').val();

		// if set to display by role (aka is null) then show the roles list, otherwise hide
		if( $field.find('input.amnav-menu-logged-in-out:checked').val() === 'in' ){
			$field.next('.amnav_menu_control_field').show();
		} else {
			$field.next('.amnav_menu_control_field').hide();
		}
	});

	// on in/out/role change, hide/show the roles
	$('#menu-to-edit').on('change', 'input.amnav-menu-logged-in-out', function() {
		if( $(this).val() === 'in' ){
			$(this).parentsUntil('.amnav_menu_logged_in_out').next('.amnav_menu_control_field').slideDown();
		} else {
			$(this).parentsUntil('.amnav_menu_logged_in_out').next('.amnav_menu_control_field').slideUp();
		}
	});


})(jQuery);