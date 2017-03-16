( function($, undef) {
	// html stuff
	var _before = '<a tabindex="0" class="insta-color-result" />',
		_after = '<div class="insta-picker-holder" />',
		_wrap = '<div class="insta-picker-container" />',
		_button = '<input type="button" class="btn btn-small hidden insta-picker-btn" />';
		
	// jQuery UI Widget constructor
	var ColorPicker = {
		options: {
			defaultColor: false,
			change: false,
			clear: false,
			hide: true,
			palettes: true
		},
		_create: function() {
			// bail early for unsupported Iris.
			if ( !$.support.iris )
				return;
			var self = this;
			var el = self.element;

			$.extend( self.options, el.data() );

			self.initialValue = el.val();

			// Set up HTML structure, hide things
			el.addClass( 'insta-color-picker' ).hide().wrap( _wrap );
			self.wrap = el.parent();
			self.toggler = $( _before ).insertBefore( el ).css( { backgroundColor: self.initialValue } ).attr("title", 'Select Color').attr("data-current", 'Current Color');
			self.pickerContainer = $( _after ).insertAfter( el );
			self.button = $( _button );

			if ( self.options.defaultColor )
				self.button.addClass( 'insta-picker-default' ).val('Default');
			else
				self.button.addClass( 'insta-picker-clear' ).val('Clear');

			el.wrap('<span class="insta-picker-input-wrap" />').after(self.button);

			el.iris( {
				target: self.pickerContainer,
				hide: true,
				width: 200,
				mode: 'hsl',
				palettes: self.options.palettes,
				change: function( event, ui ) {
					self.toggler.css( { backgroundColor: ui.color.toString() } );
					// check for a custom cb
					if ( $.isFunction( self.options.change ) )
						self.options.change.call( this, event, ui );
				}
			} );
			el.val( self.initialValue );
			self._addListeners();
			if ( ! self.options.hide )
				self.toggler.click();
		},
		_addListeners: function() {
			var self = this;

			self.toggler.click( function( event ){
				event.stopPropagation();
				self.element.toggle().iris( 'toggle' );
				self.button.toggleClass('hidden');
				self.toggler.toggleClass( 'insta-picker-open' );

				// close picker when you click outside it
				if ( self.toggler.hasClass( 'insta-picker-open' ) )
					$( "body" ).on( 'click', { wrap: self.wrap, toggler: self.toggler }, self._bodyListener );
				else
					$( "body" ).off( 'click', self._bodyListener );
			});

			self.element.change(function( event ) {
				var me = $(this),
					val = me.val();
				// Empty = clear
				if ( val === '' || val === '#' ) {
					self.toggler.css('backgroundColor', '');
					// fire clear callback if we have one
					if ( $.isFunction( self.options.clear ) )
						self.options.clear.call( this, event );
				}
			});

			// open a keyboard-focused closed picker with space or enter
			self.toggler.on('keyup', function( e ) {
				if ( e.keyCode === 13 || e.keyCode === 32 ) {
					e.preventDefault();
					self.toggler.trigger('click').next().focus();
				}
			});

			self.button.click( function( event ) {
				var me = $(this);
				if ( me.hasClass( 'insta-picker-clear' ) ) {
					self.element.val( '' );
					self.toggler.css('backgroundColor', '');
					if ( $.isFunction( self.options.clear ) )
						self.options.clear.call( this, event );
				} else if ( me.hasClass( 'insta-picker-default' ) ) {
					self.element.val( self.options.defaultColor ).change();
				}
			});
		},
		_bodyListener: function( event ) {
			if ( ! event.data.wrap.find( event.target ).length )
					event.data.toggler.click();
		},
		// $("#input").instaColorPicker('color') returns the current color
		// $("#input").instaColorPicker('color', '#bada55') to set
		color: function( newColor ) {
			if ( newColor === undef )
				return this.element.iris( "option", "color" );

			this.element.iris( "option", "color", newColor );
		},
		//$("#input").instaColorPicker('defaultColor') returns the current default color
		//$("#input").instaColorPicker('defaultColor', newDefaultColor) to set
		defaultColor: function( newDefaultColor ) {
			if ( newDefaultColor === undef )
				return this.options.defaultColor;

			this.options.defaultColor = newDefaultColor;
		}
	}
	$.widget( 'insta.instaColorPicker', ColorPicker );
}(jQuery));
