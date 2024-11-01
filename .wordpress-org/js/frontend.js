/**
 * The Yournotify subscribe widget - frontend area.
 */
(function ( $ ) {
	$( document ).on( 'click', '.yournotify-subscribe__submit', function( event ) {
		event.preventDefault();

		var $noticeDiv = $( '.js-yournotify-frontend-notice' ),
			apiKey = $( '.yournotify-subscribe__key-input' ).val(),
			name = $( '.yournotify-subscribe__name-input' ).val(),
			email = $( '.yournotify-subscribe__email-input' ).val(),
			telephone = $( '.yournotify-subscribe__telephone-input' ).val(),
			lists = [Number($( '.yournotify-subscribe__list-input' ).val())],
			validate = 'yes';

		// Abort, if the API key is not set.
		if ( apiKey == '' ) {
			displayNotice( 'error', 'No API Key', $noticeDiv );

			return false;
		}

		if ( name == '' || email == '' ) {
			displayNotice( 'error', 'Name or Email field cannot be empty!', $noticeDiv );

			return false;
		}

		$.ajax({
			url: 'https://api.yournotify.com/contacts',
			type: 'POST',
			data: JSON.stringify({ name: name, email: email, telephone: telephone, lists: lists, validate: validate, subscribe: true }),
			contentType: "application/json",
			headers: {
				'Authorization': 'Bearer ' + apiKey,
			},
		})
			.done(function( response ) {
				if ( response.status == "failed" ) {
					displayNotice( 'error', response.status, $noticeDiv );
					$( '.yournotify-subscribe__name-input' ).val( '' );
					$( '.yournotify-subscribe__email-input' ).val( '' );
					$( '.yournotify-subscribe__telephone-input' ).val( '' );
				}
				else {
					displayNotice( 'updated', response.status, $noticeDiv );
					$( '.yournotify-subscribe__name-input' ).val( '' );
					$( '.yournotify-subscribe__email-input' ).val( '' );
					$( '.yournotify-subscribe__telephone-input' ).val( '' );
				}
			})
			.fail(function() {
				displayNotice( 'error', 'An error occurred with your request!', $noticeDiv );
			})
			.always(function() {
				$( '.js-yournotify-loader' ).hide();
			});
	} );

	/**
	 * Helper function to display the notice.
	 *
	 * @param type       Type of the notice ( 'error' or 'updated' ).
	 * @param message    The text message.
	 * @param $noticeDiv The jQuery element to append the message to.
	 */
	function displayNotice( type, message, $noticeDiv ) {
		$noticeDiv
			.removeClass( 'updated error' )
			.addClass( type )
			.text( '' )
			.append($('<span>').text( message ));
	}
}( jQuery ));
