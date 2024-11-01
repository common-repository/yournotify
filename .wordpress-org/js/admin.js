/* global: YournotifyAdminVars */

/**
 * The Yournotify subscribe widget - admin area.
 */
(function ( $ ) {
	$( document ).on( 'click', '.js-connect-yournotify-api-key', function( event ) {
		event.preventDefault();

		var apiKey = $( this ).siblings( '.js-yournotify-api-key' ).val(),
			$noticeDiv = $( this ).parent().siblings( '.js-yournotify-notice' ),
			$mcListContainer = $( this ).parent().siblings( '.js-yournotify-list-container' ),
			$mcListSelect = $mcListContainer.find( 'select' ),
			$mcAccountId = $( this ).siblings( '.js-yournotify-account-id' ),
			$selectedList = $( this ).parent().siblings( '.js-yournotify-list-container' ).find( '.js-yournotify-selected-list' );

		// Abort, if the API key is not set.
		if ( apiKey.length === 0 ) {
			displayNotice( 'error', YournotifyAdminVars.text.no_api_key, $noticeDiv );

			return false;
		}

		$.ajax({
			url: YournotifyAdminVars.ajax_url,
			type: 'GET',
			dataType: 'json',
			data: {
				action:   'yournotify_subscribe_get_lists',
				security: YournotifyAdminVars.ajax_nonce,
				api_key:  apiKey,
			},
			beforeSend:  function() {
				$( '.js-yournotify-loader' ).show();

				// Clear notice.
				$noticeDiv.removeClass( 'updated error' ).text( '' );
			}
		})
			.done(function( response ) {
				if ( ! response.success ) {
					displayNotice( 'error', response.data.message, $noticeDiv );

					// Reset the select field and other settings.
					$mcListSelect.find( 'option' ).remove();
					$mcAccountId.val( '' );
					$selectedList.val( '' );
				}
				else {
					displayNotice( 'updated', response.data.message, $noticeDiv );

					// Reset the select field and other settings.
					$mcListSelect.find( 'option' ).remove();
					$mcAccountId.val( '' );

					// Add options to the select control.
					$.each( response.data.lists, function( key, value ) {
						$mcListSelect
							.append(
								$('<option>', { "value" : key } )
									.text(value)
							)
					} );

					// Set existing selected list.
					if ( $selectedList.val().length > 0 && $mcListSelect.find( 'option[value=' + $selectedList.val() + ']' ) ) {
						$mcListSelect.val( $selectedList.val() );
					}

					// Trigger the change event with the initialize set to true.
					$mcListSelect.trigger( 'change', true );

					// Display the select container.
					$mcListContainer.show();
				}
			})
			.fail(function() {
				displayNotice( 'error', YournotifyAdminVars.text.ajax_error, $noticeDiv );

				// Reset the select field and other settings.
				$mcListSelect.find( 'option' ).remove();
				$selectedList.val( '' );
			})
			.always(function() {
				$( '.js-yournotify-loader' ).hide();
			});
	} );

	$( document ).on( 'change', '.js-yournotify-list-container select', function( event, initialize ) {
		var $currentList = $( this ).siblings( '.js-yournotify-selected-list' );

		if ( initialize && 0 < $currentList.val().length ) {
			$( this ).val( $currentList.val() );
		}

		$currentList.val( $( this ).val() );

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
			.append(
				$('<p>')
					.text( message )
			);
	}
}( jQuery ));
