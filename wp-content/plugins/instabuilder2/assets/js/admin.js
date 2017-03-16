var template_open = 0, 
funnel_template_e = 0,
funnel_template = 0, 
funnel_index = 0, 
new_funnel = 0, 
new_variant = 0;
;(function($) {
	var subtype_data = {};
	subtype_data.all = '<option value="textsqueeze">Text Squeeze</option>'
				+ '<option value="videosqueeze">Video Squeeze</option>'
				+ '<option value="minisqueeze">Mini Squeeze</option>'
				+ '<option value="2stepsoptin">2 Steps Opt-In</option>'
				+ '<option value="3stepsoptin">3 Steps Opt-In</option>'
				+ '<option value="textsales">Text Sales Page</option>'
				+ '<option value="videosales" >Video Sales Page</option>'
				+ '<option value="hybridsales">Hybrid Sales Page</option>'
				+ '<option value="otosales">OTO Sales Page</option>'
				+ '<option value="webinarsignup">Webinar Sign-Up</option>'
				+ '<option value="webinarthanks">Webinar Thank You</option>'
				+ '<option value="download">Download Page</option>'
				+ '<option value="confirmation">Confimation Page</option>'
				+ '<option value="thankyou">Thank You Page</option>';
	
	subtype_data.optin = '<option value="textsqueeze">Text Squeeze</option>'
				+ '<option value="videosqueeze">Video Squeeze</option>'
				+ '<option value="minisqueeze">Mini Squeeze</option>'
				+ '<option value="2stepsoptin">2 Steps Opt-In</option>'
				+ '<option value="3stepsoptin">3 Steps Opt-In</option>';
				
	subtype_data.sales = '<option value="textsales">Text Sales Page</option>'
				+ '<option value="videosales" >Video Sales Page</option>'
				+ '<option value="hybridsales">Hybrid Sales Page</option>'
				+ '<option value="otosales">OTO Sales Page</option>';
				
	subtype_data.webinar = '<option value="webinarsignup">Webinar Sign-Up</option>'
				+ '<option value="webinarthanks">Webinar Thank You</option>';
				
	subtype_data.launch = '';
	subtype_data.coming = '';
	subtype_data.others = '<option value="download">Download Page</option>'
				+ '<option value="confirmation">Confimation Page</option>'
				+ '<option value="thankyou">Thank You Page</option>';
				
				
	$('.stats-tooltip').each(function(){
		$(this).tooltip();	
	});
	
	$('.create-group-btn').click(function(e){
		if ( $('#new-group').is(":visible") ) {
			$('#new-group').hide();
		} else {
			$('#new-group').show();
			
			$('html, body').animate({
    			scrollTop: $("#page-groups").offset().top
 			}, 1000);
 
			$('#new-group-name').focus();
		}
		e.preventDefault();
	});
	
	$('body').on('change', '.pages-parent-check', function(){
		if ( $(this).is(":checked") ) {
			$('.page-child-check').attr("checked", true);
			$('.pages-parent-check').not(this).attr("checked", true);
		} else {
			$('.page-child-check').removeAttr("checked");
			$('.pages-parent-check').not(this).removeAttr("checked");
		}
	});
	
	$('body').on('change', '.groups-parent-check', function(){
		if ( $(this).is(":checked") ) {
			$('.group-child-check').attr("checked", true);
			$('.groups-parent-check').not(this).attr("checked", true);
		} else {
			$('.group-child-check').removeAttr("checked");
			$('.groups-parent-check').not(this).removeAttr("checked");
		}
	});
	
	// SEARCH DATE
	$('form#stats-form').submit(function(){
		if ( $('#search-mode').val() == 'range' && $('#date-from').val() == '' ) {
			alert('Please enter a start date.');
			return false;
		}
		
		if ( $('#search-mode').val() == 'range' && $('#date-to').val() == '' ) {
			alert('Please enter an end date.');
			return false;
		}
		
		return true;
	});
	
	$('#search-mode').change(function(){
		if ( $("option:selected", this).val() == 'range' )
			$('.range-search-field').show();
		else
			$('.range-search-field').hide();
	}).change();
	
	$('#date-from').datepicker({
		defaultDate: "-10d",
		dateFormat: "mm/dd/yy",
		changeMonth: true,
		changeYear: true,
		onClose: function( selectedDate ) {
			$('#date-to').datepicker("option", "minDate", selectedDate);
		}
	});
	
	$('#date-to').datepicker({
		dateFormat: "mm/dd/yy",
		changeMonth: true,
		changeYear: true,
		onClose: function( selectedDate ) {
			$('#date-from').datepicker("option", "maxDate", selectedDate);
		}
	});
	
	// ADD TO GROUP
	$('.page-to-group-add').each(function(){
		$(this).click(function(e){
			var $this = $(this), page_id = $this.data('postid');
			
			$('#the_post_id').val(page_id);
			$('#ib2-group-add-modal').modal({
				show: true,
				backdrop: false
			});
			
			e.preventDefault();
		});
	});
	
	$('.page-from-import').each(function(){
		$(this).click(function(e){
			$('#ib2-import-file').modal({
				show: true,
				backdrop: false
			});
			
			e.preventDefault();
		});
	});
	
	// CHANGE PERMALINK
	$('.change-page-slug').each(function(){
		$(this).click(function(e){
			var $this = $(this), page_id = $this.data('postid'), permalink = $this.data('permalink'),
			post_name = $this.data('slug'), status = $this.data('status');
			
			$('#slug_url').text(permalink);
			$('#ib2-new-slug').val(post_name);
			$('#ib2-slug-postid').val(page_id);
			$('#ib2-current-slug').val(post_name);
			$('#ib2-page-status').val(status);
			
			var pos  = 'absolute';
			var top  = (($(window).height() / 2) - ($('#ib2-change-permalink').outerHeight() / 2));
			var left = (($('#wpbody').width() / 2) - ($('#ib2-change-permalink').outerWidth() / 2));

			top = top + $(window).scrollTop();
	
			if ( top < 0 ) top = 0;
			if ( left < 0 ) left = 0;
			
			$('#ib2-change-permalink').css({
		    	'position' : pos,
		    	'top' : top,
		    	'left' : left
			});

			$('#ib2-change-permalink').fadeIn("medium");
			
			e.preventDefault();
		});
	});
	
	$('#save-new-permalink').click(function(e){
		var $this = $(this), current_slug = $('#ib2-current-slug').val(),
		new_slug = $('#ib2-new-slug').val(), post_id = $('#ib2-slug-postid').val(),
		status = $('#ib2-page-status').val();
		
		e.preventDefault();
		
		if ( new_slug == '' || current_slug == new_slug ) {
			$('#ib2-change-permalink').fadeOut("medium");
			return false;
		}
		
		$this.html('<i class="fa fa-spinner fa-spin"></i>').attr('disabled', true);
		$('#ib2-new-slug').attr('disabled', true);
		$.post(ajaxurl, {
			action: 'ib2_update_permalink',
			post_id: post_id,
			new_slug: new_slug,
		}, function( response ) {
			if ( response.success ) {
				$('#ib2-new-slug').val(response.post_name);
				$('#permalink-url-' + post_id).text(response.display_url);
				if ( status == 'publish' )
					$('#permalink-url-' + post_id).attr('href', response.new_url);
			}

			$this.text('OK').removeAttr('disabled');
			$('#ib2-new-slug').removeAttr('disabled');
			
			$('#permalink-alert').show();
			setTimeout(function(){
				$('#permalink-alert').hide();
			}, 5000);
		});
	});
	
	$('.ib2-permalink-close').each(function(){
		$(this).click(function(e){
			$('#ib2-change-permalink').fadeOut("medium");
			e.preventDefault();
		});
	});
	
	// NEW VARIATION
	$('.page-new-variation').each(function(){
		$(this).click(function(e){
			var $this = $(this), page_id = $this.data('postid'), group_id = $this.data('groupid');
			
			$('#the_post_id').val(page_id);
			$('#ib2-group-id').val(group_id);
			if ( $('#ib2-post-id').length ) {
				$('#ib2-post-id').val(page_id);
			}
		
			var pos  = 'absolute';
			var top  = (($(window).height() / 2) - ($('#ib2-new-variant').outerHeight() / 2));
			var left = (($('#wpbody').width() / 2) - ($('#ib2-new-variant').outerWidth() / 2));

			top = top + $(window).scrollTop();
	
			if ( top < 0 ) top = 0;
			if ( left < 0 ) left = 0;
			
			$('#ib2-new-variant').css({
		    	'position' : pos,
		    	'top' : top,
		    	'left' : left
			});
			
			$('#variant-type').val('duplicate');
			$('#ib2-templts').hide();
			$('#non-templates-area').show();
			$('#variant-create').data('mode', 'duplicate');
			$('#variant-create').attr('data-mode', 'duplicate');
			
			$('#ib2-new-variant').fadeIn("medium");
			
			new_variant = 1;
			
			e.preventDefault();
		});
	});
	
	$('body').on('click', '.create-new-page-variant', function(e){
		var $this = $(this), url = ib2pageurl,
		post_id = $('#the_post_id').val(), group_id = $('#ib2-group-id').val();
		
		if ( $('#ib2-post-id').length ) {
			post_id = $('#ib2-post-id').val();
		}
		
		url += '&ib2action=new_variant_ext&mode=template&template_id=' + $this.data('templateId') + '&post_id=' + post_id + '&group_id=' + group_id;
		window.location.href = url;
		e.preventDefault();
	});
	
	$('body').on('click', '#variant-create', function(e){
		var $this = $(this), url = ib2pageurl,
		post_id = $('#the_post_id').val(), group_id = $('#ib2-group-id').val();
		
		if ( $('#ib2-post-id').length ) {
			post_id = $('#ib2-post-id').val();
		}
		
		url += '&ib2action=new_variant_ext&mode=' + $this.data('mode') + '&post_id=' + post_id + '&group_id=' + group_id;
		window.location.href = url;
		e.preventDefault();
	});
	
	$('.ib2-variant-close').each(function(){
		$(this).click(function(e){
			$('#ib2-new-variant').fadeOut("medium");
			new_variant = 0;
			e.preventDefault();
		});
	});
	
	$('body').on('click', '.ib2-tmplt-type', function(e){
		var $this = $(this), type = $this.data('type'),
		text = $this.text(), keyword = '';
		
		$this.parent().parent().find('.active').removeClass('active');
		$this.parent().addClass('active');
		
		$this.parents('.ib2-tmpls').find('.ib2-templts-area').find('h3').text(text + ' Templates');
		$this.parents('.ib2-tmpls').find('.ib2-templts-loader').show();
		$this.parents('.ib2-tmpls').find('.ib2-templts-content').html('');

		var newoptions = '<option value=""> -- Sub-Type -- </option>';
		newoptions += subtype_data[type];
		
		$('#ib2-tmplt-subtype').html(newoptions);
		
		$.post(ajaxurl, {
			action: 'ib2_show_templates',
			type: type,
			subtype: '',
			keyword: keyword
		}, function(response){
			$this.parents('.ib2-tmpls').find('.ib2-templts-content').html(response);
			$this.parents('.ib2-tmpls').find('.ib2-templts-loader').hide();
		});
		
		e.preventDefault();
	});
	
	$('body').on('change', '#ib2-tmplt-subtype', function(){
		var $this = $("option:selected", this), subtype = $this.val(),
		id = $(this).attr('id'), type = $('.new-variant-type').find('li.active > a').data('type'),
		keyword = $('#ib2-tmplt-tags').val();
			
		$('.ib2-templts-loader').show();
		$('.ib2-templts-content').html('');
		
		$.post(ajaxurl, {
			action: 'ib2_show_templates',
			type: type,
			subtype: subtype,
			keyword: keyword
		}, function(response){
			$('.ib2-templts-content').html(response);
			$('.ib2-templts-loader').hide();
		});
	});
	
	$('body').on('submit', 'form#template-search-form', function(e){
		e.preventDefault();
		
		var $this = $(this), subtype = $this.find('select').val(),
		id = $(this).attr('id'), type = $('.new-variant-type').find('li.active > a').data('type'),
		keyword = $this.find('input[type=text]').val();
		
		$('.ib2-templts-loader').show();
		$('.ib2-templts-content').html('');
		
		$.post(ajaxurl, {
			action: 'ib2_show_templates',
			type: type,
			subtype: subtype,
			keyword: keyword
		}, function(response){
			$('.ib2-templts-content').html(response);
			$('.ib2-templts-loader').hide();
		});
	});
	
	$('body').on('change', '#variant-type', function(){
		var opt = $("option:selected", this).val();
		$('#variant-create').data('mode', opt);
		$('#variant-create').attr('data-mode', opt);
			
		if ( opt == 'template' ) {
			$('#ib2-templts').show();
			$('#non-templates-area').hide();
			
			$('#ib2-templts > ul.nav-pills > li.active > a').trigger('click');
		} else {
			$('#ib2-templts').hide();
			$('#non-templates-area').show();
		}
	});
	
	// NEW PAGE
	$('.ib2-new-page').each(function(){
		$(this).click(function(e){
			var $this = $(this);
			
			if ( template_open == 1 ) return false;
			
			if ( !$('#ib2-templates-bg').length ) {
				$('body').append('<div id="ib2-templates-bg"></div>');
			}
			
			var pos  = 'absolute';
			var top  = (($(window).height() / 2) - ($('#ib2-templates').outerHeight() / 2));
			var left = (($(window).width() / 2) - ($('#ib2-templates').outerWidth() / 2));

			top = top + $(window).scrollTop();
	
			if ( top < 0 ) top = 0;
			if ( left < 0 ) left = 0;
			
			$('#ib2-templates').css({
		    	'position' : pos,
		    	'top' : top,
		    	'left' : left
			});
			
			var group_id = $this.data('gnum');
			$('#ib2-group-id').val(group_id);
			
			if ( $this.attr('data-postid') ) {
				$('#ib2-post-id').val($this.attr('data-postid'));
			} else {
				$('#ib2-post-id').val(0);
			}
			
			$('#ib2-templates-bg').fadeIn("fast");
			$('#ib2-templates').fadeIn("medium");
			
			template_open = 1;
			
			$('#ib2-new-scratch').show();
			
			var url = ib2pageurl;
			url += '&ib2action=newpage&template_id=scratch';
			
			if ( group_id != 0 ) {
				url += '&group_id=' + group_id;
			}
			
			$('#ib2-new-scratch').attr('href', url);
			
			e.preventDefault();
		});
	});
	
	$('body').on('mouseenter', '.ib2-tmpl-thumb', function(){
		var $this = $(this);
		
		if ( !$this.find('.ib2-choose-tmpl').length && !$this.find('.ib2-choose-bg').length ) {
			var url = '', post_id = parseInt($('#ib2-post-id').val()), group_id = parseInt($('#ib2-group-id').val());
			if ( !isNaN(post_id) && post_id != 0 ) {
				url = ib2editorurl;
				url += '&post=' + $('#ib2-post-id').val() + '&action=edit&template_id=' + $this.data('id');
			} else {
				url = ib2pageurl;
				url += '&ib2action=newpage&template_id=' + $this.data('id');
				
				if ( !isNaN(group_id) && group_id != 0 ) {
					url += '&group_id=' + $('#ib2-group-id').val();	
				}
			}
			
			var funnel_class = '';
			if ( funnel_template == 1 && pagenow == 'instabuilder-2-0_page_ib2-funnel' )
				funnel_class = ' ib2-funnel-tmpl';
				
			if ( funnel_template_e == 1 && pagenow == 'instabuilder-2-0_page_ib2-funnel' )
				funnel_class = ' ib2-funnel-tmpl-e';
				
			if ( new_funnel == 1 && pagenow == 'instabuilder-2-0_page_ib2-funnel' )
				funnel_class = ' new-funnel-tmpl';
				
			if ( new_variant == 1 )
				funnel_class = ' create-new-page-variant';
				
			$this.append('<div class="ib2-choose-bg"></div>');
			$this.append('<a href="' + url + '" data-template-id="' + $this.data('id') + '" class="ib2-btn ib2-btn-primary btn btn-success ib2-choose-tmpl' + funnel_class + '">Choose</a>');
			$this.append('<a href="' + ib2editorurl + '&post=' + ib2previewid + '&action=preview&template_id=' + $this.data('id') + '" target="_blank" class="ib2-btn ib2-btn-default btn btn-default ib2-preview-tmpl">Preview</a>');
			$this.append('<a href="#" data-template-id="' + $this.data('id') + '" class="ib2-btn ib2-btn-danger btn btn-danger ib2-delete-tmpl">Delete</a>');
			
			var top  = (($this.height() / 2) - ($this.find('.ib2-choose-tmpl').outerHeight() / 2));
			var left = (($this.width() / 2) - ($this.find('.ib2-choose-tmpl').outerWidth() / 2));

			if ( top < 0 ) top = 0;
			if ( left < 0 ) left = 0;
		
			$this.find('.ib2-choose-bg').css({
				'position': 'absolute',
				'background-color': '#FFF',
				'opacity': '0.6',
				'width': $this.width() + 'px',
				'height': $this.height() + 'px',
				'left': 0,
				'top': 0,
				'z-index': '8'
			});
			
			$this.find('.ib2-choose-tmpl').css({
				'position': 'absolute',
				'display': 'block',
				'left': left,
				'top': (top - 40),
				'z-index': '10'
			});
			
			$this.find('.ib2-preview-tmpl').css({
				'position': 'absolute',
				'display': 'block',
				'left': left,
				'top': top,
				'z-index': '10'
			});
			
			$this.find('.ib2-delete-tmpl').css({
				'position': 'absolute',
				'display': 'block',
				'left': left,
				'top': (top + 40),
				'z-index': '10'
			});
		} else {
			$this.find('.ib2-choose-bg').show();
			$this.find('.ib2-choose-tmpl').show();
			$this.find('.ib2-preview-tmpl').show();
			$this.find('.ib2-delete-tmpl').show();
		}
	});
	
	$('body').on('mouseleave', '.ib2-tmpl-thumb', function(){
		var $this = $(this);
		$this.find('.ib2-choose-tmpl').remove();
		$this.find('.ib2-choose-bg').remove();
		$this.find('.ib2-preview-tmpl').remove();
		$this.find('.ib2-delete-tmpl').remove();
	});
	
	$('body').on('click', '#ib2-templates-bg', function(e){
		$('body').trigger("close-ib2-templates");
		e.preventDefault();
	});
	
	$('body').on('click', '.ib2-delete-tmpl', function(e){
		var $this = $(this), templateid = $this.data('templateId'), parent = $this.parent();
		if ( confirm("Are you sure you want to delete this template?\nThis action CANNOT be undone.") ) {
			
			parent.hide();
			
			$.post(ajaxurl, {
				action: 'ib2_delete_template',
				template_id: templateid
			}, function(response){
				parent.remove();
			});
			
			
		}
		e.preventDefault();
	});
	
	$(document).keyup(function(e) {
		if ( e.keyCode == 27 && template_open == 1 ) {
			$('body').trigger('close-ib2-templates');
		}
	});
	
	$('.ib2-template-close').click(function(e){
		$('body').trigger("close-ib2-templates");
		e.preventDefault();
	});
	
	$('body').on('close-ib2-templates', function(){
		$('#ib2-templates').fadeOut("medium");
		$('#ib2-templates-bg').fadeOut("slow");
		
		template_open = 0;
		funnel_template = 0;
		$('#ib2-group-id').val(0);
	});
	
	
	$('body').on('change', '#ib2-tmpl-type', function(){
		var $this = $("option:selected", this), type = $this.val(),
		text = $this.text(), keyword = $('#ib2-tmpl-tags').val();
		
		$('#ib2-templates-area').find('h3').text(text + ' Templates');
		$('#ib2-template-loader').show();
		$('#ib2-templates-content').html('');
		
		var newoptions = '<option value=""> -- Sub-Type -- </option>';
		newoptions += subtype_data[type];
		
		$('#ib2-tmpl-subtype').html(newoptions);
		
		$.post(ajaxurl, {
			action: 'ib2_show_templates',
			type: type,
			subtype: '',
			keyword: keyword
		}, function(response){
			$('#ib2-templates-content').html(response);
			$('#ib2-template-loader').hide();
		});
	});
	
	$('body').on('change', '#ib2-tmpl-subtype', function(){
		var $this = $("option:selected", this), subtype = $this.val(),
		type = $('#ib2-tmpl-type').val(), text = $this.text(),
		keyword = $('#ib2-tmpl-tags').val();
		
		$('#ib2-template-loader').show();
		$('#ib2-templates-content').html('');
		
		$.post(ajaxurl, {
			action: 'ib2_show_templates',
			type: type,
			subtype: subtype,
			keyword: keyword
		}, function(response){
			$('#ib2-templates-content').html(response);
			$('#ib2-template-loader').hide();
		});
	});
	
	$('body').on('click', '#find-templates', function(){
		var $this = $(this), subtype = $('#ib2-tmpl-subtype').val(),
		type = $('#ib2-tmpl-type').val(),
		keyword = $('#ib2-tmpl-tags').val();
		
		$('#ib2-template-loader').show();
		$('#ib2-templates-content').html('');
		
		$.post(ajaxurl, {
			action: 'ib2_show_templates',
			type: type,
			subtype: subtype,
			keyword: keyword
		}, function(response){
			$('#ib2-templates-content').html(response);
			$('#ib2-template-loader').hide();
		});
	});
	
	$('.create-funnel-btn').click(function(e){
		$('#new-funnel').show();
		$('.non-funnel-form').hide();
		
		$('html, body').animate({
			scrollTop: $("#page-groups").offset().top
	 	}, 1000);
	 	
	 	$(this).hide();
	 	$('.funnel-box-title').text('Create Funnel');
		$('#new-funnel-name').focus();

		e.preventDefault();
	});
	
	$('.cancel-create-funnel').click(function(e){
		$('#new-funnel').hide();
		$('.non-funnel-form').show();
		
		$('html, body').animate({
			scrollTop: $("#page-groups").offset().top
	 	}, 1000);
	 	
	 	$('.create-funnel-btn').show();
	 	$('.funnel-box-title').text('Funnels');
		e.preventDefault();
	});
	
	var funnel = {};
	funnel.optin = ['optin', 'confirmation', 'thank_you'];
	funnel.sales = ['sales', 'upsell', 'downsell', 'download'];
	funnel.webinar = ['webinar', 'webinar_thanks'];
	funnel.launch = ['optin', 'launch_video', 'launch_video', 'launch_video', 'sales', 'download'];
	
	var tmpl = '';
	tmpl += '<div class="funnel-page col-sm-3">';
		tmpl += '<div class="col-sm-9 funnel-window">';
			tmpl += '<div class="funnel-img"><img src="' + $('#ib2-img-folder').val() + 'window.png" class="img-responsive" /></div>';
			tmpl += '<div class="funnel-btns text-center"><a href="#" role="button" class="btn btn-primary btn-sm funnel-choose-template" title="Choose Template"><i class="fa fa-file-image-o"></i></a> <a href="#" role="button" class="btn btn-success btn-sm add-funnel-page" title="Add New Page"><i class="fa fa-plus"></i></a>  <a href="#" role="button" class="btn btn-danger btn-sm remove-funnel-page" title="Remove This Page"><i class="fa fa-minus"></i></a></div>';
			tmpl += '<div class="funnel-field">';
				tmpl += '<div class="form-group text-center">';
					tmpl += '<select class="form-control input-sm funnel-page-type" name="funnel_page_type[]">';
						tmpl += '<option value="optin">Squeeze Page</option>';
						tmpl += '<option value="sales">Sales Page</option>';
						tmpl += '<option value="webinar">Webinar Signup</option>';
						tmpl += '<option value="webinar_thanks">Webinar Thank You</option>';
						tmpl += '<option value="confirmation">Confirmation</option>';
						tmpl += '<option value="thank_you">Thank You</option>';
						tmpl += '<option value="download">Download Page</option>';
						tmpl += '<option value="launch_video">Launch Video</option>';
						tmpl += '<option value="upsell">Upsell Page</option>';
						tmpl += '<option value="downsell">Downsell Page</option>';
					tmpl += '</select>';
				tmpl += '</div>';
				tmpl += '<input type="hidden" class="funnel-page-template" name="funnel_page_template[]" value="none" />';
			tmpl += '</div>';
		tmpl += '</div>';
		tmpl += '<div class="col-sm-3 funnel-arrow">';
			tmpl += '<img src="' + $('#ib2-img-folder').val() + 'pointer-arrow.png" class="img-responsive" />';
		tmpl += '</div>';
	tmpl += '</div>';
	
	$('#new-funnel-type').change(function(){
		var opt = $("option:selected", this).val(),
		fnl = funnel[opt];
		
		$('#funnel-draw').html('');
		for ( var i = 0; i < fnl.length; i++ ) {
			var l = i+1;
			
			$('#funnel-draw').append(tmpl);
			
			if ( i == 0 ) {
				$('.funnel-page').eq(i).find('.remove-funnel-page').remove();
			}
			
			$('.funnel-page').eq(i).find('.funnel-page-type').val(fnl[i]);
			
			if ( l == fnl.length ) {
				$('.funnel-page').eq(i).find('.funnel-arrow > img').hide();
			}
		}
		
		$('#new-funnel-tsets').html('');
		var newoptions = '<option value=""> -- None -- </option>';
		newoptions += sets[opt];
		
		$('#new-funnel-tsets').html(newoptions).val('');
	}).change();
	
	//var tempImage = {};
	$('#new-funnel-tsets').change(function(){
		var opt = $("option:selected", this).val(), type = $('#new-funnel-type').val();
		
		$('#ib2-funnel-submit').button('loading');
		$('#new-funnel-tsets').attr('disabled', true);
		$('.funnel-btns').hide();
		
		var pgs = $('.funnel-page-type').length;	
		$('.funnel-page-type').each(function(i){
			var f= $(this), j = i+1, pg_type = $("option:selected", this).val();
			if ( opt == '' ) {
				f.parents('.funnel-window').find('.funnel-img > img').attr('src', $('#ib2-img-folder').val() + 'window.png');
				f.parent().parent().find('.funnel-page-template').val('none');
				$('#ib2-funnel-submit').button('reset');
				$('#new-funnel-tsets').removeAttr('disabled');
				$('.funnel-btns').show();
			} else {
				f.after('<img src="' + $('#ib2-img-folder').val() + 'ajax-loader.gif' + '" />');
				f.hide();
				$.post(ajaxurl, {
					action: 'ib2_templateset_data',
					page_type: pg_type,
					sets: opt
				}, function(response){
					if ( response.success ) {
						f.parents('.funnel-window').find('.funnel-img > img').attr('src', response.img);
						f.parent().parent().find('.funnel-page-template').val(response.template_id);
					}
					if ( j == pgs ) {
						$('#ib2-funnel-submit').button('reset');
						$('#new-funnel-tsets').removeAttr('disabled');
						$('.funnel-btns').show();
					}
					f.next('img').remove();
					f.show();
				});
			}
		});
	});
	
	$('body').on('click', '.add-funnel-page', function(e){
		var $this = $(this), total = $('.funnel-page').length,
		pos = $this.parents('.funnel-page').index() + 1;
		
		$this.parents('.funnel-page').after(tmpl);
		
		if ( total == pos ) {
			$this.parents('.funnel-page').find('.funnel-arrow > img').show();
			$('.funnel-page').eq(pos).find('.funnel-arrow > img').hide();
		}
		
		e.preventDefault();
	});
	
	$('body').on('click', '.remove-funnel-page', function(e){
		var $this = $(this), total = $('.funnel-page').length,
		pos = $this.parents('.funnel-page').index();

		if ( total == (pos+1) ) {
			$('.funnel-page').eq(pos-1).find('.funnel-arrow > img').hide();
		}
		
		$this.parents('.funnel-page').remove();
		e.preventDefault();
	});
	
	$('.funnel-new-page').click(function(e){
		if ( $('#new-funnel-page').is(":visible") ) {
			$('#new-funnel-page').hide();
		} else {
			$('#new-funnel-page').show();
			$('html, body').animate({
    			scrollTop: $("#new-funnel-page").offset().top
 			}, 1000);
		}
		
		e.preventDefault();
	});
	
	$('body').on('click', '.funnel-choose-template', function(e){
		var $this = $(this);
		
		if ( template_open == 1 ) return false;
		
		if ( !$('#ib2-templates-bg').length ) {
			$('body').append('<div id="ib2-templates-bg"></div>');
		}
		
		var pos  = 'absolute';
		var top  = (($(window).height() / 2) - ($('#ib2-templates').outerHeight() / 2));
		var left = (($(window).width() / 2) - ($('#ib2-templates').outerWidth() / 2));

		top = top + $(window).scrollTop();

		if ( top < 0 ) top = 0;
		if ( left < 0 ) left = 0;
		
		$('#ib2-templates').css({
	    	'position' : pos,
	    	'top' : top,
	    	'left' : left
		});
		
		$('#ib2-templates-bg').fadeIn("fast");
		$('#ib2-templates').fadeIn("medium");
		
		var after = false;
		if ( $this.attr('data-page-type') )
			after = true;
			
		var page_type;	
		if ( after == true ) {
			var post_id = $this.data('postid'), group_id = $this.data('groupid');
			page_type = $this.data('pageType');
			$('#ib2-post-id').val(post_id);
			$('#ib2-group-id').val(group_id);
		} else
			page_type = $this.parent().parent().find('.funnel-field').find('.funnel-page-type').val();
			
		var _page_type = page_type;
		if ( page_type == 'upsell' || page_type == 'downsell' ) page_type = 'sales';
		if ( page_type == 'webinar_thanks' ) page_type = 'webinar';
		if ( page_type == 'confirmation' || page_type == 'thank_you' || page_type == 'download' ) page_type = 'others';
		if ( page_type == 'launch_video' ) page_type = 'launch';
		
		$('#ib2-new-scratch').hide();
		
		$('#ib2-tmpl-tags').val('');
		$('#ib2-tmpl-type').val(page_type);
		if ( _page_type == 'confirmation' ) {
			$('#ib2-tmpl-subtype').val(_page_type).trigger('change');
		} else if ( _page_type == 'thank_you' ) {
			$('#ib2-tmpl-subtype').val('thankyou').trigger('change');
		} else if ( _page_type == 'download' ) {
			$('#ib2-tmpl-subtype').val('download').trigger('change');
		} else if ( _page_type == 'webinar' ) {
			$('#ib2-tmpl-subtype').val('webinarsignup').trigger('change');
		} else if ( _page_type == 'webinar_thanks' ) {
			$('#ib2-tmpl-subtype').val('webinarthanks').trigger('change');
		} else if ( _page_type == 'upsell' || _page_type == 'downsell' ) {
			$('#ib2-tmpl-subtype').val('otosales').trigger('change');
		} else {
			$('#ib2-tmpl-type').trigger('change');
		}
		
		funnel_index = ( after === false ) ? $this.parents('.funnel-page').index() : 0;
		template_open = 1;
		funnel_template = ( after === false ) ? 1 : 0;
		funnel_template_e = ( after === false ) ? 0 : 1;
		e.preventDefault();
	});
	
	$('body').on('click', '.ib2-funnel-tmpl', function(e){
		e.preventDefault();
		
		var $this = $(this), img = $this.parent().find('img').attr('src');
		$('body').trigger("close-ib2-templates");
		
		$('.funnel-page').eq(funnel_index).find('.funnel-img > img').attr('src', img).addClass('funnel-img-border');
		$('.funnel-page').eq(funnel_index).find('.funnel-page-template').val($this.data('templateId'));
		
	});
	
	$('body').on('click', '.ib2-funnel-tmpl-e', function(e){
		var $this = $(this), url = ib2pageurl,
		post_id = $('#the_post_id').val(), group_id = $('#ib2-group-id').val();
		
		if ( $('#ib2-post-id').length ) {
			post_id = $('#ib2-post-id').val();
		}
		
		url += '&ib2action=default_funnel_template&mode=template&template_id=' + $this.data('templateId') + '&post_id=' + post_id + '&group_id=' + group_id;
		window.location.href = url;
		e.preventDefault();
	});
	
	$('form#funnel-form').submit(function(e){
		var $this = $(this);
		$('#ib2-funnel-submit').button('loading');
		if ( $('#new-funnel-name').val() == '' ) {
			alert('ERROR: Please create a name for your new funnel.');
			$('#new-funnel-name').focus();
			$('#ib2-funnel-submit').button('reset');
			return false;
		}
		
		$.post(ajaxurl, {
			action: 'ib2_check_group_name',
			name: $('#new-funnel-name').val()
		}, function(response){
			if ( response == 'exists' ) {
				alert('ERROR: A funnel or group with the same name is already exists. Please choose another funnel name.');
				$('#new-funnel-name').val('');
				$('#new-funnel-name').focus();
				return false;
			} else {
				document.getElementById('funnel-form').submit();
				return true;
			}
			$('#ib2-funnel-submit').button('reset');
		});
		
		e.preventDefault();
	});
	
	$('body').on('click', '.new-page-tmpl', function(e){
		var $this = $(this);
		
		if ( template_open == 1 ) return false;
		
		if ( !$('#ib2-templates-bg').length ) {
			$('body').append('<div id="ib2-templates-bg"></div>');
		}
		
		var pos  = 'absolute';
		var top  = (($(window).height() / 2) - ($('#ib2-templates').outerHeight() / 2));
		var left = (($(window).width() / 2) - ($('#ib2-templates').outerWidth() / 2));

		top = top + $(window).scrollTop();

		if ( top < 0 ) top = 0;
		if ( left < 0 ) left = 0;
		
		$('#ib2-templates').css({
	    	'position' : pos,
	    	'top' : top,
	    	'left' : left
		});
		
		$('#ib2-templates-bg').fadeIn("fast");
		$('#ib2-templates').fadeIn("medium");
		
		var page_type = $('#new_page_type').val();
		if ( page_type == 'upsell' || page_type == 'downsell' ) page_type = 'sales';
		if ( page_type == 'order' || page_type == 'confirmation' || page_type == 'thank_you' || page_type == 'download' ) page_type = 'others';
		if ( page_type == 'launch_video' ) page_type = 'launch';
		
		$('#ib2-new-scratch').hide();
		
		if ( page_type != $('#ib2-tmpl-type').val() )
			$('#ib2-tmpl-type').val(page_type).trigger('change');
		
		template_open = 1;
		new_funnel = 1;
		e.preventDefault();
	});
	
	$('body').on('click', '.new-funnel-tmpl', function(e){
		e.preventDefault();
		
		var $this = $(this), img = $this.parent().find('img').attr('src');
		$('body').trigger("close-ib2-templates");
		
		if ( !$('#new-page-tmpl-preview > img').length ) {
			$('#new-page-tmpl-preview').append('<img class="img-responsive" />');
		}
		$('#new-page-tmpl-preview > img').attr('src', img).addClass('funnel-img-border');
		$('#new_template').val($this.data('templateId'));
		
	});
})(jQuery);
