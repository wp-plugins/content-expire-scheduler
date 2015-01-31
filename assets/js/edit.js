jQuery(document).ready(function($) {

	var previous = $('#rl-content-expire-scheduler-date').val();

	$('#rl-content-expire-scheduler-date').datepicker({
		dateFormat: 'yy-mm-dd'
	});

	$('#rl-content-expire-scheduler-edit, .rl-content-expire-scheduler-hide').click( function(e) {

		e.preventDefault();

		var date = $('#rl-content-expire-scheduler-date').val();

		if( $(this).hasClass('cancel' ) ) {

			$('#rl-content-expire-scheduler-date').val( previous );

		} else if( date ) {

			$('#rl-content-expire-scheduler-label').text( $('#rl-content-expire-scheduler-date').val() );

		}

		$('#rl-content-expire-scheduler-field').slideToggle();

	});

});