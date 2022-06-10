/* global whConfig, jQuery */

( function( $ ) {
	// Helpers.
	const $noticesArea = $( '.wh-notices-area' );
	const $todoInner = $( '.wh-todo-inner' );

	function printNotice( type, text ) {
		$noticesArea.append( '<div class="wh-notice wh-' + type + '">' + text + '</div>' );
	}

	function clearNotices() {
		$noticesArea.text( '' );
	}

	// Create,
	$( '.wh-todo-create' ).on( 'click', function( event ) {
		event.preventDefault();

		$.ajax( {
			type: 'POST',
			url: whConfig.adminAjaxUrl,
			data: {
				action: 'wh_todo_create',
				todo_nonce: whConfig.todoNonce,
			},
			success: ( response ) => {
				if ( response.success && response.data.updated ) {
					$( '.wh-todo-item-example' ).clone().appendTo( '.wh-todo-items' ).removeClass( 'wh-todo-item-example wh-hidden' ).addClass( 'wh-todo-item wh-status-actual' ).data( 'id', response.data.id ).attr( 'data-id', response.data.id );

					findItems();
				}
			},
			error: ( response ) => {
				printNotice( 'error', response.responseJSON.data );
			},
		} );
	} );

	findItems();

	function findItems() {
		$( '.wh-todo-item' ).each( function() {
			const $item = $( this );
			const id = $item.data( 'id' );

			// Change status.
			$item.find( '.wh-todo-done' ).on( 'click', function( event ) {
				event.preventDefault();

				const status = $( this ).data( 'status' );

				$todoInner.addClass( 'wh-loading' );
				clearNotices();

				$.ajax( {
					type: 'POST',
					url: whConfig.adminAjaxUrl,
					data: {
						action: 'wh_todo_change_status',
						status,
						id,
						todo_nonce: whConfig.todoNonce,
					},
					success: ( response ) => {
						if ( response.success && response.data.updated ) {
							$todoInner.removeClass( 'wh-loading' );

							if ( status === 'actual' ) {
								$item.removeClass( 'wh-status-done' ).addClass( 'wh-status-actual' );
								$item.find( 'input' ).prop( 'disabled', false );
							} else {
								$item.removeClass( 'wh-status-actual' ).addClass( 'wh-status-done' );
								$item.find( 'input' ).prop( 'disabled', true );
							}
						}
					},
					error: ( response ) => {
						printNotice( 'error', response.responseJSON.data );
					},
				} );
			} );

			// Delete.
			$item.find( '.wh-todo-delete' ).on( 'click', function( event ) {
				event.preventDefault();

				$todoInner.addClass( 'wh-loading' );
				clearNotices();

				$.ajax( {
					type: 'POST',
					url: whConfig.adminAjaxUrl,
					data: {
						action: 'wh_todo_delete',
						id,
						todo_nonce: whConfig.todoNonce,
					},
					success: ( response ) => {
						if ( response.success && response.data.updated ) {
							$item.remove();
							$todoInner.removeClass( 'wh-loading' );
						}
					},
					error: ( response ) => {
						printNotice( 'error', response.responseJSON.data );
					},
				} );
			} );

			// Update.
			$item.find( 'input' ).on( 'keyup', function( event ) {
				event.preventDefault();

				const text = $( this ).val();

				$todoInner.addClass( 'wh-loading' );
				clearNotices();

				$.ajax( {
					type: 'POST',
					url: whConfig.adminAjaxUrl,
					data: {
						action: 'wh_todo_update',
						id,
						text,
						todo_nonce: whConfig.todoNonce,
					},
					success: ( response ) => {
						if ( response.success && response.data.updated ) {
							$todoInner.removeClass( 'wh-loading' );
						}
					},
					error: ( response ) => {
						printNotice( 'error', response.responseJSON.data );
					},
				} );
			} );
		} );
	}
}( jQuery ) );
