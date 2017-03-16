var $is_drag = 0, 
over_state = 0,
current_img,
insta_file_frame,
frame_data,
onpop = 0,
edit_state = 0;
;(function($) {
	var elcount = $('.insta-content-el').length,
	contentWidth = $('.entry-content').width(),
	old_disable_comment = $('#edit-disable-comment').is(":checked") ? 'yes' : 'no',
	elboxrez = 0;

	var stack = new Undo.Stack(),
	EditCommand = Undo.Command.extend({
		constructor: function(textarea, oldValue, newValue) {
			this.textarea = textarea;
			this.oldValue = oldValue;
			this.newValue = newValue;
		},
		execute: function() {
		},
		undo: function() {
			_insta_destroy();
			this.textarea.html(this.oldValue);
			_insta_init();
		},
		
		redo: function() {
			_insta_destroy();
			this.textarea.html(this.newValue);
			_insta_init();
		}
	});
	stack.changed = function() {
		stackUI();
	};
	
	// Initialize
	_insta_init();
	
	// Update Element Number 
	$('#insta-current-num').val(elcount);
	
	// Initialize Drag & Drop Elements
	$(".ite-element").draggable({
		connectToSortable: ".insta-sortable",
		helper: "clone",
		start: function(event, ui) {
			$is_drag = 1;
		}
	});
	
	$(".graphic-item").draggable({
		connectToSortable: ".insta-sortable",
		helper: "clone",
		start: function(event, ui) {
			$is_drag = 1;
		}
	});
	
	// Make elements placeholder draggable
	$(".insta-editor-tools").draggable({ handle: '.ite-tools-header' });
	
	// Load Title Editor
	$("#edit-title-text").jqte({
		br: true,
		p: false,
		format:false,
		indent: false,
		link: false,
		ol: false,
		outdent:false,
		rule: false,
		unlink: false,
		ul:false,
		sub: false,
		strike: false,
		sup: false,
		source: false
	});
	
	// Make Element Settings Modal Draggable
	$(".modal").draggable({handle: '.modal-header'});
	
	// Load Color Pickers
	$('#edit-box-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5']
	});
	
	$('#edit-box-border-color').instaColorPicker({
	    hide: true,
	    palettes: ['#c09853', '#b94a48', '#468847', '#3a87ad', '#ccc', '#e5e5e5']
	});
	
	$('#edit-divider-color').instaColorPicker({
	    hide: true,
	    palettes: true
	});
	
	$('#edit-caption-box-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5']
	});
	
	$('#edit-caption-text-color').instaColorPicker({
	    hide: true,
	    palettes: ['#c09853', '#b94a48', '#468847', '#3a87ad', '#ccc', '#e5e5e5']
	});
	
	$('#edit-caption-border-color').instaColorPicker({
	    hide: true,
	    palettes: ['#c09853', '#b94a48', '#468847', '#3a87ad', '#ccc', '#e5e5e5']
	});
	
	$('#edit-stroke-color').instaColorPicker({
	    hide: true,
	    palettes: true
	});
	
	$('#edit-shadow-color').instaColorPicker({
	    hide: true,
	    palettes: true
	});
	
	$('#edit-cusbtn-text-color').instaColorPicker({
	    hide: true,
	    palettes: true
	});
	
	$('#edit-cusbtn-back-color').instaColorPicker({
	    hide: true,
	    palettes: true
	});
	
	$('#edit-cusbtn-border-color').instaColorPicker({
	    hide: true,
	    palettes: true
	});
	
	// Switch
	$('.insta-switch').each(function(){
		$(this).click(function(e){
			e.preventDefault();
			var $this = $(this), mode = $this.data('switch');
			if ( mode == 'on' ) {
				$this.addClass('btn-success active');
				$this.removeClass('btn-default');
				$this.parent().find('.insta-switch-off').removeClass('btn-danger active');
				$this.parent().find('.insta-switch-off').addClass('btn-default');
				$this.parent().find('.insta-switch-val').attr('checked', true);
			} else if ( mode == 'off' ) {
				$this.addClass('btn-danger active');
				$this.removeClass('btn-default');
				$this.parent().find('.insta-switch-on').removeClass('btn-success active');
				$this.parent().find('.insta-switch-on').addClass('btn-default');
				$this.parent().find('.insta-switch-val').removeAttr('checked');
			}
		});
	});
	
	function insta_switch_state( element, state ) {
		if ( state == 'on' ) {
			element.parent().find('.insta-switch-on').removeClass('btn-default');
			element.parent().find('.insta-switch-on').addClass('btn-success active');
			element.parent().find('.insta-switch-off').removeClass('btn-danger active');
			element.parent().find('.insta-switch-off').addClass('btn-default');
		} else if ( state == 'off' ) {
			element.parent().find('.insta-switch-on').addClass('btn-default');
			element.parent().find('.insta-switch-on').removeClass('btn-success active');
			element.parent().find('.insta-switch-off').addClass('btn-danger active');
			element.parent().find('.insta-switch-off').removeClass('btn-default');
		}
	}
	
	// Sliders
	$("#divider-width-slider").slider({
		min: 50,
		max: 100,
		slide: function( event, ui ) {
			$('#edit-divider-width').val(ui.value);
		}
	});
	
	$("#divider-thickness-slider").slider({
		min: 1,
		max: 20,
		slide: function( event, ui ) {
			$('#edit-divider-thick').val(ui.value);
		}
	});
	
	$("#box-width-slider").slider({
		min: 10,
		max: $('.entry-content').innerWidth(),
		slide: function( event, ui ) {
			$('#edit-box-width').val(ui.value);
		}
	});
	
	
	
	$("#border-thickness-slider").slider({
		min: 1,
		max: 20,
		slide: function( event, ui ) {
			$('#edit-box-border-thick').val(ui.value);
		}
	});
	
	$("#box-radius-slider").slider({
		min: 1,
		max: 50,
		slide: function( event, ui ) {
			$('#edit-box-border-radius').val(ui.value);
		}
	});
	
	$('body').on("keyup", ".it-number-val", function(){
		var slider_id = $(this).data('sliderLink'), 
		cur_val = $(this).val(),
		new_val = $("#" + slider_id).slider("option", "min"),
		max_val = $("#" + slider_id).slider("option", "max");
		
		if ( !$.isNumeric(cur_val) ) {
			cur_val = $("#" + slider_id).slider("option", "value");
			$(this).val(cur_val);
		}
		
		if ( cur_val >= 10 && cur_val <= max_val )
			new_val = $(this).val();
			
		if ( cur_val > max_val ) {
			new_val = max_val;
			$(this).val(max_val);
		}
		
		$("#" + slider_id).slider("option", "value", new_val);
	});
	
	// Image resizeable...
	$('body').on('click', '.insta-el-img-edit', function(e){
		var $this = jQuery(this);
		$this.parent().find('img').focus();
		$this.parent().find('img').select();
	});
	
	$('body').on('click', "span[contenteditable*='true']", function(){
		$this.parent().find('img').focus();
		$this.parent().find('img').select();
	});
	
	// Page Settings Buttons 
	$('body').on('click', '.insta-page-settings', function(e){
		onpop = 1;
		$('#ite-page-settings-modal').modal({
			backdrop: false,
			show: true
		});
	});
	
	$('body').on('keyup blur', '#edit-page-title', function(e){
		$('.entry-title').text($(this).val());
	});
	
	$('body').on('click', '.insta-pg-title', function(e){
		if ( $(this).data('switch') == 'on' )
			$('.entry-title').hide();
		else
			$('.entry-title').show();
	});
	
	$('body').on('click', '.insta-cm-dis', function(e){
		if ( $(this).data('switch') == 'on' )
			$('#comment-wrap').hide();
		else
			$('#comment-wrap').show();
	});
	
	// Graphics Buttons 
	$('body').on('click', '.insta-insert-graphics', function(e){
		onpop = 1;
		$('#ite-graphics-modal').modal({
			backdrop: false,
			show: true
		});
	});
	
	$('body').on('click', '.graphic-selector', function(e){
		e.preventDefault();
		var $this = $(this), selector = $this.data('graphic'),
		currentId = $('#graphics-area').find('.insta-graphic-lists').attr('id');

		$('#graphic-drop-down').text(selector);
		if ( $('#insta-graphics-' + selector).length ) {
			$('#' + currentId).appendTo('#graphics-hidden');
			$('#insta-graphics-' + selector).appendTo('#graphics-area');
		} else {
			$('#' + currentId).clone().appendTo('#graphics-hidden');
			$('#graphics-area').html('<p class="muted">Loading...</p>');
			$.post(insta_ajaxurl, { action: 'get_graphics', dir: selector}, function(response){
				$('#graphics-area').html(response);
				
				$(".graphic-item")
					.unbind('draggable')
					.draggable({
						connectToSortable: ".insta-sortable",
						helper: "clone",
						start: function(event, ui) {
							$is_drag = 1;
						}
					});
			});
		}
	});
	
	// Disable some functionalities
	$('body').on('click', 'a', function(e){
		var $this = $(this), cls = $this.attr('class');
		if ( !$this.hasClass('insta-element-edit')
			&& !$this.hasClass('insta-el-img-link')
			&& !$this.hasClass('insta-btn-link')
			&& !$this.hasClass('insta-exit-editor')
			&& onpop != 1 
			&& $this.attr('target') != '_blank'
		) {
			e.preventDefault();
			alert('Link is disable in editor mode.');
		}
	});
	
	$('#commentform').submit(function(e){
		alert('Comment form submission is disabled in editor mode.');
		e.preventDefault();
	});
	
	// Element Mouse Over
	$('body').on('mouseover', '.insta-content-el', function(e){
		var $this = $(this), has_parent = false,
		element, columns = $this.parent().parent(),
		boxes = $this.parent().parent().parent(), is_box = false;
		
		if ( columns.hasClass('insta-col-placeholder') || boxes.hasClass('insta-box-placeholder') ) {
			over_state = 0;
			has_parent = true;
		}
		
		is_box = boxes.hasClass('insta-box-placeholder') ? true : false;
		
		if ( over_state == 0 ) {
			$this.addClass('insta-element-highlight');
			if ( !$('#' + $this.attr('id') + '-close').length )
				$this.append('<div id="' + $this.attr('id') + '-close" class="insta-content-el-close"><i class="fa fa-times"></i></div>');
			
			if ( !$('#' + $this.attr('id') + '-edit').length && $this.data('el') != 'fbcomm' )
				$this.append('<div id="' + $this.attr('id') + '-edit" class="insta-content-el-edit"><button type="button" class="btn btn-default insta-element-edit" title="Edit This Element"><i class="fa fa-edit"></i></button>&nbsp;&nbsp;<button type="button" class="btn btn-warning insta-element-clone" title="Clone This Element"><i class="fa fa-copy"></i></button></div>');
					
			if ( !$('#' + $this.attr('id') + '-handle').length ) {
				$this.append('<div id="' + $this.attr('id') + '-handle" class="insta-content-el-handle">::::</div>');
				var el_w = $this.width(), handle_l = (el_w / 2) - 25;
				$('#' + $this.attr('id') + '-handle').css('left', handle_l + 'px');
			}
			
			if ( has_parent ) {
				element = is_box ? boxes : columns;

				$('#' + element.attr('id')).removeClass('insta-element-highlight');
				$('#' + element.attr('id') + '-close').remove();
				$('#' + element.attr('id') + '-edit').remove();
				$('#' + element.attr('id') + '-handle').remove();
			}
			over_state = 1;
		}
		e.stopPropagation();
	});
	
	// Element Mouse Leave
	$('body').on('mouseleave', '.insta-content-el', function(e){
		var $this = $(this);
		$this.removeClass('insta-element-highlight');
		$this.find('.insta-content-el-close').remove();
		$this.find('.insta-content-el-edit').remove();
		$this.find('.insta-content-el-handle').remove();
		over_state = 0;
	});
	
	// Confirmation to remove element...
	$('body').delegate('.insta-content-el-close', 'click', function(e){
		var $this = $(this), elementID = $this.parent().attr('id');
		$('#insta-editor-element-id').val(elementID);
		$('#ite-close-modal').modal('show');
		e.preventDefault();
	});
	
	// Remove element...
	$('body').delegate('.insta-remove-element', 'click', function(e){
		e.preventDefault();
		var elementID = $('#insta-editor-element-id').val();
		$('#' + elementID).remove();
		$('#ite-close-modal').modal('hide');
	});
	
	// Close Element Settings Modal
	$('body').delegate('.insta-edit-close', 'click', function(e){
		e.preventDefault();
		var $id = $(this).parent().parent().parent().parent().attr('id');
		onpop = 0;
		$('#' + $id).modal('hide');
	});
	
	$('.modal').on('hidden', function(){
    	onpop = 0;
	});
	
	// ===============================================
	// OPEN ELEMENT SETTINGS / CLONE ELEMENT
	// ===============================================
	$('body').delegate('.insta-element-clone', 'click', function(e){
		var $this = $(this);
		elementID = $this.parent().parent().attr('id'),
		cloned = $('#' + elementID);
		cloned.clone().insertAfter(cloned);
		
		var cur_num = $('#insta-current-num').val(),
		new_num = parseInt(cur_num) + 1;
		
		var newElementId = 'insta-el-' + insta_id + new_num;
		do {
			new_num++;
			newElementId = 'insta-el-' + insta_id + new_num;
		} while ( $('#' + newElementId).length );
		
		$('#insta-current-num').val(new_num);
			
		cloned.after().attr('id', newElementId);
	});
	
	$('body').delegate('.insta-element-edit, .insta-btn-link', 'click', function(e){
		e.preventDefault();
		var $this = $(this),
		type = $this.parent().parent().data('el'),
		elementID = $this.parent().parent().attr('id');
		$('#insta-editor-element-id').val(elementID);
		$this.parent().parent().removeClass('insta-element-highlight');
		$this.parent().parent().find('.insta-content-el-close').remove();
		$this.parent().parent().find('.insta-content-el-handle').remove();
		$this.parent().parent().find('.insta-content-el-edit').remove();

		if ( type == 'text') {
			var content = $('#' + elementID).html();
			$('#instafronteditor_ifr').contents().find('#tinymce').html(content);
			$('#instafronteditor').val(content);
			$('#ite-editor-modal').modal({
				backdrop: false,
				show: true
			});
		} 
		// Open Notice Element Settings
		else if ( type == 'notice') {
			var content = $('#' + elementID).find('.alert').find('.notice-content').html();
			$('#edit-notice-type').val($('#' + elementID).data('ntype'));
			$('#instanoticeeditor_ifr').contents().find('#tinymce').html(content);
			$('#ite-notice-editor-modal').modal('show');
		} 
		// Open Image Element Settings
		else if ( type == 'image' ) {
			var uploaded = $('#' + elementID).data('uploaded');
			if ( uploaded == 'no' ) 
				instamedia(elementID);
			else {
				current_img = $('#' + elementID).find('img').attr('src');
				var img_align = $('#' + elementID).css('textAlign');
				if ( img_align == '' ) {
					img_align = 'left';
					$('#cur-img-pos').val(img_align);
				}
				
				$('#edit-img-group').find('.active').removeClass('active');
				$('#edit-img-pos-' + img_align).addClass('active');
				if ( $('#' + elementID).find('a').length ) {
					$('#edit-img-url').val($('#' + elementID).find('a').attr('href'));
					if ( $('#' + elementID).find('a').attr('target') == '_blank' ) {
						$('#edit-img-url-target').attr('checked', 'checked');
						insta_switch_state($('#edit-img-url-target'), 'on');
					} else {
						$('#edit-img-url-target').removeAttr('checked');
						insta_switch_state($('#edit-img-url-target'), 'off');
					}
				} else {
					$('#edit-img-url').val('');
					$('#edit-img-url-target').removeAttr('checked');
					insta_switch_state($('#edit-img-url-target'), 'off');
				}
				
				if ( $('#' + elementID).find('.insta-caption').length ) {
					var cbox_color = $('#' + elementID).find('.insta-caption').css('backgroundColor'),
					cborder_color = $('#' + elementID).find('.insta-caption').css('borderLeftColor'),
					ctext_color = $('#' + elementID).find('.insta-caption-txt').css('color'),
					ctext = $('#' + elementID).find('.insta-caption-txt').text();
					
					$('#edit-caption-text').val(ctext);
					$('#edit-caption-text-color').val(ctext_color);
					$('#edit-caption-box-color').val(cbox_color);
					$('#edit-caption-border-color').val(cborder_color);
					
					$('#edit-caption-text-color').iris('option', 'color', ctext_color);
					$('#edit-caption-box-color').iris('option', 'color', cbox_color);
					$('#edit-caption-border-color').iris('option', 'color', cborder_color);
					
					if ( $('#' + elementID).find('.insta-caption').hasClass('insta-caption-shadow') )
						insta_switch_state($('#edit-img-caption-box-shadow'), 'on');
					else
						insta_switch_state($('#edit-img-caption-box-shadow'), 'off');
				} else {
					$('#edit-caption-text').val('');
					$('#edit-caption-text-color').val('#808080');
					$('#edit-caption-box-color').val('#E5E5E5');
					$('#edit-caption-border-color').val('#CCCCCC');
					
					$('#edit-caption-text-color').iris('option', 'color', '#808080');
					$('#edit-caption-box-color').iris('option', 'color', '#E5E5E5');
					$('#edit-caption-border-color').iris('option', 'color', '#CCCCCC');
					
					insta_switch_state($('#edit-img-caption-box-shadow'), 'off');
				}
				
				$('#ite-image-editor-modal').modal({
					backdrop: false,
					show: true
				});
			}
		} else if ( type == 'divider' ) {
			var hrwidth = $('#' + elementID).data('hrwidth');
			$("#divider-width-slider").slider("option", "value", hrwidth);
			$("#edit-divider-width").val(hrwidth);
			$("#edit-divider-style").val($('#' + elementID).css('borderBottomStyle'));
			$('#divider-style-dropdown').text(insta_ucwords($('#' + elementID).css('borderBottomStyle')));
			
			var dthick = $('#' + elementID).css('borderBottomWidth');
			dthick = dthick.replace('px', '');
			
			var dcolor = $('#' + elementID).css('borderBottomColor');
			dcolor = '#' + rgbToHex(dcolor);

			$("#edit-divider-thick").val(dthick);
			$("#divider-thickness-slider").slider("option", "value", dthick);
			
			$("#edit-divider-color").val(dcolor);
			$('#edit-divider-color').iris('option', 'color', dcolor);
			
			$('#ite-divider-editor-modal').modal({
				backdrop: false,
				show: true
			});
		} else if ( type == 'box' ) {
			var box_w = $('#' + elementID + '-box').css('width'),
			box_c = $('#' + elementID + '-box').css('backgroundColor'),
			box_b = $('#' + elementID + '-box').css('borderTopStyle'),
			box_bt = $('#' + elementID).data('borderWidth'),
			box_bc = $('#' + elementID).data('borderColor'),
			box_br = $('#' + elementID + '-box').css('borderTopLeftRadius');
			
			box_w = box_w.replace('px', '');
			box_c = rgbToHex(box_c);
			box_br = box_br.replace('px', '');
			
			$("#box-width-slider").slider("option", "value", box_w);
			$("#border-thickness-slider").slider("option", "value", box_bt);
			$("#box-radius-slider").slider("option", "value", box_br);
			
			$('#edit-box-width').val(box_w);
			$('#edit-box-color').val(box_c);
			$('#edit-box-border-color').val(box_bc);
			$('#edit-box-border-thick').val(box_bt);
			$('#edit-box-border-radius').val(box_br);
			
			$('#edit-box-color').iris('option', 'color', box_c);
			$('#edit-box-border-color').iris('option', 'color', box_bc);
		
			$('#edit-box-border-type').val(box_b);
			$('#border-drop-down').text(insta_ucwords(box_b));
			
			if ( $('#' + elementID + '-box').hasClass('insta-box-shadow') ) {
				$('#edit-box-drop-shadow').attr('checked', true);
				insta_switch_state($('#edit-box-drop-shadow'), 'on');
			} else {
				$('#edit-box-drop-shadow').removeAttr('checked');
				insta_switch_state($('#edit-box-drop-shadow'), 'off');
			}
			
			$('#ite-box-editor-modal').modal({
				backdrop: false,
				show: true
			});
		} else if ( type == 'columns' ) {
			var cols = $('#' + elementID).data("cols"),
			cwidths = $('#' + elementID).data("cwidths");
			$('#edit-colnum-group').find('.insta-col-type-active').removeClass('insta-col-type-active');
			$('#edit-col-' + cols + '-' + cwidths).addClass('insta-col-type-active');
			$('#cur-col-num').val(cols);
			$('#orig-col-num').val(cols);
			$('#ite-column-editor-modal').modal({
				backdrop: false,
				show: true
			});
		} else if ( type == 'button' ) {
			var btn_pos = $('#' + elementID).find('p').css('textAlign'),
			btn_clr = $('#' + elementID).data('clr'),
			btn_size = $('#' + elementID).data('size');
			
			$('#cur-btn-pos').val(btn_pos);
			$('#edit-btn-group').find('.active').removeClass('active');
			$('#edit-btn-pos-' + btn_pos).addClass('active');
		
			var old_clr = $('#edit-btn-clr-group').find('.active').data('btnClr');
			if ( old_clr != 'default' ) {
				$('#edit-btn-clr-group').find('.active').removeClass('btn-' + old_clr);
			}
			$('#edit-btn-clr-group').find('.active').removeClass('active');
			
			if ( btn_clr == 'custom' ) {
				$('#btn-custom-colors').show();
				var cuscolor = $('#' + elementID).find('a').css('color'),
				cusbg = $('#' + elementID).find('a').css('backgroundColor'),
				cusborder = $('#' + elementID).find('a').css('borderColor');
				
				$('#edit-cusbtn-text-color').iris('option', 'color', cuscolor);
				$('#edit-cusbtn-back-color').iris('option', 'color', cusbg);
				$('#edit-cusbtn-border-color').iris('option', 'color', cusborder);
				
			} else {
				$('#btn-custom-colors').hide();
				
				$('#edit-cusbtn-text-color').iris('option', 'color', '#FFFFFF');
				$('#edit-cusbtn-back-color').iris('option', 'color', '#A7A7A7');
				$('#edit-cusbtn-border-color').iris('option', 'color', '#3A3A3A');
			}
			
			$('#cur-btn-clr').val(btn_clr);
			$('#edit-btn-clr-' + btn_clr).addClass('active');
			$('#edit-btn-clr-' + btn_clr).addClass('btn-' + btn_clr);
			
			$('#cur-btn-size').val(btn_size);
			$('#edit-btn-size-group').find('.active').removeClass('active');
			$('#edit-btn-size-' + btn_size).addClass('active');
			
			$('#edit-btn-text').val($('#' + elementID).find('a').text());
			$('#edit-btn-url').val($('#' + elementID).find('a').attr('href'));
			$('#edit-btn-text').val($('#' + elementID).find('a').text());
			
			if ( $('#' + elementID).find('a').attr('target') == '_blank' )
				$('#edit-btn-url-target').attr('checked', 'checked');
			else
				$('#edit-btn-url-target').removeAttr('checked');
						
			$('#ite-button-editor-modal').modal({
				backdrop: false,
				show: true
			});
		} else if ( type == 'youtube' ) {
			var yurl = $('#' + elementID).data('yurl'),
			yautoplay = $('#' + elementID).data('yautoplay'),
			ycontrols = $('#' + elementID).data('ycontrols'),
			ywidth = $('#' + elementID).find('iframe').css('width');
			
			ywidth = ywidth.replace('px', '');
			if ( yurl != 'none' )
				$('#edit-yt-url').val(yurl);
			else
				$('#edit-yt-url').val('');
				
			$('#edit-yt-size').val(ywidth);
			
			if ( yautoplay == 'yes' ) {
				$('#edit-yt-auto-play').attr('checked', true);
				insta_switch_state($('#edit-yt-auto-play'), 'on');
			} else {
				$('#edit-yt-auto-play').removeAttr('checked');
				insta_switch_state($('#edit-yt-auto-play'), 'off');
			}
			
			if ( ycontrols == 'yes' ) {
				$('#edit-yt-disable-controls').attr('checked', true);
				insta_switch_state($('#edit-yt-disable-controls'), 'on');
			} else {
				$('#edit-yt-disable-controls').removeAttr('checked');
				insta_switch_state($('#edit-yt-disable-controls'), 'off');
			}
			
			$('#ite-yt-editor-modal').modal({
				backdrop: false,
				show: true
			});
			
		} else if ( type == 'vimeo' ) {
			var vurl = $('#' + elementID).data('vurl'),
			vautoplay = $('#' + elementID).data('vautoplay'),
			vwidth = $('#' + elementID).find('iframe').css('width');
			
			vwidth = vwidth.replace('px', '');
			$('#edit-vm-url').val(vurl);
			$('#edit-vm-size').val(vwidth);
			
			if ( vautoplay == 'yes' ) {
				$('#edit-vm-auto-play').attr('checked', true);
				insta_switch_state($('#edit-vm-auto-play'), 'on');
			} else {
				$('#edit-vm-auto-play').removeAttr('checked');
				insta_switch_state($('#edit-vm-auto-play'), 'off');
			}
			
			$('#ite-vm-editor-modal').modal({
				backdrop: false,
				show: true
			});
			
		} else if ( type == 'iframe' ) {
			var html = $('#' + elementID).html(), iframe = $('#' + elementID).find('iframe');
			$('#edit-iframe-url').val(iframe.attr('src'));
			$('#edit-iframe-width').val(iframe.attr('width'));
			$('#edit-iframe-height').val(iframe.attr('height'));
			if ( iframe.attr('scrolling') == 'auto' ) {
				$('#edit-iframe-scroll').attr('checked', true);
				insta_switch_state($('#edit-iframe-scroll'), 'on');
			} else {
				$('#edit-iframe-scroll').removeAttr('checked');
				insta_switch_state($('#edit-iframe-scroll'), 'off');
			}
			
			if ( iframe.css('display') == 'none' ) {
				$('#edit-iframe-invisible').attr('checked', true);
				insta_switch_state($('#edit-iframe-invisible'), 'on');
			} else {
				$('#edit-iframe-invisible').removeAttr('checked');
				insta_switch_state($('#edit-iframe-invisible'), 'off');
			}
				
			$('#ite-iframe-editor-modal').modal({
				backdrop: false,
				show: true
			});
			
		} 
		
		// Open Title Element Settings
		else if ( type == 'title' ) {
			var html = $('#' + elementID).html(), content = $('#' + elementID).find('.insta-heading').html();
			$('.jqte_editor').html(content);
			
			if ( $('#' + elementID).data('stroke') == 'yes' ) {
				$('#edit-title-stroke').attr('checked', true);
				insta_switch_state($('#edit-title-stroke'), 'on');
				$('#edit-stroke-color').iris('option', 'color', $('#' + elementID).data('strokeColor'));
			} else {
				$('#edit-title-stroke').removeAttr('checked');
				insta_switch_state($('#edit-title-stroke'), 'off');
				$('#edit-stroke-color').iris('option', 'color', '#FFFFFF');
			}
			
			if ( $('#' + elementID).data('titleShadow') == 'yes' ) {
				$('#edit-title-shadow').attr('checked', true);
				insta_switch_state($('#edit-title-shadow'), 'on');
				$('#edit-shadow-color').iris('option', 'color', $('#' + elementID).data('shadowColor'));
			} else {
				$('#edit-title-shadow').removeAttr('checked');
				insta_switch_state($('#edit-title-shadow'), 'off');
				$('#edit-shadow-color').iris('option', 'color', '#808080');
			}
			
			$('#ite-title-editor-modal').modal({
				backdrop: false,
				show: true
			});
			
			
		} else if ( type == 'note' ) {
			var html = $('#' + elementID).html(), title = $('#' + elementID).find('.insta-note-title').text();
			$('.edit-note-title').val(title);
			$('#ite-note-editor-modal').modal({
				backdrop: false,
				show: true
			});
			
		} else if ( type == 'video' ) {
			var mp4 = $('#' + elementID).data('mp4'), ogg = $('#' + elementID).data('ogg'),
			webm = $('#' + elementID).data('webm'), splash = $('#' + elementID).data('splash'),
			ratio = $('#' + elementID).data('ratio'), width = 640;
			
			if ( $('#' + elementID).find('iframe').length ) width = $('#' + elementID).find('iframe').width();
			
			$('#edit-video-url').val(mp4);
			$('#edit-video-ogg-url').val(ogg);
			$('#edit-video-webm-url').val(webm);
			$('#cur-ratio').val(ratio);
			$('#edit-video-size').val(width);
			$('#edit-video-splash-url').val(splash);
			if ( $('#' + elementID).data('autoplay') == 'yes' ) {
				$('#edit-video-auto-play').attr('checked', true);
				insta_switch_state($('#edit-video-auto-play'), 'on');
			} else {
				$('#edit-video-auto-play').removeAttr('checked');
				insta_switch_state($('#edit-video-auto-play'), 'off');
			}
			
			if ( $('#' + elementID).data('controls') == 'no' ) {
				$('#edit-video-disable-controls').attr('checked', true);
				insta_switch_state($('#edit-video-disable-controls'), 'on');
			} else {
				$('#edit-video-disable-controls').removeAttr('checked');
				insta_switch_state($('#edit-video-disable-controls'), 'off');
			}
			
			$('#edit-ratio-group').find('.active').removeClass('active');
			$('#edit-ratio-' + ratio).addClass('active');
				
			$('#ite-video-editor-modal').modal({
				backdrop: false,
				show: true
			});
			
		} else if ( type == 'html' ) {
			var html = $('#' + elementID).html();
			$('#edit-html-code').val(html);
			$('#ite-html-editor-modal').modal({
				backdrop: false,
				show: true
			});
		}
		
		onpop = 1;
	});
	
	// ===============================================
	// UPDATE ELEMENTS
	// ===============================================
	$('body').delegate('.insta-update-col', 'click', function(e){
		e.preventDefault();
		var $this = $(this),
		elementID = $('#insta-editor-element-id').val(),
		newcolnum = parseInt($('#cur-col-num').val()),
		newcolwidth = $('#cur-col-width').val(),
		origcolnum = parseInt($('#orig-col-num').val()),
		totalMargin = (newcolnum - 1) * 2,
		newWidth = (100 - totalMargin) / newcolnum;
	
		$('#' + elementID).find('.icontent-col').css('width', newWidth + '%');
		if ( newcolnum > origcolnum ) {
			var diff = newcolnum - origcolnum, starnum = origcolnum + 1, lastcol = '';
			$('#' + elementID).find('.insta-col-last').removeClass('insta-col-last');
			for ( var i = 0; i < diff; i++ ) {
				if ( (i + 1) == diff ) lastcol = ' insta-col-last';
				var newCol = '<div style="width:' + newWidth + '%" id="' + elementID + '-col' + starnum + '" class="insta-col-sortable insta-col-highlight icontent-col' + lastcol + '"></div>';
				$('#' + elementID).append(newCol);
				starnum += 1;
			}
		} else if ( newcolnum < origcolnum ) {
			var diff = origcolnum - newcolnum;
			for ( var i = 0; i < diff; i++ ) {
				var rem_id = $('#' + elementID).find('.insta-col-last').attr('id');
				$('#' + rem_id).prev().addClass('insta-col-last');
				$('#' + rem_id).remove();
			}
		}
		
		if ( newcolwidth != '' ) {
			if ( newcolwidth == '1a' ) {
				$('#' + elementID).find('.icontent-col').css('width', '32%');
				$('#' + elementID).find('.insta-col-last').css('width', '66%');
			} else if ( newcolwidth == '1b' ) {
				$('#' + elementID).find('.icontent-col').css('width', '66%');
				$('#' + elementID).find('.insta-col-last').css('width', '32%');
			} else if ( newcolwidth == '2a' ) {
				$('#' + elementID).find('.icontent-col').css('width', '23.5%');
				$('#' + elementID).find('.insta-col-last').css('width', '74.5%');
			} else if ( newcolwidth == '2b' ) {
				$('#' + elementID).find('.icontent-col').css('width', '74.5%');
				$('#' + elementID).find('.insta-col-last').css('width', '23.5%');
			}
		}
		
		instacolumns();
		
		$('#col-warning-txt').hide();
		
		$('#' + elementID).data("cols", newcolnum);
		$('#' + elementID).attr("data-cols", newcolnum);
		
		$('#' + elementID).data("cwidths", newcolwidth);
		$('#' + elementID).attr("data-cwidths", newcolwidth);
		
		$('#ite-column-editor-modal').modal('hide');
	});
	
	$('body').delegate('.insta-update-img', 'click', function(e){
		e.preventDefault();
		var $this = $(this), elementID = $('#insta-editor-element-id').val(),
		url = $('#edit-img-url').val(), url_target = '_self', caption = $('#edit-caption-text').val();
		
		if ( $('#edit-img-url-target').is(":checked") ) url_target = '_blank';
		$('#' + elementID).css('textAlign', $('#cur-img-pos').val());
		var html = '';
		if ( url != '' ) {
			html += '<a href="' + url + '" target="' + url_target + '" class="insta-el-img-link">';
			html += '<img src="' + current_img + '" alt="" class="insta-el-img-edit" />';
			html += '</a>';
			
		} else {
			html += '<img src="' + current_img + '" alt="" class="insta-el-img-edit" />';
		}
		
		$('#' + elementID).find('span').html(html);
		
		if ( caption != '' ) {
			var cbox_color = $('#edit-caption-box-color').val(),
			ctext_color = $('#edit-caption-text-color').val(),
			cborder_color = $('#edit-caption-border-color').val();
			if ( !$('#' + elementID).find('.insta-caption').length )
				$('#' + elementID).find('span').wrap('<div class="insta-caption"></div>');
				
			if ( !$('#' + elementID).find('.insta-caption-txt').length )
				$('#' + elementID).find('.insta-caption').append('<p class="insta-caption-txt">' + caption + '</p>');
			else
				$('#' + elementID).find('.insta-caption-txt').text(caption);
				
			$('#' + elementID).find('.insta-caption').css({
				'background': cbox_color,
				'border': '1px solid ' + cborder_color
			});
			
			$('#' + elementID).find('.insta-caption-txt').css('color', ctext_color);
			if ( $('#edit-img-caption-box-shadow').is(":checked") )
				$('#' + elementID).find('.insta-caption').addClass('insta-caption-shadow');
		} else {
			$('#' + elementID).find('.insta-caption-txt').remove();
			if ( $('#' + elementID).find('.insta-caption').length )
				$('#' + elementID).find('span').unwrap();
		}
		
		$('#ite-image-editor-modal').modal('hide');
	});
	
	$('body').delegate('.insta-update-video', 'click', function(e){
		e.preventDefault();
		var $this = $(this), elementID = $('#insta-editor-element-id').val(),
		mp4 = $('#edit-video-url').val(), ogg = $('#edit-video-ogg-url').val(),
		webm = $('#edit-video-webm-url').val(), width = $('#edit-video-size').val(),
		splash = $('#edit-video-splash-url').val(), aspect = $('#cur-ratio').val(),
		ratio, height, src;
		
		$('#' + elementID).data('mp4', mp4);
		$('#' + elementID).attr('data-mp4', mp4);
		$('#' + elementID).data('ogg', ogg);
		$('#' + elementID).attr('data-ogg', ogg);
		$('#' + elementID).data('webm', webm);
		$('#' + elementID).attr('data-webm', webm);
		$('#' + elementID).data('splash', splash);
		$('#' + elementID).attr('data-splash', splash);
		$('#' + elementID).data('ratio', aspect);
		$('#' + elementID).attr('data-ratio', aspect);
		
		if ( $('#edit-video-auto-play').is(":checked") ) {
			$('#' + elementID).data('autoplay', "yes");
			$('#' + elementID).attr('data-autoplay', "yes");
		} else { 
			$('#' + elementID).data('autoplay', "no");
			$('#' + elementID).attr('data-autoplay', "no");
		}
		
		if ( $('#edit-video-disable-controls').is(":checked") ) {
			$('#' + elementID).data('controls', "no");
			$('#' + elementID).attr('data-controls', "no");
		} else {
			$('#' + elementID).data('controls', "yes");
			$('#' + elementID).attr('data-controls', "yes");
		}
		   	
		if ( aspect == 'full' ) ratio = 0.75; else if ( aspect == 'anamorphic' ) ratio = 0.417; else ratio = 0.5625;
	   	height = Math.ceil(parseInt(width) * ratio);
	   	src = $('#insta-current-vidpage').data('flowurl');
	   	src += '&mp4=' + mp4 + '&ogg=' + ogg + '&webm=' + webm;
	   	src += '&ratio=' + ratio;
	   	src += '&splash=' + splash;
	    if ( $('#edit-video-auto-play').is(":checked") ) src += '&autoplay=1';
	   	if ( $('#edit-video-disable-controls').is(":checked") ) src += '&controls=0';
	   	
	   	var html = '<iframe id="flowplayer-' + elementID + '" class="insta-video-playblack" width="' + width + '" height="' + height + '" src="' + src + '" frameborder="0" scrolling="no" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	   	$('#' + elementID).html(html);

		onpop = 0;
		$('#ite-video-editor-modal').modal('hide');
	});
	
	$('body').delegate('.insta-update-yt', 'click', function(e){
		e.preventDefault();
		var $this = $(this), elementID = $('#insta-editor-element-id').val(),
		url = $('#edit-yt-url').val();
		
		$('#' + elementID).data('yurl', url);
		$('#' + elementID).attr('data-yurl', url);
		
		if ( $('#edit-yt-auto-play').is(":checked") ) {
			$('#' + elementID).data('yautoplay', "yes");
			$('#' + elementID).attr('data-yautoplay', "yes");
		} else {
			$('#' + elementID).data('yautoplay', "no");
			$('#' + elementID).attr('data-yautoplay', "no");
		}
		
		if ( $('#edit-yt-disable-controls').is(":checked") ) {
			$('#' + elementID).data('ycontrols', "yes");
			$('#' + elementID).attr('data-ycontrols', "yes");
		} else {
			$('#' + elementID).data('ycontrols', "no");
			$('#' + elementID).attr('data-ycontrols', "no");
		}
		
		var videoid = url.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);
		if ( videoid != null ) {
			var ytID = videoid[1];
			var width = ( $('#edit-yt-size').val() == '' ) ? 640 : $('#edit-yt-size').val();
		   	width = parseInt(width);
		   	var height = Math.ceil(width * 0.5625);
		   	var params = '&rel=0&modestbranding=0&showinfo=0';
		   	if ( $('#edit-yt-auto-play').is(":checked") ) params += '&autoplay=1';
		   	if ( $('#edit-yt-disable-controls').is(":checked") ) params += '&controls=0';
		   	
		   	var html = '<iframe id="ytplayer-' + ytID + '" class="insta-video-playblack" width="' + width + '" height="' + height + '" src="http://www.youtube.com/embed/' + ytID + '?wmode=transparent' + params + '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
		   	$('#' + elementID).html(html);
		} else { 
		    alert('Invalid YouTube URL');
		}
		onpop = 0;
		$('#ite-yt-editor-modal').modal('hide');
	});
	
	$('body').delegate('.insta-update-vm', 'click', function(e){
		e.preventDefault();
		var $this = $(this), elementID = $('#insta-editor-element-id').val(),
		url = $('#edit-vm-url').val();
		
		$('#' + elementID).data('vurl', url);
		$('#' + elementID).attr('data-vurl', url);
		
		if ( $('#edit-vm-auto-play').is(":checked") ) {
			$('#' + elementID).data('vautoplay', "yes");
			$('#' + elementID).attr('data-vautoplay', "yes");
		} else {
			$('#' + elementID).data('vautoplay', "no");
			$('#' + elementID).attr('data-vautoplay', "no");
		}
		   	
		var videoid = url.match(/(videos|video|channels|\.com)\/([\d]+)/);
		if ( videoid != null ) {
			var vmID = videoid[2];
			var width = ( $('#edit-vm-size').val() == '' ) ? 640 : $('#edit-vm-size').val();
		   	width = parseInt(width);
		   	var height = Math.ceil(width * 0.5625);
		   	var params = '&title=0&byline=0&portrait=0';
		   	if ( $('#edit-vm-auto-play').is(":checked") ) params += '&autoplay=1';
		   	
		   	var html = '<iframe id="vmplayer-' + vmID + '" class="insta-video-playblack" width="' + width + '" height="' + height + '" src="http://player.vimeo.com/video/' + vmID + '?' + params + '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
		   	$('#' + elementID).html(html);
		} else { 
		    alert('Invalid Vimeo URL');
		}
		onpop = 0;
		$('#ite-vm-editor-modal').modal('hide');
	});
	
	$('body').delegate('.insta-update-html', 'click', function(e){
		e.preventDefault();
		var $this = $(this), elementID = $('#insta-editor-element-id').val(),
		html = $('#edit-html-code').val();
		$('#' + elementID).html(html);
		$('#ite-html-editor-modal').modal('hide');
	});
	
	$('body').delegate('.insta-update-divider', 'click', function(e){
		e.preventDefault();
		var $this = $(this), elementID = $('#insta-editor-element-id').val(),
		hrwidth = $('#edit-divider-width').val();
		$('#' + elementID).css({
			'width': hrwidth + '%',
			'border-bottom-color': $('#edit-divider-color').val(),
			'border-bottom-style': $('#edit-divider-style').val(),
			'border-bottom-width': $('#edit-divider-thick').val() + 'px',
		});
		$('#' + elementID).data('hrwidth', hrwidth);
		$('#ite-divider-editor-modal').modal('hide');
	});
	
	$('body').delegate('.insta-update-iframe', 'click', function(e){
		e.preventDefault();
		var $this = $(this), elementID = $('#insta-editor-element-id').val(),
		url = $('#edit-iframe-url').val(), width = $('#edit-iframe-width').val(),
		height = $('#edit-iframe-height').val();
		
		$('#' + elementID).html('<iframe></iframe>');
		$('#' + elementID).find('iframe').attr('src', url);
		$('#' + elementID).find('iframe').attr('width', width);
		$('#' + elementID).find('iframe').attr('height', height);
		$('#' + elementID).find('iframe').css({
			'width': width + 'px',
			'height': height + 'px',
			'margin': '0 auto 24px auto'
		});
		
		if ( $('#edit-iframe-scroll').is(":checked") ) $('#' + elementID).find('iframe').attr('scrolling', 'auto');
		if ( $('#edit-iframe-invisible').is(":checked") ) {
			$('#' + elementID).find('iframe').css({
				'display': 'none',
				'margin': 0
			});
			$('#' + elementID).append('<p class="muted">&lt;-- This is an invisible iframe element --&gt;</p>');
		}
		
		$('#ite-iframe-editor-modal').modal('hide');
	});
	
	$('body').delegate('.insta-update-btn', 'click', function(e){
		e.preventDefault();
		var $this = $(this), elementID = $('#insta-editor-element-id').val(),
		url = $('#edit-btn-url').val(), url_target = '_self', label = $('#edit-btn-text').val(),
		color = 'btn-' + $('#cur-btn-clr').val(),
		size = ( $('#cur-btn-size').val() != 'normal' ) ? 'btn-' + $('#cur-btn-size').val() : '',
		cuscolor = $('#edit-cusbtn-text-color').val(), cusbg = $('#edit-cusbtn-back-color').val(),
		cusborder = $('#edit-cusbtn-border-color').val();
		
		if ( $('#cur-btn-clr').val() == 'custom' ) {
			color = 'btn-custom-color';
		}
		
		if ( $('#edit-btn-url-target').is(":checked") ) url_target = '_blank';
		$('#' + elementID).find('p').css('textAlign', $('#cur-btn-pos').val());
		$('#' + elementID).find('a')
			.attr('href', url)
			.attr('target', url_target)
			.text(label)
			.removeClass('btn-info')
			.removeClass('btn-success')
			.removeClass('btn-primary')
			.removeClass('btn-warning')
			.removeClass('btn-danger')
			.removeClass('btn-custom-color')
			.removeClass('btn-lg')
			.removeClass('btn-xs')
			.removeClass('btn-sm')
			.removeClass('btn-xlg')
			.addClass(size)
			.addClass(color);
			
		if ( $('#cur-btn-clr').val() == 'custom' ) {
			$('#' + elementID).find('a')
				.css({
					'background-color': cusbg,
					'color': cuscolor,
					'border-color': cusborder
				});
		} else {
			$('#' + elementID).find('a').removeAttr('style');
		}
		
		$('#' + elementID).data('clr', $('#cur-btn-clr').val());
		$('#' + elementID).attr('data-clr', $('#cur-btn-clr').val());
		
		$('#' + elementID).data('size', $('#cur-btn-size').val());
		$('#' + elementID).attr('data-size', $('#cur-btn-size').val());
		$('#ite-button-editor-modal').modal('hide');
	});
	
	$('body').delegate('.insta-update-text', 'click', function(e){
		e.preventDefault();
		var $this = $(this), elementID = $('#insta-editor-element-id').val(),
		new_content = $('#instafronteditor_ifr').contents().find('#tinymce').html();
		
		if ( $('#instafronteditor').is(":visible") )
			new_content = $('#instafronteditor').val();
			
		$('#' + elementID).html(new_content);
		$('#ite-editor-modal').modal('hide');
	});
	
	$('body').delegate('.insta-update-title', 'click', function(e){
		e.preventDefault();
		var $this = $(this), elementID = $('#insta-editor-element-id').val(),
		new_content = $('.jqte_editor').html();
		
		var tfx = '';
		var stcolor = $('#edit-stroke-color').val();
		var shcolor = $('#edit-shadow-color').val();
		if ( $('#edit-title-stroke').is(":checked") ) {
			tfx += 'text-shadow: 2px 2px 0 ' + stcolor + ', -2px 2px 0 ' + stcolor + ', 2px -2px 0 ' + stcolor + ', -2px -2px 0 ' + stcolor;
			$('#' + elementID).data('stroke', 'yes');
			$('#' + elementID).attr('data-stroke', 'yes');
			$('#' + elementID).data('strokeColor', stcolor);
			$('#' + elementID).attr('data-stroke-color', stcolor);
		} else {
			$('#' + elementID).data('stroke', 'no');
			$('#' + elementID).attr('data-stroke', 'no');
		}
		
		if ( $('#edit-title-shadow').is(":checked") ) {
			if ( tfx != '' ) 
				tfx += ', 3px 2px 4px ' + shcolor;
			else
				tfx += 'text-shadow: 3px 2px 4px ' + shcolor;
				
			$('#' + elementID).data('titleShadow', 'yes');
			$('#' + elementID).attr('data-title-shadow', 'yes');
			$('#' + elementID).data('shadowColor', shcolor);
			$('#' + elementID).attr('data-shadow-color', shcolor);
		} else {
			$('#' + elementID).data('titleShadow', 'no');
			$('#' + elementID).attr('data-title-shadow', 'no');
		}
		
		if ( tfx != '' ) {
			$('#' + elementID).attr('style', tfx);
		} else {
			$('#' + elementID).removeAttr('style');
		}
		
		$('#' + elementID).find('.insta-heading').html(new_content);
		$('#ite-title-editor-modal').modal('hide');
	});
	
	$('body').delegate('.insta-update-note', 'click', function(e){
		e.preventDefault();
		var $this = $(this), elementID = $('#insta-editor-element-id').val(),
		new_title = $('#edit-note-title').val();
		
		$('#' + elementID).find('.insta-note-title').text(new_title);
		$('#ite-note-editor-modal').modal('hide');
	});
	
	$('body').delegate('.insta-update-notice', 'click', function(e){
		e.preventDefault();
		var $this = $(this), elementID = $('#insta-editor-element-id').val(),
		new_content = $('#instanoticeeditor_ifr').contents().find('#tinymce').html();
		
		$('#' + elementID).find('div').removeClass('alert-danger');
		$('#' + elementID).find('div').removeClass('alert-success');
		$('#' + elementID).find('div').removeClass('alert-info');
		$('#' + elementID).find('div').removeClass('alert-warning');
		
		if ( $('#edit-notice-type').val() != '' ) 
			$('#' + elementID).find('div').addClass('alert-' + $('#edit-notice-type').val());
			
		var icon = 'fa-warning';
		if ( $('#edit-notice-type').val() == 'info' ) {
			icon = 'fa-info';
		} else if ( $('#edit-notice-type').val() == 'error' ) {
			icon = 'fa-times';
		} else if ( $('#edit-notice-type').val() == 'success' ) {
			icon = 'fa-check';
		}
		
		$('#' + elementID).data('ntype', $('#edit-notice-type').val());
		$('#' + elementID).attr('data-ntype', $('#edit-notice-type').val());
		$('#' + elementID).find('.alert').html('<i class="fa fa-2x ' + icon + '" style="float:left; display: block; margin-right:10px;"></i><span class="notice-content">' + new_content + '</span>');
		$('#ite-notice-editor-modal').modal('hide');
	});
	
	$('body').delegate('.insta-update-box', 'click', function(e){
		e.preventDefault();
		var $this = $(this), elementID = $('#insta-editor-element-id').val(),
		box_w = $('#edit-box-width').val(), box_c = $('#edit-box-color').val(),
		box_b = $('#edit-box-border-type').val(), box_bc = $('#edit-box-border-color').val(),
		box_bt = $('#edit-box-border-thick').val(), box_br = $('#edit-box-border-radius').val();

		$('#' + elementID + '-box').css({
			'width': box_w + 'px',
			'background': box_c
		});
		
		if ( box_b == 'none' ) {
			$('#' + elementID + '-box').css('border', 'none' );
		} else {
			$('#' + elementID + '-box').css({
				'border' : box_bt + 'px ' + box_b + ' ' + box_bc 
			});
		}
		
		if ( box_br > 0 ) {
			$('#' + elementID + '-box').css({
				'-webkit-border-radius': box_br + 'px',
    			'border-radius': box_br + 'px'
    		});
		}
		
		$('#' + elementID).data('borderColor', box_bc);
		$('#' + elementID).attr('data-border-color', box_bc);
		$('#' + elementID).data('borderWidth', box_bt);
		$('#' + elementID).attr('data-border-width', box_bt);
		
		if ( $('#edit-box-drop-shadow').is(":checked") ) {
			$('#' + elementID + '-box').addClass('insta-box-shadow');
		} else {
			$('#' + elementID + '-box').removeClass('insta-box-shadow');
		}
		
		$('#ite-box-editor-modal').modal('hide');
	});
	
	// ===============================================
	// ELEMENT SETTINGS HELPER
	// ===============================================
	$('body').delegate('.border-selector', 'click', function(e){
		e.preventDefault();
		var $this = $(this), style = $this.data('border');
		$('#border-drop-down').text(insta_ucwords(style));
		$('#edit-box-border-type').val(style);
	});
	
	$('body').delegate('.divider-selector', 'click', function(e){
		e.preventDefault();
		var $this = $(this), style = $this.data('dividerLine');
		$('#divider-style-dropdown').text(insta_ucwords(style));
		$('#edit-divider-style').val(style);
	});
	
	$('body').delegate('.edit-btn-pos', 'click', function(e){
		e.preventDefault();
		$(this).parent().find('.active').removeClass('active');
		$(this).addClass('active');
		$('#cur-btn-pos').val($(this).data('btnPos'));
	});
	
	$('body').delegate('.edit-btn-pos', 'click', function(e){
		e.preventDefault();
		$(this).parent().find('.active').removeClass('active');
		$(this).addClass('active');
		$('#cur-btn-pos').val($(this).data('btnPos'));
	});
	
	$('body').delegate('.edit-btn-size', 'click', function(e){
		e.preventDefault();
		$(this).parent().find('.active').removeClass('active');
		$(this).addClass('active');
		$('#cur-btn-size').val($(this).data('btnSize'));
	});
	
	$('body').delegate('.edit-btn-clr', 'click', function(e){
		e.preventDefault();
		$(this).parent().find('.active').removeClass('active');
		$(this).parent().find('.btn-primary').removeClass('btn-primary');
		$(this).parent().find('.btn-info').removeClass('btn-info');
		$(this).parent().find('.btn-success').removeClass('btn-success');
		$(this).parent().find('.btn-warning').removeClass('btn-warning');
		$(this).parent().find('.btn-danger').removeClass('btn-danger');
		$(this).parent().find('.btn-custom').removeClass('btn-custom');
		$(this).addClass('active');
		$(this).addClass('btn-' + $(this).data('btnClr'));
		
		if ( $(this).data('btnClr') == 'custom' ) {
			$('#btn-custom-colors').show();
		} else {
			$('#btn-custom-colors').hide();
		}
		
		$('#cur-btn-clr').val($(this).data('btnClr'));
	});
	
	$('body').delegate('.edit-ratio', 'click', function(e){
		e.preventDefault();
		$(this).parent().find('.active').removeClass('active');
		$(this).addClass('active');
		$('#cur-ratio').val($(this).data('vidRatio'));
	});
	
	$('body').delegate('.edit-img-pos', 'click', function(e){
		e.preventDefault();
		$(this).parent().find('.active').removeClass('active');
		$(this).addClass('active');
		$('#cur-img-pos').val($(this).data('imgPos'));
	});
	
	$('body').delegate('.insta-replace-img', 'click', function(e){
		e.preventDefault();
		var elementID = $('#insta-editor-element-id').val();
		$('#ite-image-editor-modal').modal('hide');
		instamedia(elementID);
	});
	
	$('body').delegate('.insta-col-type', 'click', function(e){
		e.preventDefault();
		var curnum = parseInt($('#cur-col-num').val()),
		orignum = parseInt($('#orig-col-num').val()),
		selnum = parseInt($(this).data('colNum')),
		selwidth = $(this).data('colWidth');

		$('#cur-col-num').val(selnum);
		$('#cur-col-width').val(selwidth);
		if ( selnum < orignum ) {
			var res = orignum - selnum;
			$('#res-cols').text(res);
			$('#col-warning-txt').show();
		} else if ( selnum >= orignum ) {
			$('#col-warning-txt').hide();
		}
		$('#edit-colnum-group').find('.insta-col-type-active').removeClass('insta-col-type-active');
		$(this).addClass('insta-col-type-active');
	});
	
	// ===============================================
	// SWITCH EDITOR
	//================================================
	$('body').on('click', '.insta-switch-editor', function(e){
		e.preventDefault();
		
		var msg = "======================================\n";
		msg += "You're about to switch the editor mode\n";
		msg += "======================================\n";
		msg += "All elements position will be changed and might get messed up after\n";
		msg += "switching and you will need to rearrange the elements position.\n\n";
		msg += "Do you want to continue?";
		
		if ( confirm(msg) ) {
			_insta_destroy();
			
			stack.save();
			
			$.blockUI({
				message: "<h1>Switching Editor...<h1>",
				overlayCSS: { backgroundColor: '#000' } 
			});
			
			var entry_height = $('.entry-content').innerHeight();
			$('.entry-content').css({
				'position': 'relative',
				'height': entry_height + 'px'	
			});
			
			$('.insta-content-el').each(function(){
				var $this = $(this);
				
				$this.css({
					'width': '99%'
				});
				
				var position = $this.position();
				$this.css({
					'top': position.top + 'px',
					'left': position.left + 'px',
				});
			});
			
			$('body')
				.find('.insta-box-placeholder')
					.removeClass('insta-content-el')
					.addClass('insta-content-elbox');
					
			$('body')
				.find('.insta-video-container')
					.removeClass('insta-content-el')
					.addClass('insta-content-eltube');
					
			var post_id = $('#insta-post-id').data('postId'),
			content = $('.entry-content').html(),
			title = $('#edit-page-title').val(),
			entry_height = $('.entry-content').height(),
			hide_title = $('#edit-hide-title').is(":checked") ? 'yes' : 'no',
			disable_comment = $('#edit-disable-comment').is(":checked") ? 'yes' : 'no',
			data = {
				action: 'save_editor',
				content: content,
				title: title,
				post_id: post_id,
				editor: 'flex',
				height: entry_height,
				hide_title: hide_title,
				disable_comment: disable_comment
			};
			
			$.post(insta_ajaxurl, data, function(response){
				if ( response != 'OK' ) {
					alert('ERROR:' + response);
				}
				
				// reload editor
				window.location.href = $('#insta-editor-url').val();
			});
		}
	});
	
	// ===============================================
	// SAVE PAGE
	//================================================
	$('body').on('click', '#insta-page-save', function(e){
		e.preventDefault();
		
		_insta_destroy();
		
		stack.save();
		
		var post_id = $('#insta-post-id').data('postId'),
		title = $('#edit-page-title').val(),
		content = $('.entry-content').html(),
		editor = $('#insta-current-editor').val(),
		entry_height = $('.entry-content').height(),
		hide_title = $('#edit-hide-title').is(":checked") ? 'yes' : 'no',
		disable_comment = $('#edit-disable-comment').is(":checked") ? 'yes' : 'no',
		data = {
			action: 'save_editor',
			title: title,
			content: content,
			post_id: post_id,
			editor: editor,
			height: entry_height,
			hide_title: hide_title,
			disable_comment: disable_comment
		};
		
		$.blockUI({
			message: "<h1>Saving...<h1>",
			overlayCSS: { backgroundColor: '#000' } 
		});
		
		$.post(insta_ajaxurl, data, function(response){
			if ( response != 'OK' ) {
				alert('ERROR:' + response);
			}
			
			if ( disable_comment != old_disable_comment ) {
				location.reload();
			} else {
				// reinit
				_insta_init();
				setTimeout($.unblockUI, 500);
			}
		});
	});
	
	// ===============================================
	// FUNCTIONS
	// ===============================================
	
	function instasortableupdate( event, ui ) {
		var type = ui.item.data('element');
		if ( $is_drag == 1 ) {
			var cur_num = $('#insta-current-num').val(),
			new_num = parseInt(cur_num) + 1;
			
			var newElementId = 'insta-el-' + insta_id + new_num;
			do {
				new_num++;
				newElementId = 'insta-el-' + insta_id + new_num;
			} while ( $('#' + newElementId).length );
			
			$('#insta-current-num').val(new_num);
			if ( type == 'text' ) {
				ui.item.replaceWith('<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el" data-el="text"><p>Your text content goes here</p></div>');
			} else if ( type == 'image' ) {
				ui.item.replaceWith('<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el insta-content-el-image" data-el="image" data-uploaded="no" data-align="center" style="text-align:center" ><span contenteditable="true"><img src="" alt="" class="insta-el-img-edit" /></span></div>');
				insta_switch_state($('#edit-img-url-target'), 'off');
				insta_switch_state($('#edit-img-caption-box-shadow'), 'off');
				if ( ui.item.hasClass('graphic-item') ) {
					$('#' + newElementId).find('img').attr('src', ui.item.attr('src'));
					$('#' + newElementId).data('uploaded', 'yes');
					$('#' + newElementId).attr('data-uploaded', 'yes');
				} else {
					$('#insta-el-' + insta_id + new_num).find('img').attr('src', $('#insta-img-src').attr('src'));
					onpop = 1;
					instamedia('insta-el-' + insta_id + new_num);
				}
			} else if ( type == 'divider' ) {
				ui.item.replaceWith('<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el insta-divider" data-el="divider" data-hrwidth="100" style="width:100%; border-bottom-color:#E5E5E5; border-bottom-style:solid"></div>');
			} else if ( type == 'box' ) {
				ui.item.replaceWith('<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el insta-box-placeholder" data-el="box" data-edited="no" data-border-color="#cccccc" data-border-width="1"><div id="insta-el-' + insta_id + new_num + '-box" class="insta-box-element"><div class="insta-box-content insta-col-sortable"></div></div></div>');
				$('#edit-box-border-type').val('solid');
				$('#border-drop-down').text('Solid');
				instacolumns();
				$('#insta-el-' + insta_id + new_num + '-box').unbind('resizable').resizable({
					maxWidth: $('#insta-el-' + insta_id + new_num).width(),
					handles: 'e, s, se',
					autoHide: true,
					create: function( event, ui ) {
						elboxrez = 1;
					},
					stop: function ( event, ui ) {
						$(this).find('.insta-box-content:first').height(ui.size.height);
					}
				});
				
				insta_switch_state($('#edit-box-drop-shadow'), 'off');
			} else if ( type == 'fbcomm' ) {
				if ( insta_fbappid != '' ) {
					var comment = '';
					comment += '<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el insta-fb-comm" data-el="fbcomm">';
					comment += '</div>';
					ui.item.replaceWith(comment);
					
					$('.insta-fb-comm').trigger('loadfbcomm');

				} else {
					ui.item.replaceWith('');
					$('#ite-fberror-modal').modal('show');
				}
			} else if ( type == 'button' ) {
				ui.item.replaceWith('<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el" data-el="button" data-clr="warning" data-size="lg"><p style="text-align:center"><a href="http://" class="insta-btn-link btn btn-lg btn-warning" target="_self" role="button">Button Text</a></p></div>');
				$('#insta-editor-element-id').val('insta-el-' + insta_id + new_num);
				$('#edit-btn-text').val('Button Text');
				$('#edit-btn-url').val('http://');
				$('#edit-btn-url-target').removeAttr('checked');
				
				$('#cur-btn-pos').val('center');
				$('#edit-btn-group').find('.active').removeClass('active');
				$('#edit-btn-pos-center').addClass('active');
			
				var old_clr = $('#edit-btn-clr-group').find('.active').data('btnClr');
				if ( old_clr != 'default' ) {
					$('#edit-btn-clr-group').find('.active').removeClass('btn-' + old_clr);	
				}
				$('#edit-btn-clr-group').find('.active').removeClass('active');
				
				$('#btn-custom-colors').hide();
				
				$('#edit-cusbtn-text-color').iris('option', 'color', '#FFFFFF');
				$('#edit-cusbtn-back-color').iris('option', 'color', '#A7A7A7');
				$('#edit-cusbtn-border-color').iris('option', 'color', '#3A3A3A');
				
				$('#cur-btn-clr').val('warning');
				$('#edit-btn-clr-warning').addClass('active');
				$('#edit-btn-clr-warning').addClass('btn-warning');
				
				$('#cur-btn-size').val('lg');
				$('#edit-btn-size-group').find('.active').removeClass('active');
				$('#edit-btn-size-lg').addClass('active');
			
				$('#ite-button-editor-modal').modal({
					backdrop: false,
					show: true
				});
			} else if ( type == 'youtube' ) {
				var yt = '';
				yt += '<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el insta-video-container" data-yautoplay="no" data-ycontrols="no" data-yurl="none" data-el="youtube">';
				yt += '<img id="insta-el-' + insta_id + new_num + '-placeholder" src="" />';
				yt += '</div>';
				$('#insta-editor-element-id').val('insta-el-' + insta_id + new_num);
				ui.item.replaceWith(yt);
				onpop = 1;
				$('#insta-el-' + insta_id + new_num).find('img').attr('src', $('#insta-yt-src').attr('src'));
				
				$('#edit-yt-url').val('');
				$('#edit-yt-size').val('640');
				$('#edit-yt-auto-play').removeAttr('checked');
				$('#edit-yt-disable-controls').removeAttr('checked');
			
				insta_switch_state($('#edit-yt-auto-play'), 'off');
				insta_switch_state($('#edit-yt-disable-controls'), 'off');
				
				$('#ite-yt-editor-modal').modal({
					backdrop: false,
					show: true
				});
				
			} else if ( type == 'vimeo' ) {
				var vm = '';
				vm += '<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el insta-video-container" data-vautoplay="no" data-vurl="none" data-el="vimeo">';
				vm += '<img id="insta-el-' + insta_id + new_num + '-placeholder" src="" />';
				vm += '</div>';
				$('#insta-editor-element-id').val('insta-el-' + insta_id + new_num);
				ui.item.replaceWith(vm);
				onpop = 1;
				$('#insta-el-' + insta_id + new_num).find('img').attr('src', $('#insta-yt-src').attr('src'));
				
				$('#edit-vm-url').val('');
				$('#edit-vm-size').val('640');
				$('#edit-vm-auto-play').removeAttr('checked');
				
				insta_switch_state($('#edit-vm-auto-play'), 'off');
			
				$('#ite-vm-editor-modal').modal('show');
			} else if ( type == 'html' ) {
				var html = '';
				html += '<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el" data-el="html">';
				html += 'Click "Edit" To Insert your HTML code';
				html += '</div>';
				ui.item.replaceWith(html);
				onpop = 1;
				$('#insta-editor-element-id').val('insta-el-' + insta_id + new_num);
				$('#edit-html-code').val('');
				$('#ite-html-editor-modal').modal({
					backdrop: false,
					show: true
				});
				
			} else if ( type == 'note' ) {
				var html = '';
				html += '<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el insta-content-note" data-el="note" style="background:#E5E5E5;">';
				html += '<h3 class="insta-note-title">Your Notes</h3>';
				html += '<div class="insta-note-entries">';
				//html += '<div class="insta-note-entry"><div class="insta-note-text">This is note number #1</div><div class="insta-note-date"><div class="insta-note-btns"><button type="button" class="btn insta-edit-note">Edit</button> <button type="button" class="btn btn-danger insta-delete-note">Delete</button></div><span class="insta-note-date-txt">Last Edited Monday, Jan 10 2013</span></div>';
				//html += '<div class="insta-note-entry"><div class="insta-note-text">This is note number #1</div><div class="insta-note-date"><div class="insta-note-btns"><button type="button" class="btn insta-edit-note">Edit</button> <button type="button" class="btn btn-danger insta-delete-note">Delete</button></div><span class="insta-note-date-txt">Last Edited Monday, Jan 10 2013</span></div>';
				html += '</div>';
				html += '<div class="insta-note-editor"><textarea class="insta-note-editor-field"></textarea><div style="text-align:right"><button type="button" class="btn btn-success insta-save-note">Save Note</button></div></div>';
				html += '<input type="hidden" class="insta-note-username" value="" />';
				html += '<input type="hidden" class="insta-note-contentid" value="0" />';
				html += '<input type="hidden" class="insta-note-postid" value="' + insta_postid + '" />';
				html += '</div>';
				ui.item.replaceWith(html);
				onpop = 1;
				$('#insta-editor-element-id').val('insta-el-' + insta_id + new_num);
				$('#ite-note-editor-modal').modal({
					backdrop: false,
					show: true
				});
				
			} else if ( type == 'title' ) {
				var default_title = '<span style="font-size:32px; font-family: \'Montserrat\', sans-serif; color:#cc0000;">Your Title Text Goes Here...</span>';
				var title = '';
				title += '<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el" data-el="title" data-stroke="no" data-stroke-color="#ffffff" data-title-shadow="no" data-shadow-color="#808080">';
				title += '<div class="insta-heading">' + default_title + '</div>';
				title += '</div>';
				ui.item.replaceWith(title);
				$('.jqte_editor').html(default_title);
				onpop = 1;
				
				$('#edit-title-shadow').removeAttr('checked');
				$('#edit-title-stroke').removeAttr('checked');
				
				insta_switch_state($('#edit-title-shadow'), 'off');
				insta_switch_state($('#edit-title-stroke'), 'off');
				
				$('#insta-editor-element-id').val('insta-el-' + insta_id + new_num);
				$('#ite-title-editor-modal').modal({
					backdrop: false,
					show: true
				});
				
			} else if ( type == 'notice' ) {
				var html = '';
				html += '<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el insta-alert" data-ntype="" data-el="notice">';
				html += '<div class="alert"><span class="notice-content"><p>Your Notice/Alert Text Here.</p></span></div>';
				html += '</div>';
				$('#edit-notice-type').val('warning');
				$('#insta-editor-element-id').val('insta-el-' + insta_id + new_num);
				$('#instanoticeeditor_ifr').contents().find('#tinymce').html('<p>Your Notice/Alert Text Here.</p>');
				ui.item.replaceWith(html);
				
				onpop = 1;
				$('#insta-editor-element-id').val('insta-el-' + insta_id + new_num);
				$('#ite-notice-editor-modal').modal({
					backdrop: false,
					show: true
				});
			} else if ( type == 'iframe' ) {
				var html = '';
				html += '<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el" data-el="iframe" style="text-align:center">';
				html += '<iframe src="" width="720" height="480" style="width:720px; margin:0 auto 24px" frameborder="0" scrolling="no" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
				html += '</div>';
				ui.item.replaceWith(html);
				onpop = 1;
				$('#insta-editor-element-id').val('insta-el-' + insta_id + new_num);
				$('#edit-iframe-url').val('');
				$('#edit-iframe-width').val('720');
				$('#edit-iframe-height').val('480');
				$('#edit-iframe-scroll').removeAttr('checked');
				$('#edit-iframe-invisible').removeAttr('checked');
				
				insta_switch_state($('#edit-iframe-scroll'), 'off');
				insta_switch_state($('#edit-iframe-invisible'), 'off');
				
				$('#ite-iframe-editor-modal').modal({
					backdrop: false,
					show: true
				});
			} else if ( type == 'video' ) {
				var data = ' data-mp4="" data-ogg="" data-webm="" data-ratio="wide" data-autoplay="no" data-controls="yes" data-splash=""';
				var vids = '';
				vids += '<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el insta-video-container" data-el="video"' + data + '>';
				vids += '<img id="insta-el-' + insta_id + new_num + '-placeholder" src="" />';
				vids += '</div>';
				ui.item.replaceWith(vids);
				$('#insta-el-' + insta_id + new_num).find('img').attr('src', $('#insta-vid-src').attr('src'));
				onpop = 1;
				$('#insta-editor-element-id').val('insta-el-' + insta_id + new_num);
				$('#edit-video-url').val('');
				$('#edit-video-ogg-url').val('');
				$('#edit-video-webm-url').val('');
				$('#cur-ratio').val('wide');
				$('#edit-video-size').val('640');
				$('#edit-video-splash-url').val('');
				$('#edit-video-auto-play').removeAttr('checked');
				$('#edit-video-disable-seek').removeAttr('checked');
				$('#edit-ratio-group').find('.active').removeClass('active');
				$('#edit-ratio-wide').addClass('active');
				
				insta_switch_state($('#edit-video-auto-play'), 'off');
				insta_switch_state($('#edit-video-disable-controls'), 'off');
				
				$('#ite-video-editor-modal').modal({
					backdrop: false,
					show: true
				});
			} else if ( type == 'columns' ) {
				if ( ui.item.parent().hasClass('icontent-col')) {
					ui.item.replaceWith('');
					alert('ERROR: Cannot drop Columns into a column');
				} else {
					var cols = '<div id="insta-el-' + insta_id + new_num + '" class="insta-content-el insta-col-placeholder icontent-row" data-el="columns" data-cols="2" data-cwidths="">';
					cols += '<div style="width:48%" id="insta-el-' + insta_id + new_num + '-col1" class="insta-col-sortable icontent-col insta-col-highlight"></div>';
					cols += '<div style="width:48%" id="insta-el-' + insta_id + new_num + '-col2" class="insta-col-sortable icontent-col insta-col-highlight insta-col-last"></div>';
					cols += '</div>';
					ui.item.replaceWith(cols);
					
					// reload sortable and resizable for columns...
					instacolumns();
				}
			}
			$is_drag = 0;
			
			$('.entry-content').trigger('undoable');
		} else {
			if ( ui.item.hasClass('insta-col-placeholder') && ui.item.parent().hasClass('icontent-col') ) {
				$(this).sortable('cancel');
				alert('ERROR: Cannot drop Columns into a column');
			}
		}
	}
	
	function instamedia( elementID ) {
		// If the media frame already exists, reopen it.
		if ( typeof(insta_file_frame) !== "undefined" ) {
			insta_file_frame.close();
		}
		
		frame_data = {
			title: 'Upload Image',
			button: {
				text: 'Use Image',
			},
			multiple: false
		};

		// Create the media frame.
		insta_file_frame = wp.media.frames.customHeader = wp.media(frame_data);
		
		 // When an image is selected, run a callback.
		insta_file_frame.on('select', function(){
			// We set multiple to false so only get one image from the uploader
			attachment = insta_file_frame.state().get('selection').first().toJSON();
 
			// Do something with attachment.id and/or attachment.url here
			$('#' + elementID).find('img').attr('src', attachment.url);
			$('#' + elementID).data('uploaded', 'yes');
			$('#' + elementID).attr('data-uploaded', 'yes');
			onpop = 0;
		});
		
		 // Finally, open the modal
		insta_file_frame.open();
	}
	
	function instacolumns() {
		$('.insta-col-sortable').unbind('sortable').sortable({
			connectWith: ".insta-col-sortable, .insta-sortable",
			placeholder: "insta-sortable-highlight",
			cursorAt: { top: 5 },
			handle: ".insta-content-el-handle",
			update: instasortableupdate,
			stop: function( event, ui ) {
				$('.entry-content').trigger('undoable');
			}
		});
	}
	
	function insta_ucwords (str) {
	  // http://kevin.vanzonneveld.net
	  // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	  // +   improved by: Waldo Malqui Silva
	  // +   bugfixed by: Onno Marsman
	  // +   improved by: Robin
	  // +      input by: James (http://www.james-bell.co.uk/)
	  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // *     example 1: ucwords('kevin van  zonneveld');
	  // *     returns 1: 'Kevin Van  Zonneveld'
	  // *     example 2: ucwords('HELLO WORLD');
	  // *     returns 2: 'HELLO WORLD'
	  return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function ($1) {
		return $1.toUpperCase();
	  });
	}
	
	function rgbToHex(color) {
	    if (color.substr(0, 1) === "#") {
	        return color;
	    }
	    var nums = /(.*?)rgb\((\d+),\s*(\d+),\s*(\d+)\)/i.exec(color),
	        r = parseInt(nums[2], 10).toString(16),
	        g = parseInt(nums[3], 10).toString(16),
	        b = parseInt(nums[4], 10).toString(16);
	    return (
	        (r.length == 1 ? "0"+ r : r) +
	        (g.length == 1 ? "0"+ g : g) +
	        (b.length == 1 ? "0"+ b : b)
	    );
	}
	
	function _insta_init() {
		$(".insta-sortable").sortable({
			connectWith: ".insta-col-sortable",
			placeholder: "insta-sortable-highlight",
			cursorAt: { top: 5 },
			handle: ".insta-content-el-handle",
			update: instasortableupdate,
			start: function(event, ui) {
				$('iframe.insta-video-playblack').hide();
			},
			stop: function(event, ui) {
				$('iframe.insta-video-playblack').show();
				$('.entry-content').trigger('undoable');
			}
		});
		
		//$(".insta-sortable").droppable({
			//accept: ".ite-element, .graphic-item",
			//drop: insta_drop_box
		//});
			
		$('.insta-box-element').resizable({
			maxWidth: contentWidth,
			handles: 'e, s, se',
			autoHide: true,
			create: function( event, ui ) {
				elboxrez = 1;
			},
			stop: function ( event, ui ) {
				$(this).find('.insta-box-content:first').height(ui.size.height);
				$('.entry-content').trigger('undoable');
			}
		});
		
		$('img').each(function(){
			$(this).parents('span').attr('contenteditable', 'true');
		});
		
		instacolumns();
	}
	
	function _insta_destroy() {
		$(".insta-sortable").sortable('destroy');
		if ( $('.insta-col-sortable').length ) $('.insta-col-sortable').sortable('destroy');
		if ( elboxrez ) {
			$('.insta-box-element').resizable('destroy');
		}
		
		$('.insta-box-element').removeClass('ui-resizable-autohide');
		
		$('.insta-box-element').css({
			'height' : 'auto',
			'position': 'relative'
		});
		
		$('.insta-box-element').find('.insta-box-content').css({
			'height' : 'auto'
		});
	}
	
	// UNDO SCRIPT //
	$('body').on('click', '.insta-update-el', function(e){
		$('.entry-content').trigger('undoable');
	});
	
	var undo = $(".insta-undo"),
		redo = $(".insta-redo"),
		dirty = $(".insta-dirty");
		
	function stackUI() {
		if ( !stack.canUndo() ) {
			undo
				.addClass("disabled")
				.removeClass("active");
		} else {
			undo
				.addClass("active")
				.removeClass("disabled");
		}
		
		if ( !stack.canRedo() ) {
			redo
				.addClass("disabled")
				.removeClass("active");
		} else {
			redo
				.addClass("active")
				.removeClass("disabled");
		}
		
		dirty.toggle(stack.dirty());
	}
	stackUI();
	
	$(document.body).delegate(".insta-undo", "click", function(e) {
		stack['undo']();
		e.preventDefault();
	});
	
	$(document.body).delegate(".insta-redo", "click", function(e) {
		stack['redo']();
		e.preventDefault();
	});
	
	var instaContent = $(".entry-content"),
		startContent = instaContent.html(),
		undoTimer;
		
	$(".entry-content").bind("undoable", function() {
		// a way too simple algorithm in place of single-character undo
		clearTimeout(undoTimer);
		undoTimer = setTimeout(function() {
			var newContent = instaContent.html();
			// ignore meta key presses
			if ( newContent != startContent ) {
				// this could try and make a diff instead of storing snapshots
				stack.execute(new EditCommand(instaContent, startContent, newContent));
				startContent = newContent;
			}
		}, 250);
	});
	
	
	$(document).on('keydown', function(e){
		if ( onpop == 0 && e.which === 90 && e.ctrlKey ) {
			e.preventDefault();
     		stack.canUndo() && stack.undo();
  		} else if ( onpop == 0 && e.which === 89 && e.ctrlKey ) {
  			e.preventDefault();
     		stack.canRedo() && stack.redo();
  		}
	});
})(jQuery);
