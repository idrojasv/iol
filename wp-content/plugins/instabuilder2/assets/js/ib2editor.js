;(function($) {
	var ib2_file_frame,
	frame_data,
	elToDelete,
	btnFontSize,
	newFontSize,
	tempVideoSrc,
	cdDate,
	featherEditor = null,
	txtEd = null,
	destroyed = false,
	editor_loaded = 0,
	$is_drag = 0, 
	$is_text_edit = 0,
	$is_popup = 0,
	inTextEdit = 0,
	quiz_dropped = 0,
	opt3_dropped = 0,
	trafficleft = 0,
	changeTemplate = 0,
	settingsOpening = 0,
	exitingEditor = false,
	startStackType = {},
	startStack = $('#screen-container').html(),
	startStackGlobal = {},
	canUndo = false,
	canRedo = false,
	lastStackAction = 'none',
	doingStack = false,
	stackNum = -1,
	stackSaveNum = -1,
	stackData = [],
	stackGlobal = [],
	stackType = [],
	lastautosave = Math.floor(Date.now()/1000),
	$is_autosave = 0,
	$is_copying = 0,
	fieldenc = false;
	
	var coldrag = null;			//reference to the current grip that is being dragged
	var sections = [];			// array of the already processed tables (table.id as key)
	var coldisabled = [];
	var	colcount = 0;			//internal count to create unique IDs when needed.
	var colsign  = 'ib2ColResizer';
	
	// shortcuts
	var I = parseInt;
	var M = Math;
	var ie =$.browser.msie;
	
	var circleSeconds = {};
    var circleMinutes = {};
    var circleHours = {};
    var circleDays = {};

    var layerSeconds = {};
    var layerMinutes = {};
    var layerHours = {};
    var layerDays = {};
    
    var circleBgSeconds = {};
    var circleBgMinutes = {};
    var circleBgHours = {};
    var circleBgDays = {};

    var layerBgSeconds = {};
    var layerBgMinutes = {};
    var layerBgHours = {};
    var layerBgDays = {};
    
    var circularTime = {};

	if ( $('#ib2-aviary-key').val() != '' ) {
		var avkey = $.trim($('#ib2-aviary-key').val());
		// Image Editor Config
		featherEditor = new Aviary.Feather({
			apiKey: avkey,
			apiVersion: 3,
			theme: 'dark', // Check out our new 'light' and 'dark' themes!
			tools: 'all',
			appendTo: 'ib2-aviary',
			onSave: function(imageID, newURL) {
				var type = $('#ib2-imed-type').val();
				
				if ( type == 'image' ) {
					ib2_image(imageID, newURL);
				} else if ( type == 'background' ) {
					if ( $('#ib2-imed-id').val() == 'screen-container' ) {
						ib2_body_background('screen-container', newURL);
					} else {
						var boxID = $('#ib2-current-box').val();
						if ( $('#' + boxID).data('el') == 'box' ) {
							ib2_box_background(boxID, newURL);
	  					} else {
	  						ib2_section_background(boxID, newURL);
	  					}
					}
				}

				$.post(ajaxurl, {
					action: 'ib2_aviary_download',
					image: newURL,
					post_id: $('#ib2-post-id').val()
				}, function(response){
	           		if ( response.success ) {
	           			if ( type == 'image' ) {
							ib2_image(imageID, response.url);
						} else if ( type == 'background' ) {
							if ( $('#ib2-imed-id').val() == 'screen-container' ) {
								ib2_body_background('screen-container', response.url);
							} else {
								var boxID = $('#ib2-current-box').val();
								if ( $('#' + boxID).data('el') == 'box' ) {
									ib2_box_background(boxID, response.url);
			  					} else {
			  						ib2_section_background(boxID, response.url);
			  					}
							}
						}
	           		}
				});
			},
			
			onError: function(errorObj) {
				if ( errorObj ) {
					console.log(errorObj);
								
					if ( errorObj.message )
						alert(errorObj.message);
				}
			},
			
			onClose: function(isDirty) {
				if ( isDirty ) {
					alert('You haven\'t save your changes.');	
				}
				
				$('#ib2-image-editor').hide();
				$("#save-background-ui").hide();
			}
	   });
   	}
   
   	// Fix Box Height
   	if ( $('.ib2-box-el').length ) {
   		$('.ib2-box-el').each(function(){
   			var $this = $(this), id = $this.attr('id'),
   			curHeight = $this.find(' > .el-content').css('height');
   			
   			$('#' + id + '-box').css({
   				'min-height': curHeight,
   				'height': 'auto'
   			});
   			
   			$this.find(' > .el-content').css({
   				'min-height': curHeight,
   				'height': 'auto'
   			});
   		});	
   	}
   
	// Initialize Drag & Drop Elements
	$(".ib2-element").draggable({
		connectToSortable: ".ib2-section-content, #screen-container, .ib2-pop-content",
		helper: "clone",
		start: function(event, ui) {
			$is_drag = 1;
		}
	});
	
	$('.ib2-minimize-elements').click(function(e){
		$(this).parent().hide();
		$('.els-maximize').show();
		$('.element-area').slideUp();
		e.preventDefault();
	});
	
	$('.ib2-maximize-elements').click(function(e){
		$(this).parent().hide();
		$('.els-minimize').show();
		$('.element-area').slideDown();
		$('.combo-elements').hide();
		e.preventDefault();
	});
	
	$('.show-combo-els').click(function(e){
		$('.normal-elements').hide();
		$('.combo-elements').slideDown();
		e.preventDefault();
	});
	
	$('.show-basic-els').click(function(e){
		$('.combo-elements').hide();
		$('.normal-elements').slideDown();
		e.preventDefault();
	});
	
	// init IB 2 main function
	ib2_init();
	ib2_hotspot_init();

	// textarea characters limit
	var metadesc_max = parseInt($('#page-desc').attr('maxlength'));
	$("#desc_limit").html( metadesc_max + ' characters remaining');
	$('#page-desc').keyup(function(){
		var txt_length = $(this).val().length;
		var char_remaining = metadesc_max - txt_length;
		
		$("#desc_limit").html( char_remaining + ' characters remaining');
	});
	
	// Page Width Slider
	$('#page-width-slider').slider({
		min: 300,
		max: 1200,
		value: $('#default-page-width').val(),
		slide: function( event, ui ) {
			$('#page-width').val(ui.value);
		},
		change: function( event, ui ) {
			$('#page-width').val(ui.value);
			
			if ( !doingStack ) {
				$('.ib2-section-el').each(function(){
					if ( $(this).hasClass('ib2-inner-section') || $(this).hasClass('ib2-popup') ) return true;
					if ( $(this).hasClass('ib2-wsection-el') ) {
						$(this).find('.el-content-inner').css('width', ui.value + 'px');
						if ( $(this).find('.el-content-inner > .el-cols > .ib2-section-content').hasClass('ib2-section-col') ) {
							$(this).find('.el-content-inner > .ib2-col-grips').css('width', ui.value + 'px');
							onSectionResize($(this).find('.el-content-inner > .el-cols'));
						} else {
							$(this).find('.el-content-inner > .el-cols > .ib2-section-content').css('width', ui.value + 'px');
						}
					} else {
						$(this).css('width', ui.value + 'px');
					}
				});
			
				$("body").trigger("ib2GlobalChange", ["slider", "page-width-slider", ui.value, 'pageWidth']);
			}
		}
	});
	
	$('#screen-container').on('editor_resize', function(){
		if ( $('.ib2-section-el').length ) {
			$('.ib2-section-el').each(function(i){
				if ( $(this).hasClass('ib2-popup') ) return true;
				var element = null;
				if ( $(this).hasClass('ib2-wsection-el')
					&& $(this).find('.el-content-inner > .el-cols > .ib2-section-content').hasClass('ib2-section-col')
				) {
					element = $(this).find('.el-content-inner > .el-cols');	
				} else if ( $(this).find(' > .el-cols > .ib2-section-content').hasClass('ib2-section-col') ) {
					element = $(this).find(' > .el-cols');
				}
				
				if ( element == null ) return true;
				
				onSectionResize(element);
			});
		}
	});
	
	$(window).resize(function(){
		$('#screen-container').css('min-height', $(window).height() + 'px');
		var ed_panel_width = $('#editor-panel').outerWidth();
		$('#editor-panel-inside').css('width', ed_panel_width + 'px');
	}).resize();
	
	// IB2 Page Guide
	$(window).load(function(){
		$('#page-width').val($('#default-page-width').val());
		$('#ib2-page-guide').css('width', $('#default-page-width').val() + 'px');
		
		if ( $('.ib2-popup-bg').length > 1 ) {
			$('.ib2-popup-bg').remove();
			$('#screen-container').append('<div class="ib2-popup-bg"></div>');
		}
		
		$('[data-el*="text"]').addClass("ib2-text-el");
		$('[data-el*="box"]').addClass("ib2-box-el");
		$('[data-el*="tabs"]').addClass("ib2-tabs-el");
		$('#editor-panel-inside-content').perfectScrollbar({
			suppressScrollX: true,
			includePadding: true
		});
		
		// remove unnecessary element
		$('.gr-textarea-btn').parent().remove();
		$('.gr-textarea-btn').remove();
		
		// Powered By
		if ( $('#ib2-powered-enable').val() == 'yes' ) {
			if ( !$('#ib2-powered-by').length ) {
				var aff_url = $('#ib2-powered-link').val(),
				powered_img = $('#ib2-img-folder').val() + 'sprites/instabuilder2-poweredby.png';
			
				if ( aff_url == '' ) aff_url = 'http://instabuilder.com';
				
				$('#ib2-bottom-slider').before('<div id="ib2-powered-by"><a href="' + aff_url + '" target="_blank"><img src="' + powered_img + '" class="img-responsive" /></a></div>');
			}
		} else {
			if ( $('#ib2-powered-by').length ) {
				$('#ib2-powered-by').remove();
			}
		}
		
		// Date Elements 
		if ( $('.ib2-date-el').length ) {
			$('.ib2-date-el').each(function(){
				var d = $(this), format = d.data('format'), offset = d.data('tz');
				
				d.text(moment().tz(offset).format(format));
			});
		}
		
		// Update Old Video Elements
		if ( $('.ib2-video-container').length ) {
			$('.ib2-video-container').each(function(){
				var v = $(this), vID = v.attr('id'), vtype = v.data('videoType');
				if ( v.find('iframe').length ) {
					var src = decodeURIComponent(v.find('iframe').attr('src')),
					vidvars = getUrlVars(src);
					
					videoData[vID] = {};
					videoData[vID].hosted = {};
					videoData[vID].youtube = {};
					videoData[vID].vimeo = {};
					videoData[vID].type = vtype;
					videoData[vID].embed = '';
					videoData[vID].autoplay = ( vidvars['otopelay'] ? vidvars['otopelay'] : 0 );
					videoData[vID].controls = ( vidvars['controls'] ? vidvars['controls'] : 0 );
					videoData[vID].hosted.mp4 = '';
					videoData[vID].hosted.ogg = '';
					videoData[vID].hosted.webm = '';
					videoData[vID].hosted.splash = '';
					videoData[vID].hosted.code = '';
					videoData[vID].youtube.url = '';
					videoData[vID].youtube.id = '';
					videoData[vID].youtube.code = '';
					videoData[vID].vimeo.url = '';
					videoData[vID].vimeo.id = '';
					videoData[vID].vimeo.code = '';
					
					if ( vtype == 'hosted' ) {
						videoData[vID].hosted.mp4 = Base64.encode(vidvars['mp4']);
						videoData[vID].hosted.ogg = Base64.encode(vidvars['ogg']);
						videoData[vID].hosted.webm = Base64.encode(vidvars['webm']);
						videoData[vID].hosted.splash = Base64.encode(vidvars['splash']);
						videoData[vID].hosted.code = Base64.encode(v.find('iframe').attr('src'));
					}
					
					if ( vtype == 'youtube' ) {
						videoData[vID].youtube.url = Base64.encode('http://www.youtube.com/watch?v=' + vidvars['ytid']);
						videoData[vID].youtube.id = vidvars['ytid'];
						videoData[vID].youtube.code = v.find('iframe').attr('src');
					}
					
					if ( vtype == 'vimeo' ) {
						videoData[vID].vimeo.url = Base64.encode('http://vimeo.com/' + vidvars['vmid']);
						videoData[vID].vimeo.id = vidvars['vmid'];
						videoData[vID].vimeo.code = v.find('iframe').attr('src');
					}
					
					v.find('.el-content').removeClass('embed-responsive embed-responsive-16by9').addClass('ib2-video-responsive-class');
					v.find('.el-content').html('<img src="' + $('#ib2-img-folder').val() + 'video-placeholder.png" class="img-responsive vid-placeholder" />');
				}
			});
		}

		ib2_undo_init();
	});

	// Import Page
	$('body').on('click', '.import-file-dialog', function(e){
			$('#ib2-import-file').modal({
				backdrop: true,
				show: true
			});

		e.preventDefault();	
	});
	
	// Change Template
	$('body').on('click', '.change-template', function(e){
		var msg = "**************************\nWARNING\n**************************\nThe current page content and configuration will be lost if you change this page template.\nThis action CANNOT be undone. Are you sure you want to change the template?";
		if ( confirm(msg) ) {
			
			$('#ib2-changetemplate-modal').modal({
				backdrop: true,
				show: true
			});
			
			$('.ib2-tmpls > ul.nav-pills > li.active > a').trigger('click');
			changeTemplate = 1;
		}
		e.preventDefault();	
	});
	
	// Change permalink
	$('.ib2-change-permalink').click(function(e){
		$('#ib2-permalink-modal').modal({
			backdrop: true,
			show: true
		});
		
		e.preventDefault();	
	});
	
	$('body').on('click', '.ib2-edit-permalink', function(e){
		$('.permalink-form').show();
		$('.permalink-display').hide();
		
		$(this).hide();
		e.preventDefault();	
	});
	
	$('body').on('click', '#save-new-permalink', function(e){
		var $this = $(this), current_slug = $('#current-slug').val(),
		new_slug = $('#ib2-new-slug').val();
		
		e.preventDefault();
		
		if ( new_slug == '' || current_slug == new_slug ) {
			$('.permalink-form').hide();
			$('.permalink-display').show();
			$('.ib2-edit-permalink').show();
			$('#ib2-new-slug').val(current_slug);
			return false;
		}
		
		$this.html('<i class="fa fa-spinner fa-spin"></i>').attr('disabled', true);
		$('#ib2-new-slug').attr('disabled', true);
		$.post(ajaxurl, {
			action: 'ib2_change_permalink',
			post_id: $('#ib2-post-id').val(),
			post_title: $('#page-title').val(),
			new_slug: new_slug,
		}, function( response ) {
			if ( response.success ) {
				$('#ib2-new-slug').val(response.post_name);
				$('#editable-post-name').text(response.post_name);
				
				var slug = $('#slug_url').val() + response.post_name + '/';
				$('#ib2-visit-url').attr('href', slug);
			} else {
				$('#ib2-new-slug').val(current_slug);
			}
			$('.permalink-form').hide();
			$('.permalink-display').show();
			$('.ib2-edit-permalink').show();
			
			$this.text('OK').removeAttr('disabled');
			$('#ib2-new-slug').removeAttr('disabled');
			
			$('#permalink-alert').show();
			setTimeout(function(){
				$('#permalink-alert').hide();
			}, 5000);
		});
	});
	
	// Element hover
	$('#main-editor').on('mouseenter', '.ib2-content-el > .el-content, .ib2-section-el, .ib2-text-el, .ib2-countdown-el, .ib2-menu-el, .ib2-quiz-el, .ib2-tabs-el, .ib2-optslide-el, .ib2-date-el, .ib2-shortcode-el', function(e){
		var $this = $(this), elID = $this.attr('id'),
		curResize = $('#ib2-current-resize').val(),
		type = $this.data('el');
		
		if ( $this.hasClass('el-content') ) {
			elID = $this.parent().attr('id');
		}

		var type = $('#' + elID).data('el');
		
		//if ( type == 'countdown' )
			//$('#' + elID).countdown('stop');

		if ( elID == curResize || $is_text_edit == 1 ) {
			return false;
		}

		$('.element-outline').removeClass('element-outline');
		
		if ( type == 'box' || type == 'hline' || type == 'image' || type == 'button' || type == 'share'|| type == 'optin' || type == 'code' || type == 'comment' ) {
			$('#' + elID + ' > .el-content').addClass('element-outline');
		} else {
			$this.addClass('element-outline');
		}
		
		if ( type == 'text' ) {
			if ( !$('#' + elID + '-txtip').length ) {
				var twidth = $('#' + elID).width();
				$('body').append('<div id="' + elID + '-txtip">Double-Click to edit</div>');
				$('#' + elID + '-txtip').css({
					'position': 'absolute',
					'background-color': '#428bca',
					'padding': '5px 10px',
					'color': '#FFFFFF',
					'font-size': '13px',
					'font-weight': 'normal',
					'-webkit-border-radius': '5px',
					'border-radius': '5px',
					'display': 'block',
					'z-index': 999
				});
				
				var tipwidth = $('#' + elID + '-txtip').width(),
				tleft = $('#' + elID).offset().left,
				ttop = $('#' + elID).offset().top,
				tipleft = (tleft+twidth) - tipwidth - 20,
				tiptop = ttop - 22;
				
				$('#' + elID + '-txtip').css({
					'left': tipleft + 'px',
					'top': tiptop + 'px',
				});
			}
		}
	});
	
	$('#main-editor').on('mouseleave', '.ib2-content-el > .el-content, .ib2-section-el, .ib2-text-el, .ib2-countdown-el, .ib2-menu-el, .ib2-quiz-el, .ib2-tabs-el, .ib2-optslide-el, .ib2-date-el, .ib2-shortcode-el', function(){
		var $this = $(this), elID = $this.attr('id'),
		curResize = $('#ib2-current-resize').val();
		
		if ( elID == curResize ) {
			return false;
		}
		
		if ( $this.hasClass('el-content') ) {
			elID = $this.parent().attr('id');
		}
		
		var type = $('#' + elID).data('el');
		
		$this.removeClass('element-outline');
		$this.find('.el-content').removeClass('element-outline');
		
		if ( $('#' + elID + '-txtip').length )
			$('#' + elID + '-txtip').remove();
		
	});
	
	$('#main-editor').on('dblclick', '.ib2-text-el', function(e){
		var $this = $(this), elID = $this.attr('id');
		
		if ( $this.hasClass('el-content') ) {
			elID = $this.parent().attr('id');
		}
		
		var type = $('#' + elID).data('el');
		
		if ( type == 'text' && $is_text_edit == 1 ) return false;
		
		if ( $('#' + elID + '-txtip').length )
			$('#' + elID + '-txtip').remove();
			
		$('.ib2-tab-title').trigger('blur');
		$('.quiz-text-edit').trigger('blur');
		
		$('.edit-tools').remove();
		$this.removeClass('element-outline');
		$this.removeClass('resize-border');
		
		if ( $this.resizable("instance") )
			$this.resizable("destroy");
			
		$('#ib2-current-text').val(elID);
			
		$('#' + elID).css('padding', '10px');
		ib2_destroy();
		ib2CreateEditor(elID);
		$is_text_edit = 1;
	});
	
	$('#main-editor').on('click', '.ib2-content-el > .el-content, .ib2-section-el, .ib2-text-el, .ib2-countdown-el, .ib2-menu-el, .ib2-quiz-el, .ib2-tabs-el, .ib2-optslide-el, .ib2-date-el, .ib2-shortcode-el', function(e){
		var $this = $(this), elID = $this.attr('id');
		
		if ( $this.hasClass('el-content') ) {
			elID = $this.parent().attr('id');
		}
		
		var type = $('#' + elID).data('el');
		
		if ( type == 'text' && $is_text_edit == 1 ) return false;
		
		$('.ib2-tab-title').trigger('blur');
		$('.quiz-text-edit').trigger('blur');

		if ( $('#' + elID + '-txtip').length )
			$('#' + elID + '-txtip').remove();
			
		$('#ib2-selected-element').val(elID);
		
		if ( $('#ib2-current-resize').val() != '' ) {
			var oldID = $('#ib2-current-resize').val();
			
			$('#' + oldID).blur();
			
			if ( $('#' + oldID).resizable("instance") )
				$('#' + oldID).resizable("destroy");
				
			if ( $('#' + oldID + ' > .el-content').resizable("instance") )
				$('#' + oldID + ' > .el-content').resizable("destroy");
				
			$('#' + oldID).removeClass('resize-border');
			$('#' + oldID + ' > .el-content').removeClass('resize-border');
		}
		
		$('#ib2-current-resize').val(elID);

		$is_text_edit = 0;
		if ( txtEd ) {
			txtEd.remove();
			txtEd = null;
			
			$("body").trigger("ib2CommonChange");
		}
		$('.edit-tools').remove();
		
		// Resizable setup
		var rHandles = "n, e, s, w, ne, sw, se, nw";
		var $resize = $('#' + elID);
		if ( type == 'text' || type == 'title' ) {
			rHandles = "e";
		} else if ( type == 'vline' || type == 'wbox' ) {
			rHandles = "n, s";
		} else if ( type == 'section' ) {
			rHandles = "e, s";
		} else if ( type == 'video' ) {
			rHandles = "e";
		} else if ( type == 'wsection' || type == 'spacer' ) {
			rHandles = "s";
		} else if ( type == 'box' || type == 'image' || type == 'button' || type == 'share' || type == 'optin' || type == 'code' ) {
			$resize = $('#' + elID + ' > .el-content');
			var rHandles = "e, s, se";
		} else if ( type == 'hline' || type == 'comment' ) {
			rHandles = "e";
			$resize = $('#' + elID + ' > .el-content');
		}
		
		var winPos = $(window).scrollTop() + $(window).height(),
		elPos = $this.offset().top + $this.height(),
		contentPos = $this.offset(),
		contentTop = contentPos.top,
		contentLeft = contentPos.left,
		toolTop = ( winPos > elPos ) ? ($this.height() + contentTop) - 50 : contentTop - 60,
		toolLeft = $this.width() + contentLeft,
		toolID = elID + '-edit-tools';
		
		if ( !$('#' + toolID).length ) {
			var $tools = $('<div>', { 'class': 'edit-tools btn-group', 'id': toolID });
			$tools.appendTo('#screen-container');
			var toolsHtml = '';
			
			if ( type == 'text' ) {
				toolsHtml += '<button type="button" class="btn btn-info btn-sm ib2-editor-btn ib2-write-btn" title="Edit Text"><i class="fa fa-pencil"></i></button>';
				if ( !$('#' + elID).hasClass('ib2-title-el') )
					toolsHtml += '<button type="button" class="btn btn-success btn-sm ib2-editor-btn ib2-generate-btn" title="Generate TOS, Disclaimer, Privacy"><i class="fa fa-file-text-o"></i></button>';
			}
			
			if ( type != 'optin3' && type != 'spacer' )
				toolsHtml += '<button type="button" class="btn btn-primary btn-sm ib2-editor-btn ib2-edit-btn" title="Edit Configuration"><i class="fa fa-cog"></i></button>';
			
			var showClone = true;
			if ( $('#' + elID).hasClass('ib2-popup') ) showClone = false;
			if ( type == 'quiz' ) showClone = false;
			if ( type == 'optin3' ) showClone = false;
			if ( type == 'countdown' ) showClone = false;
			
			if ( showClone )
				toolsHtml += '<button type="button" class="btn btn-primary btn-sm ib2-editor-btn ib2-copy-btn" title="Copy"><i class="fa fa-copy"></i></button>';
			
			if ( type == 'optin3' ) {
				var disabledPrev = ( $('#' + elID).data('currentSlide') == 1 ) ? ' disabled' : '';
				var disabledNext = ( $('#' + elID).data('currentSlide') == 3 ) ? ' disabled' : '';
				toolsHtml += '<button type="button" class="btn btn-info btn-sm ib2-editor-btn ib2-prevopt-btn" title="Previous Step"' + disabledPrev + '><i class="fa fa-chevron-left"></i></button>';
				toolsHtml += '<button type="button" class="btn btn-info btn-sm ib2-editor-btn ib2-nextopt-btn" title="Next Step"' + disabledNext + '><i class="fa fa-chevron-right"></i></button>';
			}
			
			if ( type == 'countdown' && $('#' + elID).data('end') == 'reveal' && $('#' + elID + '-content').is(":visible") && $('#' + elID).data('mode') != 'evergreen' ) {
				toolsHtml += '<button type="button" class="btn btn-success btn-sm ib2-editor-btn ib2-close-reveal-btn" title="Close Hidden Content"><i class="fa fa-toggle-up"></i></button>';
			}
			
			if ( type == 'countdown' && $('#' + elID).data('end') == 'reveal' && $('#' + elID + '-content').css("display") == 'none' && $('#' + elID).data('mode') != 'evergreen' ) {
				toolsHtml += '<button type="button" class="btn btn-success btn-sm ib2-editor-btn ib2-show-reveal-btn" title="Show Hidden Content"><i class="fa fa-toggle-down"></i></button>';
			}
			
			if ( type == 'box' || type == 'image' || type == 'button' || type == 'share' || type == 'countdown' || type == 'menu' || type == 'optin' || type == 'code' || type == 'comment' || type == 'shortcode' ) {
				toolsHtml += '<button type="button" class="btn btn-warning btn-sm ib2-editor-btn ib2-align-btn" data-align="left" title="Left Position"><i class="fa fa-align-left"></i></button>';
				toolsHtml += '<button type="button" class="btn btn-warning btn-sm ib2-editor-btn ib2-align-btn" data-align="center" title="Center Position"><i class="fa fa-align-center"></i></button>';
				toolsHtml += '<button type="button" class="btn btn-warning btn-sm ib2-editor-btn ib2-align-btn" data-align="right" title="Right Position"><i class="fa fa-align-right"></i></button>';
				
				if ( type == 'image' )
					toolsHtml += '<button type="button" class="btn btn-info btn-sm ib2-editor-btn ib2-hotspot-btn" title="Add HotSpot"><i class="fa fa-circle"></i></button>';
			}
			
			if ( type == 'section' ) {
				var maxCols = 4;
				var disabledMin = ( !$this.find(' > .el-cols > .ib2-section-col').length ) ? ' disabled' : '';
				var disabledPlus = ( $this.find(' > .el-cols > .ib2-section-col').length == maxCols ) ? ' disabled' : '';
				toolsHtml += '<button type="button" class="btn btn-warning btn-sm ib2-editor-btn ib2-remcol-btn" title="Remove Column"' + disabledMin + '><i class="fa fa-minus"></i></button>';
				toolsHtml += '<button type="button" class="btn btn-warning btn-sm ib2-editor-btn ib2-addcol-btn" title="Add Column"' + disabledPlus + '><i class="fa fa-plus"></i></button>';
			}
			
			if ( type == 'wsection' ) {
				var disabledMin = ( !$this.find('.el-content-inner > .el-cols > .ib2-section-col').length ) ? ' disabled' : '';
				var disabledPlus = ( $this.find('.el-content-inner > .el-cols > .ib2-section-col').length == 4 ) ? ' disabled' : '';
				toolsHtml += '<button type="button" class="btn btn-warning btn-sm ib2-editor-btn ib2-remcol-btn" title="Remove Column"' + disabledMin + '><i class="fa fa-minus"></i></button>';
				toolsHtml += '<button type="button" class="btn btn-warning btn-sm ib2-editor-btn ib2-addcol-btn" title="Add Column"' + disabledPlus + '><i class="fa fa-plus"></i></button>';
			}
			
			if ( type == 'tabs' ) {
				var disabledMin = ( $this.find('.tab-pane').length <= 1 ) ? ' disabled' : '';
				var disabledPlus = ( $this.find('.tab-pane').length == 6 ) ? ' disabled' : '';
				toolsHtml += '<button type="button" class="btn btn-warning btn-sm ib2-editor-btn ib2-remtab-btn" title="Remove Tab"' + disabledMin + '><i class="fa fa-minus"></i></button>';
				toolsHtml += '<button type="button" class="btn btn-warning btn-sm ib2-editor-btn ib2-addtab-btn" title="Add Tab"' + disabledPlus + '><i class="fa fa-plus"></i></button>';
			}
			
			if ( type == 'quiz' ) {
				var disabledPrev = ( $('#ib2-current-question').val() == 1 ) ? ' disabled' : '';
				var disabledNext = ( $('#ib2-current-question').val() == 6 ) ? ' disabled' : '';
				
				var q = $('#ib2-current-question').val(),
				count = $('#' + elID + '-' + q + '-answers > .form-group').length;
				
				var disabledMin = ( count == 2 ) ? ' disabled' : '';
				var disabledPlus = ( count == 5 ) ? ' disabled' : '';
				
				if ( q < 6 ) {
					toolsHtml += '<button type="button" class="btn btn-info btn-sm ib2-editor-btn ib2-addque-btn" title="Add Answer"' + disabledPlus + '><i class="fa fa-plus"></i></button>';
					toolsHtml += '<button type="button" class="btn btn-info btn-sm ib2-editor-btn ib2-remque-btn" title="Remove Answer"' + disabledMin + '><i class="fa fa-minus"></i></button>';
				}
				
				toolsHtml += '<button type="button" class="btn btn-warning btn-sm ib2-editor-btn ib2-prevque-btn" title="Previous Question"' + disabledPrev + '><i class="fa fa-chevron-left"></i></button>';
				toolsHtml += '<button type="button" class="btn btn-warning btn-sm ib2-editor-btn ib2-nextque-btn" title="Next Question"' + disabledNext + '><i class="fa fa-chevron-right"></i></button>';
			}
	
			$trashbtn = true;
			if ( type == 'section' && $('#' + elID).hasClass('ib2-popup') ) $trashbtn = false;
			if ( $('#' + elID).hasClass('ib2-slider-el') ) $trashbtn = false;
			
			if ( $trashbtn )
				toolsHtml += '<button type="button" class="btn btn-danger btn-sm ib2-editor-btn ib2-del-btn" href="#" title=""><i class="fa fa-trash-o"></i></button>&nbsp;';
					
			$tools.html(toolsHtml);
			
			var toolWidth = $tools.width();
			toolLeft = toolLeft - toolWidth;
				
			if ( $('#' + elID).hasClass('ib2-slider-el') )
				toolTop = contentTop - 60;
			
			$tools.css({
				'top': toolTop,
				'left': toolLeft
			});
		}
		
		$resize.addClass('resize-border');
		if ( type != 'shortcode' && type != 'countdown' && type != 'tabs' && type != 'quiz' && type != 'menu' && type != 'date' && type != 'optin3' ) {
			var aspectRatio = false;
			if ( type == 'image' && $('#' + elID).attr('data-aspect-ratio') ) {
				if ( $('#' + elID).data('aspectRatio') == 'yes' )
					aspectRatio = true;
			}
			
			$resize
				.resizable({
					handles: rHandles,
					distance: 10,
					aspectRatio: aspectRatio,
					create: function( e, ui ) {
						if ( type == 'section' || type == 'wsection' ) {
							if ( !$('#' + elID).hasClass('ib2-inner-section') ) {
								$(this).resizable("option", "minWidth", 400);
								$(this).resizable("option", "maxWidth", 1200);
							}
						}
					},
					resize: function ( e, ui ) {
						var toolID = elID + '-edit-tools';
						$('#' + toolID).hide();
						
						if ( type == 'image' ) {
							if ( $('#' + elID).data('aspectRatio') == 'yes' ) {
								$('#' + elID).find('img').css({
									'width': ui.size.width + 'px',
									'height': 'auto'
								});
							} else {
								$('#' + elID).find('img').css({
									'width': ui.size.width + 'px',
									'height': ui.size.height + 'px'
								});
							}
							
							$('#' + elID).find('.el-caption').css({
								'width': ui.size.width + 'px',
							});
							
							$('#' + elID).find('.el-content').css({
								'height': 'auto',
							});
							
							//$('#cur-img-width').val(Math.ceil(ui.size.width));
							//$('#cur-img-height').val(Math.ceil(ui.size.height));
						} else if ( type == 'box' ) {
							$('#' + elID + '-box').css({
								'width': ui.size.width + 'px',
								'height': 'auto',
								'min-height': ui.size.height + 'px'
							});
							
							$('#' + elID + ' > .el-content').css({
								'height': 'auto',
								'min-height': ui.size.height + 'px'
							});
							
							//$(this).css('height', 'auto');
							
						} else if ( type == 'comment' ) {
							$('#' + elID + ' > .el-content').css({
								'width': ui.size.width + 'px'
							});
							
						} else if ( type == 'wbox' ) {
							$('#' + elID).css('width', '100%');
							$('#' + elID + '-box').css({
								'width': '100%',
								'height': ui.size.height + 'px'
							});
						} else if ( type == 'hline' ) {
							$('#' + elID).find('.ib2-hline').css({
								'width': ui.size.width + 'px',
							});
							
						} else if ( type == 'spacer' ) {
							$('#' + elID).find('.el-content').css({
								'height': ui.size.height + 'px',
							});
							
						} else if ( type == 'button' ) {
							var lineHeight = Math.ceil(ui.size.height * 0.75);
							$('#' + elID).find('.el-content, a.ib2-button').css({
								'min-width': ui.size.width + 'px',
								'min-height': ui.size.height + 'px',
								'width': 'auto',
								'height': 'auto'
							});
							
						} else if ( type == 'video' ) {
							$this.css({
								'height': 'auto'
							});
						} else if ( type == 'section' ) {
							if ( $('#' + elID).find(' > .ib2-section-col').length ) {
								$('#' + elID).find(' > .ib2-section-col').each(function(i){
									$(this).css({
										'min-height': ui.size.height + 'px'
									});
								});
								onSectionResize($('#' + elID + ' > .el-cols'));
							} else {
								$('#' + elID + '-box').css({
									'min-height': ui.size.height + 'px'
								});
							}
							$('#' + elID).css('height', 'auto');
							
							if ( !$('#' + elID).hasClass('ib2-popup') && !$('#' + elID).hasClass('ib2-inner-section') ) {
								$('.ib2-section-el').each(function(){
									if ( $(this).hasClass('ib2-inner-section') ) return true;
									
									if ( $(this).hasClass('ib2-wsection-el') ) {
										$(this).find('.el-content-inner').css('width', ui.size.width + 'px');
										if ( $(this).find('.el-content-inner > .el-cols > .ib2-section-content').hasClass('ib2-section-col') ) {
											$(this).find('.el-content-inner > .ib2-col-grips').css('width', ui.size.width + 'px');
											onSectionResize($(this).find('.el-content-inner > .el-cols'));
										} else {
											$(this).find('.el-content-inner > .el-cols > .ib2-section-content').css('width', ui.size.width + 'px');
										}
									} else {
										$(this).not('#' + elID).css('width', ui.size.width + 'px');
									}
								});
							}
						} else if ( type == 'wsection' ) {
							if ( $('#' + elID).find('.ib2-section-col').length ) {
								$('#' + elID).find('.ib2-section-col').each(function(i){
									$(this).css({
										'min-height': ui.size.height + 'px'
									});
								});
								onSectionResize($('#' + elID + ' .el-content-inner > .el-cols'));
							} else {
								$('#' + elID + '-box').css({
									'min-height': ui.size.height + 'px'
								});
							}
							$('#' + elID).css('height', 'auto');
						}
					},
					
					stop: function( e, ui ) {
						if ( type == 'section' ) {
							if ( !$('#' + elID).hasClass('ib2-popup') && !$('#' + elID).hasClass('ib2-inner-section') )
								$('#page-width-slider').slider("value", ui.size.width);
							
							if ( $('#' + elID).hasClass('ib2-popup') )
								ib2_popup_position($('#' + elID));
						}
						
						// re-display and re-position element tool
						var toolID = elID + '-edit-tools',
						$position = $this.offset(),
						$top = $position.top,
						$left = $position.left,
						elHeight = $this.height(),
						elWidth = $this.width();
						
						var $winPos = $(window).scrollTop() + $(window).height(),
						$elPos = $top + elHeight,
						toolTop = ( $winPos > $elPos ) ? (elHeight + $top) - 50 : $top - 60,
						toolLeft = (elWidth + $left) - $('#' + toolID).width();
						
						if ( $('#' + toolID).length ) {
							$('#' + toolID).css({
								'top': toolTop + 'px',
								'left': toolLeft + 'px'
							});	
						}
						
						$('#' + toolID).show();
						
						//$("body").trigger("ib2CommonChange");
					},
				});
		}
		
		$this.removeClass('element-outline');
		$('#' + elID).find('.el-content').removeClass('element-outline');

		if ( type == 'optin') {
			$('#' + elID).find('.form-fields').disableSelection();
		}
		
		$('#' + elID).focus();
		
		e.preventDefault();
		e.stopPropagation();
	});

	// REMOTE COPY ELEMENT
	$('body').on('click', '.remote-copy-element', function(e){
		var $this = $(this), elID = $('#ib2-selected-element').val();
		
		if ( $is_copying == 1 ) return false;
		
		if ( elID == '' ) {
			alert('ERROR: Please select an element to be copied.');
			return false;
		}

		$is_copying = 1;
		
		ib2_destroy();
		ib2_hotspot_destroy();
		
		$('#' + elID).clone().appendTo('#remote-copy-temp');
		var content = $('#remote-copy-temp').html();
		$('#remote-copy-temp').html('');
		
		var add_data = '',
		eltype = $('#' + elID).data('el');
		if ( eltype == 'video' ) {
			add_data = videoData[elID];
		}
		
		if ( eltype == 'slides' ) {
			add_data = carouselData[elID];
		}
		
		$this.text('Copying...');
		$.post(ajaxurl, {
			action: 'ib2_remote_copy',
			elid: elID,
			type: eltype,
			content: content,
			dat: add_data
		}, function(response){
			$is_copying = 0;
			$('#ib2-selected-element').val('');
			$this.text('Remote Copy Element');
			alert("Element has been copied.");
			ib2_init();
			ib2_hotspot_init();
		});
		
		e.preventDefault();
	});
	
	// REMOTE PASTE ELEMENT
	$('body').on('click', '.remote-paste-element', function(e){
		var $this = $(this), elID = $('#ib2-selected-element').val();
		
		$is_copying = 1;
		
		ib2_destroy();
		ib2_hotspot_destroy();
		
		$this.text('Checking...');
		$.post(ajaxurl, {
			action: 'ib2_remote_paste',
			el: elID,
		}, function(response){
			if ( response.success ) {
				if ( response.type == 'wbox' || response.type == 'wsection' ) {
					$('#screen-container').append(response.content);
				} else {
					if ( elID != '' ) {
						if ( $('#' + elID).data('el') == 'section' )
							$('#' + elID + ' > .el-content > .ib2-section-content').append(response.content);
						else if ( $('#' + elID).data('el') == 'wsection' )
							$('#' + elID + ' > .el-content > .el-content-inner > .el-cols > .ib2-section-content').append(response.content);
						else
							$('.ib2-section-el').eq(0).find('.ib2-section-content').eq(0).append(response.content);
					} else {
						if ( response.type == 'section' ) {
							$('#screen-container').append(response.content);
						} else {
							$('.ib2-section-el').eq(0).find('.ib2-section-content').eq(0).append(response.content);
						}
					}
				}
				
				if ( response.type == 'video' )
					videoData[response.elid] = response.dat;
					
				if ( response.type == 'slides' )
					carouselData[response.elid] = response.dat;
					
			} else {
				alert("ERROR: There's no element to be pasted.");
			}
			
			$this.text('Remote Paste Element');
			
			$('#ib2-selected-element').val('');
			ib2_init();
			ib2_hotspot_init();
			$is_copying = 0;
		});
		
		e.preventDefault();
	});
	
	$('#main-editor').on('click', function(e){
		var oldID = $('#ib2-current-resize').val(),
		clickedID = e.target.id;
		
		if ( oldID != '' && oldID != clickedID ) {
			$('.ib2-tab-title').trigger('blur');
			$('.quiz-text-edit').trigger('blur');
			
			if ( $('#' + oldID).data('el') == 'text' ) {
				$('.ib2-text-el').trigger('blur');
				$is_text_edit = 0;
			}

			if ( $('#' + oldID).resizable("instance") )
				$('#' + oldID).resizable("destroy");
			
			if ( $('#' + oldID + ' > .el-content').resizable("instance") )
				$('#' + oldID + ' > .el-content').resizable("destroy");
				
			$('#' + oldID).removeClass('resize-border');
			$('#' + oldID + ' > .el-content').removeClass('resize-border');
			
			$('#' + oldID + '-edit-tools').remove();
			$('#ib2-current-resize').val('');
		}
	});
	
	// Disable links inside the editor...
	$('#screen-container').on('click', '.ib2-content-el a', function(e){
		if ( !$(this).hasClass('ib2-editor-btn') ) {
			e.preventDefault();
		}
	});
	
	// ======================== TAB SETTINGS ========================
	
	$('.open-tab-settings').each(function(){
		$(this).click(function(e){
			var $this = $(this), id = $this.attr('href');
			
			if ( $this.hasClass('tab-settings-active') ) {
				$this.parent().parent().find('li').each(function(){
					var l = $(this), $show = true;
					if ( l.attr('data-hide-when') ) {
						var hw = l.data('hideWhen').split(":"),
						k = hw[0], v = hw[1];
						if ( $('#' + k).val() == v )
							$show = false;
					}
					
					if ( $('#back-panel').is(":visible") && $('#back-panel > button').attr('data-settings') ) {
						var childType = $('#back-panel > button').data('settings');
						if ( childType == 'optin' && l.hasClass('button-el-only') )
							$show = false;
					}
					
					if ( l.parent().hasClass('box-settings-menu') && l.hasClass('not-wbox') ) {
						var elID = $('#ib2-current-box').val();
						if ( $('#' + elID).data('el') == 'wbox' )
							$show = false;
					}
					
					if ( l.parent().hasClass('box-settings-menu') && l.hasClass('not-popup') ) {
						var elID = $('#ib2-current-box').val();
						if ( $('#' + elID).hasClass('ib2-popup') )
							$show = false;
							
						if ( $('#' + elID).hasClass('ib2-slider-el') )
							$show = false;
					}
					
					if ( $show )
						l.show();
				});
				
				$this.attr('title', 'Click to open settings').removeClass('tab-settings-active');
				$this.find('i').attr('class', 'fa fa-chevron-right');
				$(id).hide();
			} else {
				$this.parent().parent().find('li').hide();
				$this.parent().show();
				$this.attr('title', 'Click to Back').addClass('tab-settings-active');
				$this.find('i').attr('class', 'fa fa-chevron-left');
				
				$(id).show();
				
				$('#editor-panel-inside-content').perfectScrollbar('update');
			}
			
			e.preventDefault();
		});
	});
	
	$( "#border-accordion" ).accordion({
		heightStyle: "content",
		collapsible: true
	});

	// ======================== COLOR PICKERS ========================
	// Global Font Color
	$('#body-text-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {

    		var style = '#screen-container { font-family: ' + $('#body-text-font').val() + '; font-size: ' + $('#body-text-size').val() + 'px; color:' + ui.color.toString() + '; }';
			style += '#screen-container a { color: ' + $('#body-link-color').val() + '; }';
			style += '#screen-container a:hover, #screen-container a:focus { color: ' + $('#body-link-hover-color').val() + '; }';

        	$("#editor-body-typo").html(style);
	        
	        if ( !doingStack ) {
	        	$("body").trigger("ib2GlobalChange", ["picker", "body-text-color", ui.color.toString(), 'fontColor']);
	       }
	    }
	});
	
	// Global Link Color
	$('#body-link-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	
	    	var style = '#screen-container { font-family: ' + $('#body-text-font').val() + '; font-size: ' + $('#body-text-size').val() + 'px; color:' + $('#body-text-color').val() + '; }';
			style += '#screen-container a { color: ' + ui.color.toString() + '; }';
			style += '#screen-container a:hover, #screen-container a:focus { color: ' + $('#body-link-hover-color').val() + '; }';

	        $("#editor-body-typo").html(style);
		    if ( !doingStack ) {    
		        $("body").trigger("ib2GlobalChange", ["picker", "body-link-color", ui.color.toString(), 'linkColor']);
			}
	    }
	});
	
	// Global Link Hover Color
	$('#body-link-hover-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	
	    	var style = '#screen-container { font-family: ' + $('#body-text-font').val() + '; font-size: ' + $('#body-text-size').val() + 'px; color:' + $('#body-text-color').val() + '; }';
			style += '#screen-container a { color: ' + $('#body-link-color').val() + '; }';
			style += '#screen-container a:hover, #screen-container a:focus { color: ' + ui.color + '; }';

	        $("#editor-body-typo").html(style);
	        
		    if ( !doingStack ) {
		        $("body").trigger("ib2GlobalChange", ["picker", "body-link-hover-color", ui.color.toString(), 'linkHoverColor']);
	    	}
	    }
	});
	
	// Page Background Color
	$('#background-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	
		    $("#screen-container").css('background-color', ui.color.toString());
		    if ( !doingStack ) {   
		        $("body").trigger("ib2GlobalChange", ["picker", "background-color", ui.color.toString(), 'backgroundColor']);
	    	}
	    }
	});
	
	// Hotspot Background Color
	$('#hotspot-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	var elID = $("#ib2-current-hotspot").val();
	    	
		    $("#" + elID).css('background-color', ui.color.toString());
		    if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Hotspot Border Color
	$('#hotspot-border-color').instaColorPicker({
	    hide: true,
	    palettes: ['#c09853', '#b94a48', '#468847', '#3a87ad', '#ccc', '#e5e5e5'],
	    change: function( event, ui ) {
	    	var elID = $("#ib2-current-hotspot").val();
	    	
		    $("#" + elID).css('border-color', ui.color.toString());
		    if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});

	// Quiz Question font color
	$('#quiz-question-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-quiz').val();
	        $('#' + elID).find('.ib2-quiz-page').each(function(i){
	        	var c = $(this);
	        	c.find('h3').css('color', ui.color.toString());
	        });
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Quiz Answers font color
	$('#quiz-answer-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-quiz').val();
	        $('#' + elID).find('.ib2-quiz-page').each(function(i){
	        	var c = $(this);
	        	c.find('.ib2-answer-list').css('color', ui.color.toString());
	        });
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Optin Fields
	$('#field-background-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#ffffff'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-optin').val();
	        $('#' + elID).find('input[type=text], select, textarea').css('background-color', ui.color.toString());
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Optin Fields
	$('#field-border-color').instaColorPicker({
	    hide: true,
	    palettes: ['#4a4a4a', '#666666', '#a7a7a7', '#cccccc', '#e5e5e5', '#ffffff'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-optin').val();
	        $('#' + elID).find('input[type=text], select, textarea').css('border-color', ui.color.toString());
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Menu Background Color
	$('#menu-background').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-menu').val(), bgColor = ui.color.toString(), css = '';
	        
			if ( $('#menu-style').val() != 'plain' && $('#menu-style').val() != 'plain-pipe' ) {
				css += '#' + elID + ' ul.ib2-navi { background-color: ' + bgColor + '; } ';
				
				if ( $('#menu-style').val() == 'glossy' ) {
					var glossyColor = getAltColor(bgColor, 'lighter');
					css += '#' + elID + ' ul.ib2-navi { background-image: linear-gradient(to bottom, ' + glossyColor + ' 0%, ' + bgColor + ' 100%); ';
			    	css += 'background-image: -moz-linear-gradient(top, ' + glossyColor + ' 0%, ' + bgColor + ' 100%); } ';
				}
			
				var textColor = $('#menu-text-color').val(),
				subTextColor = $('#sub-menu-text-color').val(),
				subBgColor = $('#sub-menu-background').val(),
				subBorderTop = getAltColor(subBgColor, 'lighter'),
				subBorderBot = getAltColor(subBgColor, 'darker');
		
				css += '#' + elID + ' ul.ib2-navi li a { color: ' + textColor + '; } ';
				css += '#' + elID + ' ul.ib2-navi ul { background-color: ' + subBgColor + '; } ';
				css += '#' + elID + ' ul.ib2-navi ul li { border-top-color: ' + subBorderTop + '; border-bottom-color: ' + subBorderBot + '; } ';
				css += '#' + elID + ' ul.ib2-navi ul li a { color: ' + subTextColor + '; } ';
			
				$('#' + elID + '-css').html(css);
				
				if ( !doingStack )
		        	$("body").trigger("ib2CommonChange");
			}
	    }
	});
	
	$('#menu-text-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-menu').val(), bgColor = $('#menu-background').val(), css = '';
	        
	        if ( $('#menu-style').val() != 'plain' && $('#menu-style').val() != 'plain-pipe' ) {
				css += '#' + elID + ' ul.ib2-navi { background-color: ' + bgColor + '; } ';
				
				if ( $('#menu-style').val() == 'glossy' ) {
					var glossyColor = getAltColor(bgColor, 'lighter');
					css += '#' + elID + ' ul.ib2-navi { background-image: linear-gradient(to bottom, ' + glossyColor + ' 0%, ' + bgColor + ' 100%); ';
			    	css += 'background-image: -moz-linear-gradient(top, ' + glossyColor + ' 0%, ' + bgColor + ' 100%); } ';
				}
			}
			
			var textColor = ui.color.toString(),
			subTextColor = $('#sub-menu-text-color').val(),
			subBgColor = $('#sub-menu-background').val(),
			subBorderTop = getAltColor(subBgColor, 'lighter'),
			subBorderBot = getAltColor(subBgColor, 'darker');
	
			css += '#' + elID + ' ul.ib2-navi li a { color: ' + textColor + '; } ';
			css += '#' + elID + ' ul.ib2-navi ul { background-color: ' + subBgColor + '; } ';
			css += '#' + elID + ' ul.ib2-navi ul li { border-top-color: ' + subBorderTop + '; border-bottom-color: ' + subBorderBot + '; } ';
			css += '#' + elID + ' ul.ib2-navi ul li a { color: ' + subTextColor + '; } ';
		
			$('#' + elID + '-css').html(css);
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	$('#menu-hover-background').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-menu').val(), hoverBgColor = ui.color.toString(), css = '';
	        
			if ( $('#menu-style').val() != 'plain' && $('#menu-style').val() != 'plain-pipe' ) {
				css += '#' + elID + ' ul.ib2-navi li:hover, ul.ib2-navi li:focus { background-color: ' + hoverBgColor + ' !important; } ';
				if ( $('#menu-style').val() == 'glossy' ) {
					var glossyColor = getAltColor(hoverBgColor, 'darker');
					css += '#' + elID + ' ul.ib2-navi li:hover, ul.ib2-navi li:focus { background-image: linear-gradient(to bottom, ' + glossyColor + ' 5%, ' + hoverBgColor + ' 100%) !important; ';
			    	css += 'background-image: -moz-linear-gradient(top, ' + glossyColor + ' 5%, ' + hoverBgColor + ' 100%) !important; } ';
				}
			}
			css += '#' + elID + ' ul.ib2-navi > li > a:hover, #' + elID + ' ul.ib2-navi > li > a:focus { color: ' + $('#menu-hover-text-color').val() + ' !important; } ';
	    
	    	$('#' + elID + '-hover-css').html(css);
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	$('#menu-hover-text-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-menu').val(), hoverBgColor = $('#menu-hover-background').val(), css = '';
	        
			if ( $('#menu-style').val() != 'plain' && $('#menu-style').val() != 'plain-pipe' ) {
				css += '#' + elID + ' ul.ib2-navi li:hover, ul.ib2-navi li:focus { background-color: ' + hoverBgColor + ' !important; } ';
				if ( $('#menu-style').val() == 'glossy' ) {
					var glossyColor = getAltColor(hoverBgColor, 'darker');
					css += '#' + elID + ' ul.ib2-navi li:hover, ul.ib2-navi li:focus { background-image: linear-gradient(to bottom, ' + glossyColor + ' 5%, ' + hoverBgColor + ' 100%) !important; ';
			    	css += 'background-image: -moz-linear-gradient(top, ' + glossyColor + ' 5%, ' + hoverBgColor + ' 100%) !important; } ';
				}
			}
			css += '#' + elID + ' ul.ib2-navi > li > a:hover, #' + elID + ' ul.ib2-navi > li > a:focus { color: ' + ui.color.toString() + ' !important; } ';
	    
	    	$('#' + elID + '-hover-css').html(css);
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    	
	    }
	});
	
	$('#sub-menu-background').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	        var elID = $('#ib2-current-menu').val(), bgColor = $('#menu-background').val(), css = '';
	        
	        if ( $('#menu-style').val() != 'plain' && $('#menu-style').val() != 'plain-pipe' ) {
				css += '#' + elID + ' ul.ib2-navi { background-color: ' + bgColor + '; } ';
				
				if ( $('#menu-style').val() == 'glossy' ) {
					var glossyColor = getAltColor(bgColor, 'lighter');
					css += '#' + elID + ' ul.ib2-navi { background-image: linear-gradient(to bottom, ' + glossyColor + ' 0%, ' + bgColor + ' 100%); ';
			    	css += 'background-image: -moz-linear-gradient(top, ' + glossyColor + ' 0%, ' + bgColor + ' 100%); } ';
				}
			}
			
			var textColor = $('#menu-text-color').val(),
			subTextColor = $('#sub-menu-text-color').val(),
			subBgColor = ui.color.toString(),
			subBorderTop = getAltColor(subBgColor, 'lighter'),
			subBorderBot = getAltColor(subBgColor, 'darker');
	
			css += '#' + elID + ' ul.ib2-navi li a { color: ' + textColor + '; } ';
			css += '#' + elID + ' ul.ib2-navi ul { background-color: ' + subBgColor + '; } ';
			css += '#' + elID + ' ul.ib2-navi ul li { border-top-color: ' + subBorderTop + '; border-bottom-color: ' + subBorderBot + '; } ';
			css += '#' + elID + ' ul.ib2-navi ul li a { color: ' + subTextColor + '; } ';
		
			$('#' + elID + '-css').html(css);
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	$('#sub-menu-text-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-menu').val(), bgColor = $('#menu-background').val(), css = '';
	        
	        if ( $('#menu-style').val() != 'plain' && $('#menu-style').val() != 'plain-pipe' ) {
				css += '#' + elID + ' ul.ib2-navi { background-color: ' + bgColor + '; } ';
				
				if ( $('#menu-style').val() == 'glossy' ) {
					var glossyColor = getAltColor(bgColor, 'lighter');
					css += '#' + elID + ' ul.ib2-navi { background-image: linear-gradient(to bottom, ' + glossyColor + ' 0%, ' + bgColor + ' 100%); ';
			    	css += 'background-image: -moz-linear-gradient(top, ' + glossyColor + ' 0%, ' + bgColor + ' 100%); } ';
				}
			}
			
			var textColor = $('#menu-text-color').val(),
			subTextColor = ui.color.toString(),
			subBgColor = $('#sub-menu-background').val(),
			subBorderTop = getAltColor(subBgColor, 'lighter'),
			subBorderBot = getAltColor(subBgColor, 'darker');
	
			css += '#' + elID + ' ul.ib2-navi li a { color: ' + textColor + '; } ';
			css += '#' + elID + ' ul.ib2-navi ul { background-color: ' + subBgColor + '; } ';
			css += '#' + elID + ' ul.ib2-navi ul li { border-top-color: ' + subBorderTop + '; border-bottom-color: ' + subBorderBot + '; } ';
			css += '#' + elID + ' ul.ib2-navi ul li a { color: ' + subTextColor + '; } ';
		
			$('#' + elID + '-css').html(css);
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	$('#sub-menu-hover-background').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	       	var elID = $('#ib2-current-menu').val(), css = '';
			css += '#' + elID + ' ul.ib2-navi li ul li a:hover, #' + elID + ' ul.ib2-navi li ul li a:focus { color: ' + $('#sub-menu-hover-text-color').val() + '; background-color: ' + ui.color.toString() + ' !important; text-decoration: none !important; } ';
	
			$('#' + elID + '-sub-hover-css').html(css);
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	$('#sub-menu-hover-text-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-menu').val(), css = '';
			css += '#' + elID + ' ul.ib2-navi li ul li a:hover, #' + elID + ' ul.ib2-navi li ul li a:focus { color: ' + ui.color.toString() + '; background-color: ' + $('#sub-menu-hover-background').val() + ' !important; text-decoration: none !important; } ';
	
			$('#' + elID + '-sub-hover-css').html(css);
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Box Color
	$('#box-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
	    		return false;
	    		
	    	var boxID = $('#ib2-current-box').val(), el = $('#' + boxID + '-box'), glossy = false;
	    	
	    	if ( $('#' + boxID).attr('data-glossy') && $('#' + boxID).data('glossy') == 'yes' ) {
	    		glossy = true;
	    	} 
	    	
	    	if ( $('#' + boxID).data('el') != 'box' )
	    		el = $('#' + boxID + ' > .el-content');
	    		
	    	if ( el.css('backgroundColor') != 'transparent' ) {
	    		if ( glossy ) {
					var color = ui.color.toString(),
					lightColor = getAltColor(color, 'lighter'),
					glossyColor = getAltColor(lightColor, 'lighter');
					
					$('#' + boxID).attr('data-glossy', 'yes');
		  			$('#' + boxID).data('glossy', 'yes');
		  			
		  			el.css({
		  				'background-color': color,
		  				'background-image': 'linear-gradient(to bottom, ' + glossyColor + ' 5%, ' + color + ' 100%)',
		  				'background-image': '-moz-linear-gradient(top, ' + glossyColor + ' 5%, ' + color + ' 100%)'
		  			});
		  		} else {
					$('#' + boxID).attr('data-glossy', 'no');
		  			$('#' + boxID).data('glossy', 'no');
		  			
		  			el.css('background-color', ui.color.toString());
		  			
		  			var bgImg = el.css('backgroundImage');
		  			if ( bgImg.indexOf("linear-gradient") >= 0 ) {
		  				el.css('background-image', 'none');
		  			}
		  		}
	    	}
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Box Border Color
	$('#box-border-color').instaColorPicker({
	    hide: true,
	    palettes: ['#c09853', '#b94a48', '#468847', '#3a87ad', '#ccc', '#e5e5e5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var boxID = $('#ib2-current-box').val(), el = $('#' + boxID + '-box');
	    	if ( $('#' + boxID).data('el') != 'box' )
	    		el = $('#' + boxID + ' > .el-content');
	        el.css('border-color', ui.color.toString());
	        
	       	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Box Left Border Color
	$('#box-left-border-color').instaColorPicker({
	    hide: true,
	    palettes: ['#c09853', '#b94a48', '#468847', '#3a87ad', '#ccc', '#e5e5e5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var boxID = $('#ib2-current-box').val(), el = $('#' + boxID + '-box');
	    	if ( $('#' + boxID).data('el') != 'box' )
	    		el = $('#' + boxID + ' > .el-content');
	        el.css('border-left-color', ui.color.toString());
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Box Right Border Color
	$('#box-right-border-color').instaColorPicker({
	    hide: true,
	    palettes: ['#c09853', '#b94a48', '#468847', '#3a87ad', '#ccc', '#e5e5e5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var boxID = $('#ib2-current-box').val(), el = $('#' + boxID + '-box');
	    	if ( $('#' + boxID).data('el') != 'box' )
	    		el = $('#' + boxID + ' > .el-content');
	        el.css('border-right-color', ui.color.toString());
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Box Top Border Color
	$('#box-top-border-color').instaColorPicker({
	    hide: true,
	    palettes: ['#c09853', '#b94a48', '#468847', '#3a87ad', '#ccc', '#e5e5e5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var boxID = $('#ib2-current-box').val(), el = $('#' + boxID + '-box');
	    	if ( $('#' + boxID).data('el') != 'box' )
	    		el = $('#' + boxID + ' > .el-content');
	        el.css('border-top-color', ui.color.toString());
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Box Bottom Border Color
	$('#box-bottom-border-color').instaColorPicker({
	    hide: true,
	    palettes: ['#c09853', '#b94a48', '#468847', '#3a87ad', '#ccc', '#e5e5e5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var boxID = $('#ib2-current-box').val(), el = $('#' + boxID + '-box');
	    	if ( $('#' + boxID).data('el') != 'box' )
	    		el = $('#' + boxID + ' > .el-content');
	        el.css('border-bottom-color', ui.color.toString());
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Box Shadow Color
	$('#box-shadow-color').instaColorPicker({
	    hide: true,
	    palettes: ['#808080', '#a7a7a7', '#c2c2c2', '#e5e5e5', '#f5f5f5', '#ffffff'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-box').val(),
	    	el = $('#' + elID + '-box');
	    	
	    	if ( $('#' + elID).data('el') != 'box' )
	    		el = $('#' + elID + ' > .el-content');
	    		
	    	if ( $('#box-shadow-type').val() != 'none' ) {
	    		var shadow = '';
	        	if ( $('#box-shadow-type').val() == 'inset' ) shadow += 'inset ';
	        	shadow += $('#box-hshadow').val() + 'px ';
	        	shadow += $('#box-vshadow').val() + 'px ';
	        	shadow += ( $('#box-shadow-type').val() == 'inset' ) ? '10px 4px ' : '8px 3px ';
	        	shadow += ui.color.toString();
	        	
	        	el.css({
	        		'box-shadow': shadow,
	        		'-webkit-box-shadow': shadow
	        	});

	    	}
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Horizontal Line Color
	$('#hline-color').instaColorPicker({
	    hide: true,
	    palettes: ['#c09853', '#b94a48', '#468847', '#3a87ad', '#ccc', '#e5e5e5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var boxID = $('#ib2-current-hline').val(), el = $('#' + boxID).find('.ib2-hline');
	        el.css('border-top-color', ui.color.toString());
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Button Color
	$('#button-color').instaColorPicker({
	    hide: true,
	    palettes: ['#c09853', '#b94a48', '#468847', '#3a87ad', '#ccc', '#e5e5e5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var buttonID = $('#ib2-current-button').val(),
	    	el = ( $('#ib2-button-mode').val() == 'normal' ) ? $('#' + buttonID).find('a.ib2-button') : $('#' + buttonID),
	    	buttonColor = ui.color.toString(),
	    	borderColor = getAltColor(buttonColor, 'darker'),
	    	type = $('#button-style').val();
	    	
	    	if ( type == 'flat' ) {
	    		el.css({
		        	'background-color': buttonColor,
		        	'border-color': borderColor,
		        	'background-image': 'none'
		        });
	    	} else if ( type == 'glossy' ) {
	    		var glossyColor = getAltColor(buttonColor, 'lighter');
	    		el.css({
		        	'background-color': buttonColor,
		        	'background-image': 'linear-gradient(center bottom , ' + buttonColor + ' 0%, ' + glossyColor + ')',
		        	'background-image': '-moz-linear-gradient(center bottom , ' + buttonColor + ' 0%, ' + glossyColor + ')',
		        	'border-color': borderColor
		        });	
	    	}
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});

	// Button Text Color
	$('#button-text-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var buttonID = $('#ib2-current-button').val(),
	    	el = ( $('#ib2-button-mode').val() == 'normal' ) ? $('#' + buttonID).find('a.ib2-button') : $('#' + buttonID);
	    	
	        el.css('color', ui.color.toString());
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Button Text Shadow Color
	$('#button-tshadow-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var buttonID = $('#ib2-current-button').val(),
	    	el = ( $('#ib2-button-mode').val() == 'normal' ) ? $('#' + buttonID).find('a.ib2-button') : $('#' + buttonID);
	    	
	        el.css('text-shadow', '1px 1px 0 ' + ui.color.toString());
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Button Hover Color
	$('#button-hover-color').instaColorPicker({
	    hide: true,
	    palettes: ['#c09853', '#b94a48', '#468847', '#3a87ad', '#ccc', '#e5e5e5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var buttonID = $('#ib2-current-button').val(),
	    	selector = ( $('#ib2-button-mode').val() == 'normal' ) ? '#' + buttonID + ' > .el-content > a.ib2-button' : '#' + buttonID,
	    	buttonColor = ui.color.toString(),
	    	borderColor = getAltColor(buttonColor, 'darker'),
	    	type = $('#button-style').val();
	    	
	    	var css = '' + selector + ':hover, ' + selector + ':active {';
	    	if ( type == 'flat' ) {
		        css += 'color: ' + $('#button-text-hover-color').val() + ' !important; text-shadow: 1px 1px 0 ' + $('#button-tshadow-hover-color').val() + ' !important; background-color:' + buttonColor + ' !important; border-color:' + borderColor + ' !important; background-image:none !important;  }';
	    	} else if ( type == 'glossy' ) {
	    		var glossyColor = getAltColor(buttonColor, 'lighter');
	    		css += 'color: ' + $('#button-text-hover-color').val() + ' !important; text-shadow: 1px 1px 0 ' + $('#button-tshadow-hover-color').val() + ' !important; background-color:' + buttonColor + ' !important; border-color:' + borderColor + ' !important; ';
		        css += 'background-image: linear-gradient(center bottom , ' + buttonColor + ' 0%, ' + glossyColor + ') !important; ';
		        css += 'background-image: -moz-linear-gradient(center bottom , ' + buttonColor + ' 0%, ' + glossyColor + ') !important; ';
				css += '}';
	    	}
	    	
	    	$('#' + buttonID + '-css').html(css);
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});

	// Button Text Hover Color
	$('#button-text-hover-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var buttonID = $('#ib2-current-button').val(),
	    	selector = ( $('#ib2-button-mode').val() == 'normal' ) ? '#' + buttonID + ' > .el-content > a.ib2-button' : '#' + buttonID,
	    	buttonColor = $('#button-hover-color').val(),
	    	borderColor = getAltColor(buttonColor, 'darker'),
	    	type = $('#button-style').val();
	    	
	    	var css = '' + selector + ':hover, ' + selector + ':active {';
	    	if ( type == 'flat' ) {
		        css += 'color: ' + ui.color.toString() + ' !important; text-shadow: 1px 1px 0 ' + $('#button-tshadow-hover-color').val() + ' !important; background-color:' + buttonColor + ' !important; border-color:' + borderColor + ' !important; }';
	    	} else if ( type == 'glossy' ) {
	    		var glossyColor = getAltColor(buttonColor, 'lighter');
	    		css += 'color: ' + ui.color.toString() + ' !important; text-shadow: 1px 1px 0 ' + $('#button-tshadow-hover-color').val() + ' !important; background-color:' + buttonColor + ' !important; border-color:' + borderColor + ' !important; ';
		        css += 'background-image: linear-gradient(center bottom , ' + buttonColor + ' 0%, ' + glossyColor + ') !important; ';
		        css += 'background-image: -moz-linear-gradient(center bottom , ' + buttonColor + ' 0%, ' + glossyColor + ') !important; ';
				css += '}';
	    	}

	    	$('#' + buttonID + '-css').html(css);
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Button Text Shadow Hover Color
	$('#button-tshadow-hover-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var buttonID = $('#ib2-current-button').val(),
	    	selector = ( $('#ib2-button-mode').val() == 'normal' ) ? '#' + buttonID + ' > .el-content > a.ib2-button' : '#' + buttonID,
	    	buttonColor = $('#button-hover-color').val(),
	    	borderColor = getAltColor(buttonColor, 'darker'),
	    	type = $('#button-style').val();
	    	
	    	var css = '' + selector + ':hover, ' + selector + ':active {';
	    	if ( type == 'flat' ) {
		        css += 'color: ' + $('#button-text-hover-color').val() + ' !important; text-shadow: 1px 1px 0 ' + ui.color.toString() + ' !important; background-color:' + buttonColor + ' !important; border-color:' + borderColor + ' !important; }';
	    	} else if ( type == 'glossy' ) {
	    		var glossyColor = getAltColor(buttonColor, 'lighter');
	    		css += 'color: ' + $('#button-text-hover-color').val() + ' !important; text-shadow: 1px 1px 0 ' + ui.color.toString() + ' !important; background-color:' + buttonColor + ' !important; border-color:' + borderColor + ' !important; ';
		        css += 'background-image: linear-gradient(center bottom, ' + buttonColor + ' 0%, ' + glossyColor + ') !important; ';
		        css += 'background-image: -moz-linear-gradient(center bottom, ' + buttonColor + ' 0%, ' + glossyColor + ') !important; ';
				css += '}';
	    	}
	    	
	    	$('#' + buttonID + '-css').html(css);

			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// FB Pre-Text Color
	$('#facebook-optin-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-optin').val(),
	    	el = $('#' + elID + '-fb').find('.ib2-facebook-optin-txt'),
	    	color = ui.color.toString();
	    	
	    	el.css('color', color);
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Countdown Font Color
	$('#countdown-font-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-countdown').val(),
	    	el = $('#' + elID), color = ui.color.toString();
	    	
	    	el.css('color', color);
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Countdown Text Shadow Color
	$('#countdown-text-shadow-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-countdown').val(),
	    	el = $('#' + elID), color = ui.color.toString();
	    	
	    	el.css('text-shadow', '1px 1px 0 ' + color);
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Countdown Color
	$('#countdown-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-countdown').val(),
	    	el = $('#' + elID), color = ui.color.toString();
	    	
	    	var css = '';
	    	if ( $('#countdown-style').val() == 'flat-box' ) {
		        css += '#' + elID + '.ib2-countdown-style > .ib2-digit { ';
		        css += 'background-color: ' + color + '; ';
		        css += 'background-image: none; ';
		        css += 'border-width: ' + $('#countdown-border-thick').val() + 'px; ';
		        css += 'border-style: ' + $('#countdown-border-style').val() + '; ';
		        css += 'border-color: ' + $('#countdown-border-color').val() + '; }';
			} else if ( $('#countdown-style').val() == 'glossy-box' ) {
				var glossyColor = getAltColor(color, 'lighter');
				css += '#' + elID + '.ib2-countdown-style > .ib2-digit { ';
		        css += 'background-color: ' + color + '; ';
		        css += 'background-image: linear-gradient(to bottom, ' + glossyColor + ' 5%, ' + color + ' 100%); ';
		        css += 'background-image: -moz-linear-gradient(top, ' + glossyColor + ' 5%, ' + color + ' 100%); ';
		        css += 'border-width: ' + $('#countdown-border-thick').val() + 'px; ';
		        css += 'border-style: ' + $('#countdown-border-style').val() + '; ';
		        css += 'border-color: ' + $('#countdown-border-color').val() + '; }';
			}
			
			$('#' + elID + '-countdown-css').html(css);
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	$('#countdown-border-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-countdown').val(),
	    	el = $('#' + elID), color = $('#countdown-color').val();
	    	
	    	var css = '';
	    	if ( $('#countdown-style').val() == 'flat-box' ) {
		        css += '#' + elID + '.ib2-countdown-style > .ib2-digit { ';
		        css += 'background-color: ' + color + '; ';
		        css += 'background-image: none; ';
		        css += 'border-width: ' + $('#countdown-border-thick').val() + 'px; ';
		        css += 'border-style: ' + $('#countdown-border-style').val() + '; ';
		        css += 'border-color: ' + ui.color.toString() + '; }';
			} else if ( $('#countdown-style').val() == 'glossy-box' ) {
				var glossyColor = getAltColor(color, 'lighter');
				css += '#' + elID + '.ib2-countdown-style > .ib2-digit { ';
		        css += 'background-color: ' + color + '; ';
		        css += 'background-image: linear-gradient(to bottom, ' + glossyColor + ' 5%, ' + color + ' 100%); ';
		        css += 'background-image: -moz-linear-gradient(top, ' + glossyColor + ' 5%, ' + color + ' 100%); ';
		        css += 'border-width: ' + $('#countdown-border-thick').val() + 'px; ';
		        css += 'border-style: ' + $('#countdown-border-style').val() + '; ';
		        css += 'border-color: ' + ui.color.toString() + '; }';
			}
			
			$('#' + elID + '-countdown-css').html(css);
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Date Font Color
	$('#date-font-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-date').val(),
	    	el = $('#' + elID), color = ui.color.toString();
	    	
	    	el.css('color', color);
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Attention Bar
	$('#attention-bar-background').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var el = $('.ib2-notification-text'), color = ui.color.toString();
	    	el.css('background-color', color);
	    	$('.ib2-notification-bar > label[for=nb-show]').css('background-color', color);
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2GlobalChange", ["picker", "attention-bar-background", ui.color.toString(), 'attentionBarBackground']);
	    }
	});
	
	$('#attention-bar-border').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var el = $('.ib2-notification-text'), color = ui.color.toString();
	    	el.css('border-color', color);
	    	$('.ib2-notification-bar > label[for=nb-show]').css('border-color', color);
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2GlobalChange", ["picker", "attention-bar-border", ui.color.toString(), 'attentionBarBorder']);
	    }
	});
	
	$('#attention-bar-fontcolor').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#FFFFFF'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var el = $('.ib2-notification-text'), color = ui.color.toString();
	    	el.css('color', color);
	    	$('.ib2-notification-text a.att-url').css('color', color);
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2GlobalChange", ["picker", "attention-bar-fontcolor", ui.color.toString(), 'attentionBarFontColor']);
	    }
	});
	
	$('#text-el-shadow-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#FFFFFF'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	if ( $('#text-el-shadow').is(":checked") ) {
	    		var elID = $('#ib2-current-text').val(),
	    		blur = $('#text-el-shadow-blur').val();
	    		
	    		$('#' + elID).css({
	    			'text-shadow': '1px 1px ' + blur + 'px ' + ui.color.toString()
	    		});
	    		
	    		if ( !doingStack )
		        	$("body").trigger("ib2CommonChange");
	    	}
	    }
	});
	
	// Image CaptionColor
	$('#image-caption-color').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-image').val();
	    	
	    	$('#' + elID).find('.el-caption').css('color', ui.color.toString());
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Image CaptionColor
	$('#image-caption-background').instaColorPicker({
	    hide: true,
	    palettes: ['#fcf8e3', '#f2dede', '#dff0d8', '#d9edf7', '#e5e5e5', '#f5f5f5'],
	    change: function( event, ui ) {
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-image').val();
	    	
	    	$('#' + elID).find('.el-caption').css('background-color', ui.color.toString());
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// =========================== SLIDERS ===========================

	// Global Font Size
	$('#body-text-size-slider').slider({
		min: 9,
		max: 72,
		value: $('#default-font-size').val(),
		slide: function( event, ui ) {
			$('#body-text-size').val(ui.value);
		},
		change: function( event, ui ) {
			$('#body-text-size').val(ui.value);
			if ( !doingStack ) {
				var style = '#screen-container { font-family: ' + $('#body-text-font').val() + '; font-size: ' + ui.value + 'px; color:' + $('#body-text-color').val() + '; }';
				style += '#screen-container a { color: ' + $('#body-link-color').val() + '; }';
				style += '#screen-container a:hover, #screen-container a:focus { color: ' + $('#body-link-hover-color').val() + '; }';
	
		        $("#editor-body-typo").html(style);
	       	
	        	$("body").trigger("ib2GlobalChange", ["slider", "body-text-size-slider", ui.value, 'fontSize']);
	       }
		}
	});
	
	// Background Image Size
	$('#background-size-slider').slider({
		min: 1,
		max: 200,
		value: 100,
		slide: function( event, ui ) {
			$('#background-size').val(ui.value);
		},
		change: function( event, ui ) {
			$('#background-size').val(ui.value);
			$('#screen-container').css('background-size', ui.value + '% auto');
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Line Height
	$('#body-line-height-slider').slider({
		min: 1,
		max: 5,
		step: 0.1,
		value: 1.4,
		slide: function( event, ui ) {
			$('#body-line-height').val(ui.value);
		},
		change: function( event, ui ) {
			$('#body-line-height').val(ui.value);
			if ( !$('head').find('#advtyposet').length ) {
				$('head').append('<style id="advtyposet"></style>');
			}
			
			var css = '.ib2-section-content p { line-height: ' + ui.value + ' !important; margin-bottom: ' + $('#body-white-space').val() + 'px !important; }';
			$('#advtyposet').html(css);
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// White Space
	$('#body-white-space-slider').slider({
		min: 0,
		max: 50,
		value: 18,
		slide: function( event, ui ) {
			$('#body-white-space').val(ui.value);
		},
		change: function( event, ui ) {
			$('#body-white-space').val(ui.value);
			if ( !$('head').find('#advtyposet').length ) {
				$('head').append('<style id="advtyposet"></style>');
			}
			
			var css = '.ib2-section-content p { line-height: ' + $('#body-line-height').val() + ' !important; margin-bottom: ' + ui.value + 'px !important; }';
			$('#advtyposet').html(css);
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Quiz Questions
	$('#quiz-questions-slider').slider({
		min: 1,
		max: 5,
		value: 1,
		slide: function( event, ui ) {
			$('#quiz-questions').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#quiz-questions').val(ui.value);
			
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-quiz').val(), q = ui.value,
			num = $('#ib2-current-question').val();
			
			$('#' + elID).find('.ib2-quiz-page').each(function(i){
				var c = $(this), j = i+1;
				if ( !c.hasClass('ib2-section-content') ) {
					if ( j > q ) 
						c.addClass('ib2-unused-question');
					else
						c.removeClass('ib2-unused-question');
				}
			});

			if ( num < 6 && ui.value < num ) {
				$('#' + elID).find('.ib2-quiz-page').hide();
				$('#' + elID + '-' + ui.value).show();
			}
			
			$('#' + elID).data('questions', q);
			$('#' + elID).attr('data-questions', q);
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Quiz Question font size
	$('#quiz-question-size-slider').slider({
		min: 9,
		max: 32,
		value: 26,
		slide: function( event, ui ) {
			$('#quiz-question-size').val(ui.value);
		},
	    change: function( event, ui ) {
	    	
	    	$('#quiz-question-size').val(ui.value);
	    	
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-quiz').val();
	        $('#' + elID).find('.ib2-quiz-page').each(function(i){
	        	var c = $(this);
	        	c.find('h3').css('font-size', ui.value + 'px');
	        });
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Quiz Answers font size
	$('#quiz-answer-size-slider').slider({
		min: 9,
		max: 32,
		value: $('#body-text-size').val(),
		slide: function( event, ui ) {
			$('#quiz-answer-size').val(ui.value);
		},
	    change: function( event, ui ) {
	    	
	    	$('#quiz-answer-size').val(ui.value);
	    	
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-quiz').val();
	        $('#' + elID).find('.ib2-quiz-page').each(function(i){
	        	var c = $(this);
	        	c.find('.ib2-answer-list').css('font-size', ui.value + 'px');
	        });
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Menu corners
	$('#menu-corners-slider').slider({
		min: 0,
		max: 20,
		value: 0,
		slide: function( event, ui ) {
			$('#menu-corners').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#menu-corners').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-menu').val();
	        $('#' + elID + ' > nav > ul.ib2-navi').css({
	        	'border-radius': ui.value + 'px',
	        	'-webkit-border-radius': ui.value + 'px'
	        });
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Hotspot Width
	$('#hotspot-width-slider').slider({
		min: 5,
		max: 200,
		value: 30,
		slide: function( event, ui ) {
			$('#hotspot-width').val(ui.value);
		},
		change: function( event, ui ) {
			$('#hotspot-width').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-hotspot').val();
			$('#' + elID).css({
				'width' : ui.value + 'px',
			});
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Hotspot Height
	$('#hotspot-height-slider').slider({
		min: 5,
		max: 200,
		value: 30,
		slide: function( event, ui ) {
			$('#hotspot-height').val(ui.value);
		},
		change: function( event, ui ) {
			$('#hotspot-height').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-hotspot').val();
			$('#' + elID).css({
				'height' : ui.value + 'px',
			});
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Hotspot Opacity
	$('#hotspot-opac-slider').slider({
		min: 0,
		max: 1,
		step: 0.1,
		value: 0.8,
		slide: function( event, ui ) {
			$('#hotspot-opac').val(ui.value);
		},
		change: function( event, ui ) {
			$('#hotspot-opac').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-hotspot').val();
			$('#' + elID).css({
				'opacity' : ui.value,
			});
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Hotspot Border Thick Slider
	$('#hotspot-border-thick-slider').slider({
		min: 0,
		max: 10,
		value: 2,
		slide: function( event, ui ) {
			$('#hotspot-border-thick').val(ui.value);
		},
		change: function( event, ui ) {
			$('#hotspot-border-thick').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-hotspot').val();
			$('#' + elID).css({
				'border-width' : ui.value + 'px',
			});
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Box/Section Top Margin
	$('#box-tm-slider').slider({
		min: 0,
		max: 200,
		value: 0,
		slide: function( event, ui ) {
			$('#box-tm').val(ui.value);
		},
		change: function( event, ui ) {
			$('#box-tm').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-box').val();
			$('#' + elID).css({
				'margin-top' : ui.value + 'px',
			});
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Box/Section Top Margin
	$('#box-bm-slider').slider({
		min: 0,
		max: 200,
		value: 0,
		slide: function( event, ui ) {
			$('#box-bm').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#box-bm').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-box').val();
			$('#' + elID).css({
				'margin-bottom' : ui.value + 'px',
			});
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Box/Section horizontal padding
	$('#box-hp-slider').slider({
		min: 0,
		max: 200,
		value: 25,
		slide: function( event, ui ) {
			$('#box-hp').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#box-hp').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-box').val(),
			el = ( $('#' + elID).data('el') != 'box' ) ? $('#' + elID + ' > .el-content') : $('#' + elID + '-box');
			
			el.css({
				'padding-left' : ui.value + 'px',
			});
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Box/Section horizontal padding
	$('#box-hp2-slider').slider({
		min: 0,
		max: 200,
		value: 25,
		slide: function( event, ui ) {
			$('#box-hp2').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#box-hp2').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-box').val(),
			el = ( $('#' + elID).data('el') != 'box' ) ? $('#' + elID + ' > .el-content') : $('#' + elID + '-box');
			
			el.css({
				'padding-right' : ui.value + 'px',
			});
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Box/Section vertical padding
	$('#box-vp-slider').slider({
		min: 0,
		max: 200,
		value: 15,
		slide: function( event, ui ) {
			$('#box-vp').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#box-vp').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-box').val(),
			el = ( $('#' + elID).data('el') != 'box' ) ? $('#' + elID + ' > .el-content') : $('#' + elID + '-box');
			
			el.css({
				'padding-top' : ui.value + 'px',
			});
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Box/Section vertical padding
	$('#box-vp2-slider').slider({
		min: 0,
		max: 200,
		value: 15,
		slide: function( event, ui ) {
			$('#box-vp2').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#box-vp2').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-box').val(),
			el = ( $('#' + elID).data('el') != 'box' ) ? $('#' + elID + ' > .el-content') : $('#' + elID + '-box');
			
			el.css({
				'padding-bottom' : ui.value + 'px',
			});
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});

	// Box Border Thickness
	$('#box-border-thick-slider').slider({
		min: 0,
		max: 10,
		value: 1,
		change: function( event, ui ) {
			
			$('#box-border-thick').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var boxID = $('#ib2-current-box').val(), el = $('#' + boxID + '-box');
			if ( $('#' + boxID).data('el') != 'box' )
	    		el = $('#' + boxID + ' > .el-content');
	        el.css('border-width', ui.value + 'px');
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Box Left Border Thickness
	$('#box-left-border-thick-slider').slider({
		min: 0,
		max: 10,
		value: 1,
		change: function( event, ui ) {
			
			$('#box-left-border-thick').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var boxID = $('#ib2-current-box').val(), el = $('#' + boxID + '-box');
			if ( $('#' + boxID).data('el') != 'box' )
	    		el = $('#' + boxID + ' > .el-content');
	        el.css('border-left-width', ui.value + 'px');
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Box Right Border Thickness
	$('#box-right-border-thick-slider').slider({
		min: 0,
		max: 10,
		value: 1,
		change: function( event, ui ) {
			
			$('#box-right-border-thick').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var boxID = $('#ib2-current-box').val(), el = $('#' + boxID + '-box');
			if ( $('#' + boxID).data('el') != 'box' )
	    		el = $('#' + boxID + ' > .el-content');
	        el.css('border-right-width', ui.value + 'px');
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Box Top Border Thickness
	$('#box-top-border-thick-slider').slider({
		min: 0,
		max: 10,
		value: 1,
		change: function( event, ui ) {
			
			$('#box-top-border-thick').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var boxID = $('#ib2-current-box').val(), el = $('#' + boxID + '-box');
			if ( $('#' + boxID).data('el') != 'box' )
	    		el = $('#' + boxID + ' > .el-content');
	        el.css('border-top-width', ui.value + 'px');
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Box Bottom Border Thickness
	$('#box-bottom-border-thick-slider').slider({
		min: 0,
		max: 10,
		value: 1,
		change: function( event, ui ) {
			
			$('#box-bottom-border-thick').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var boxID = $('#ib2-current-box').val(), el = $('#' + boxID + '-box');
			if ( $('#' + boxID).data('el') != 'box' )
	    		el = $('#' + boxID + ' > .el-content');
	        el.css('border-bottom-width', ui.value + 'px');
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Box Corners
	$('#box-corners-slider').slider({
		min: 0,
		max: 20,
		value: 0,
		slide: function( event, ui ) {
			$('#box-corners').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#box-corners').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var boxID = $('#ib2-current-box').val(), el = $('#' + boxID + '-box');
			if ( $('#' + boxID).data('el') != 'box' )
	    		el = $('#' + boxID + ' > .el-content');
	        el.css({
	        	'border-top-left-radius': ui.value + 'px',
	        	'border-top-right-radius': ui.value + 'px',
	        	'-webkit-border-top-left-radius': ui.value + 'px',
	        	'-webkit-border-top-right-radius': ui.value + 'px'
	        });
	        
	        if ( $('#' + boxID).data('el') == 'box' ) {
        		$('#' + boxID + ' > .el-content' ).css({
		        	'border-top-left-radius': ui.value + 'px',
		        	'border-top-right-radius': ui.value + 'px',
		        	'-webkit-border-top-left-radius': ui.value + 'px',
		        	'-webkit-border-top-right-radius': ui.value + 'px'
		        });
	        }
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Box Corners
	$('#box-corners-bot-slider').slider({
		min: 0,
		max: 20,
		value: 0,
		slide: function( event, ui ) {
			$('#box-corners-bot').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#box-corners-bot').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var boxID = $('#ib2-current-box').val(), el = $('#' + boxID + '-box');
			if ( $('#' + boxID).data('el') != 'box' )
	    		el = $('#' + boxID + ' > .el-content');
	        el.css({
	        	'border-bottom-left-radius': ui.value + 'px',
	        	'border-bottom-right-radius': ui.value + 'px',
	        	'-webkit-border-bottom-left-radius': ui.value + 'px',
	        	'-webkit-border-bottom-right-radius': ui.value + 'px'
	        });
	        
	        if ( $('#' + boxID).data('el') == 'box' ) {
        		$('#' + boxID + ' > .el-content' ).css({
		        	'border-bottom-left-radius': ui.value + 'px',
		        	'border-bottom-right-radius': ui.value + 'px',
		        	'-webkit-border-bottom-left-radius': ui.value + 'px',
		        	'-webkit-border-bottom-right-radius': ui.value + 'px'
		        });
	        }
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});

	// Box Horizontal Shadow
	$('#box-hshadow-slider').slider({
		min: -20,
		max: 20,
		value: 0,
		slide: function( event, ui ) {
			$('#box-hshadow').val(ui.value);
		},
		change: function( event, ui ) {
			$('#box-hshadow').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-box').val(),
	    	el = $('#' + elID + '-box');
	    	
	    	if ( $('#' + elID).data('el') != 'box' )
	    		el = $('#' + elID + ' > .el-content');
	    		
	        if ( $('#box-shadow-type').val() != 'none' ) {
	    		var shadow = '';
	        	if ( $('#box-shadow-type').val() == 'inset' ) shadow += 'inset ';
	        	shadow += ui.value + 'px ';
	        	shadow += $('#box-vshadow').val() + 'px ';
	        	shadow += ( $('#box-shadow-type').val() == 'inset' ) ? '10px 4px ' : '8px 3px ';
	        	shadow += $('#box-shadow-color').iris("color");
	        	
	        	el.css({
	        		'box-shadow': shadow,
	        		'-webkit-box-shadow': shadow
	        	});
	    	}
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Box Vertical Shadow
	$('#box-vshadow-slider').slider({
		min: -20,
		max: 20,
		value: 0,
		slide: function( event, ui ) {
			$('#box-vshadow').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#box-vshadow').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-box').val(),
	    	el = $('#' + elID + '-box');
	    	
	    	if ( $('#' + elID).data('el') != 'box' )
	    		el = $('#' + elID + ' > .el-content');
	    		
	        if ( $('#box-shadow-type').val() != 'none' ) {
	    		var shadow = '';
	        	if ( $('#box-shadow-type').val() == 'inset' ) shadow += 'inset ';
	        	shadow += $('#box-hshadow').val() + 'px ';
	        	shadow += ui.value + 'px ';
	        	shadow += ( $('#box-shadow-type').val() == 'inset' ) ? '10px 4px ' : '8px 3px ';
	        	shadow += $('#box-shadow-color').iris("color");
	        	
	        	el.css({
	        		'box-shadow': shadow,
	        		'-webkit-box-shadow': shadow
	        	});
	    	}
	    	
	    	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Box Opacity
	$('#box-opacity-slider').slider({
		min: 0,
		max: 100,
		value: 100,
		slide: function( event, ui ) {
			$('#box-opacity').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#box-opacity').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-box').val(),
	    	el = $('#' + elID + '-box');

			if ( $('#' + elID).data('el') != 'box' )
	    		el = $('#' + elID + ' > .el-content');
	    		
    		var opacity = parseFloat(ui.value / 100);
        	el.css({
        		'filter': 'alpha(opacity=' + ui.value + ')',
        		'opacity': opacity
        	});
        	
        	if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	$('#box-delay-hour-slider').slider({
		min: 0,
		max: 23,
		value: 0,
		slide: function( event, ui ) {
			$('#box-delay-hour').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#box-delay-hour').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			if ( $('#enable_box_delay').is(":checked") ) {
				var elID = $('#ib2-current-box').val(),
		    	el = $('#' + elID);
	
				var hour = ui.value,
	  			min = $('#box-delay-min').val(),
	  			secs = $('#box-delay-secs').val();
	  			
	  			$('#' + elID).data('delay', hour + ':' + min + ':' + secs);
	  			$('#' + elID).attr('data-delay', hour + ':' + min + ':' + secs);
	  		}
	  		
	  		if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	$('#box-delay-min-slider').slider({
		min: 0,
		max: 59,
		value: 0,
		slide: function( event, ui ) {
			$('#box-delay-min').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#box-delay-min').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			if ( $('#enable_box_delay').is(":checked") ) {
				var elID = $('#ib2-current-box').val(),
		    	el = $('#' + elID);
	
				var hour = $('#box-delay-hour').val(),
	  			min = ui.value,
	  			secs = $('#box-delay-secs').val();
	  			
	  			$('#' + elID).data('delay', hour + ':' + min + ':' + secs);
	  			$('#' + elID).attr('data-delay', hour + ':' + min + ':' + secs);
	  		}
	  		
	  		if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	$('#box-delay-secs-slider').slider({
		min: 0,
		max: 59,
		value: 0,
		slide: function( event, ui ) {
			$('#box-delay-secs').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#box-delay-secs').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			if ( $('#enable_box_delay').is(":checked") ) {
				var elID = $('#ib2-current-box').val(),
		    	el = $('#' + elID);
	
				var hour = $('#box-delay-hour').val(),
	  			min = $('#box-delay-min').val(),
	  			secs = ui.value;
	  			
	  			$('#' + elID).data('delay', hour + ':' + min + ':' + secs);
	  			$('#' + elID).attr('data-delay', hour + ':' + min + ':' + secs);
	  		}
	  		
	  		if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Image Top Margin
	$('#img-tm-slider').slider({
		min: 0,
		max: 200,
		value: 0,
		slide: function( event, ui ) {
			$('#img-tm').val(ui.value);
		},
		change: function( event, ui ) {
			$('#img-tm').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-image').val();
			$('#' + elID).css({
				'margin-top' : ui.value + 'px',
			});
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Image Bottom Margin
	$('#img-bm-slider').slider({
		min: 0,
		max: 200,
		value: 0,
		slide: function( event, ui ) {
			$('#img-bm').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#img-bm').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-image').val();
			$('#' + elID).css({
				'margin-bottom' : ui.value + 'px',
			});
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Video Top Margin
	$('#video-tm-slider').slider({
		min: 0,
		max: 200,
		value: 0,
		slide: function( event, ui ) {
			$('#video-tm').val(ui.value);
		},
		change: function( event, ui ) {
			$('#video-tm').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-video').val();
			$('#' + elID).css({
				'margin-top' : ui.value + 'px',
			});
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Video Bottom Margin
	$('#video-bm-slider').slider({
		min: 0,
		max: 200,
		value: 0,
		slide: function( event, ui ) {
			$('#video-bm').val(ui.value);
		},
		change: function( event, ui ) {
			$('#video-bm').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-video').val();
			$('#' + elID).css({
				'margin-bottom' : ui.value + 'px',
			});
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Horizontal Line Thickness
	$('#hline-thick-slider').slider({
		min: 0,
		max: 10,
		value: 2,
		slide: function( event, ui ) {
			$('#hline-thick').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#hline-thick').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-hline').val(), el = $('#' + elID).find('.ib2-hline');
	        el.css('border-top-width', ui.value + 'px');
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Horizontal Line Spacing
	$('#hline-space-slider').slider({
		min: 0,
		max: 100,
		value: 0,
		slide: function( event, ui ) {
			$('#hline-space').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#hline-space').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-hline').val(),
			el = $('#' + elID).find('.el-content');
			
	        el.css({
	        	'padding-top': ui.value + 'px',
	        	'padding-bottom': ui.value + 'px',
	        });
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Spacer Size
	$('#spacer-space-slider').slider({
		min: 0,
		max: 100,
		value: 0,
		slide: function( event, ui ) {
			$('#spacer-space').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#spacer-space').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-spacer').val(),
			el = $('#' + elID).find('.el-content');
			
	        el.css({
	        	'padding-top': ui.value + 'px',
	        	'padding-bottom': ui.value + 'px',
	        });
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Countdown border thickness
	$('#countdown-border-thick-slider').slider({
		min: 0,
		max: 10,
		value: 1,
		slide: function( event, ui ) {
	    	$('#countdown-border-thick').val(ui.value);
	  	},
	    change: function( event, ui ) {
	    	
	    	$('#countdown-border-thick').val(ui.value);
	    	if ( settingsOpening == 1 )
				return false;
				
	    	var elID = $('#ib2-current-countdown').val(),
	    	el = $('#' + elID), color = $('#countdown-color').val();
	    	
	    	var css = '';
	    	if ( $('#countdown-style').val() == 'flat-box' ) {
		        css += '#' + elID + '.ib2-countdown-style > .ib2-digit { ';
		        css += 'background-color: ' + color + '; ';
		        css += 'background-image: none; ';
		        css += 'border-width: ' + ui.value + 'px; ';
		        css += 'border-style: ' + $('#countdown-border-style').val() + '; ';
		        css += 'border-color: ' + $('#countdown-border-color').val() + '; }';
			} else if ( $('#countdown-style').val() == 'glossy-box' ) {
				var glossyColor = getAltColor(color, 'lighter');
				css += '#' + elID + '.ib2-countdown-style > .ib2-digit { ';
		        css += 'background-color: ' + color + '; ';
		        css += 'background-image: linear-gradient(to bottom, ' + glossyColor + ' 5%, ' + color + ' 100%); ';
		        css += 'background-image: -moz-linear-gradient(top, ' + glossyColor + ' 5%, ' + color + ' 100%); ';
		        css += 'border-width: ' + ui.value + 'px; ';
		        css += 'border-style: ' + $('#countdown-border-style').val() + '; ';
		        css += 'border-color: ' + $('#countdown-border-color').val() + '; }';
			}
			
			$('#' + elID + '-countdown-css').html(css);
			
			if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
	    }
	});
	
	// Countdown font size
	$('#countdown-font-size-slider').slider({
		min: 9,
		max: 48,
		value: 24,
		slide: function( event, ui ) {
			$('#countdown-font-size').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#countdown-font-size').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-countdown').val(),
			el = $('#' + elID);
	        el.css('font-size', ui.value + 'px');
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// FB Pre Text Size
	$('#facebook-optin-size-slider').slider({
		min: 9,
		max: 48,
		value: 14,
		slide: function( event, ui ) {
			$('#facebook-optin-size').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#facebook-optin-size').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-optin').val(),
			el = $('#' + elID + '-fb').find('.ib2-facebook-optin-txt');
	        el.css('font-size', ui.value + 'px');
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Button Text Size Slider
	$('#button-text-size-slider').slider({
		min: 10,
		max: 150,
		value: 14,
		slide: function( event, ui ) {
			$('#button-text-size').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#button-text-size').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var buttonID = $('#ib2-current-button').val(),
			el = ( $('#ib2-button-mode').val() == 'normal' ) ? $('#' + buttonID).find('a.ib2-button') : $('#' + buttonID);
			
	        el.css({
	        	'font-size': ui.value + 'px'
	        });
	        
	        if ( $('#ib2-button-mode').val() == 'normal' ) {
	        	$('#' + buttonID + ' > .el-content').css({
	        		'width': 'auto',
	        		'height': 'auto',
	        		'min-width': el.width() + 'px',
	        		'min-height': el.height() + 'px'
	        	});
	        }
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Button Text Spacing Slider
	$('#button-text-spacing-slider').slider({
		min: 0,
		max: 30,
		value: 0,
		slide: function( event, ui ) {
			$('#button-text-spacing').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#button-text-spacing').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var buttonID = $('#ib2-current-button').val(),
			el = ( $('#ib2-button-mode').val() == 'normal' ) ? $('#' + buttonID).find('a.ib2-button') : $('#' + buttonID);
			
	        el.css({
	        	'letter-spacing': ui.value + 'px'
	        });
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Button Corners Slider
	$('#button-corners-slider').slider({
		min: 0,
		max: 30,
		value: 5,
		slide: function( event, ui ) {
			$('#button-corners').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#button-corners').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var buttonID = $('#ib2-current-button').val(),
			el = ( $('#ib2-button-mode').val() == 'normal' ) ? $('#' + buttonID).find('a.ib2-button') : $('#' + buttonID);
			
	        el.css({
	        	'border-radius': ui.value + 'px',
	        	'-webkit-border-radius': ui.value + 'px'
	        });
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Countdown hour Slider
	$('#countdown-hour-slider').slider({
		min: 0,
		max: 23,
		value: 12,
		slide: function( event, ui ) {
			$('#countdown-hour').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#countdown-hour').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-countdown').val(),
			el = $('#' + elID), day = $('#countdown-day').val(),
			hour = ui.value,
			min = $('#countdown-min').val();
				
			if ( $('#countdown-type').val() == 'date' ) {
				var offset = $('#countdown-tz').val(),
				hour = ("0" + hour).slice(-2),
				min = ("0" + min).slice(-2),
				newdate = $('#countdown-date').val() + ' ' + hour + ':' + min,
				origTime = moment.tz(newdate, offset),
				utc = origTime.clone().tz("UTC"),
				localTime = moment.utc(utc).toDate(),
				target = moment(localTime).format("YYYY/MM/DD HH:mm:ss");
				
				$('#' + elID).data('target', newdate);
				$('#' + elID).attr('data-target', newdate);
				
				$('#countdown-tz').trigger('change');
			} else {
				day = day * 24 * 60 * 60 * 1000;
				hour = hour * 60 * 60 * 1000;
				min = min * 60 * 1000;
				
				var newValue = day + hour + min, target = new Date().valueOf() + newValue;
				
				$('#' + elID).data('target', day + ':' + hour + ':' + min);
				$('#' + elID).attr('data-target', day + ':' + hour + ':' + min);
			}
		}
	});
	
	// Countdown minute Slider
	$('#countdown-min-slider').slider({
		min: 0,
		max: 59,
		value: 0,
		slide: function( event, ui ) {
			$('#countdown-min').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#countdown-min').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-countdown').val(),
			el = $('#' + elID), day = $('#countdown-day').val(),
			hour = $('#countdown-hour').val(),
			min = ui.value;
				
			if ( $('#countdown-type').val() == 'date' ) {
				var offset = $('#countdown-tz').val(),
				hour = ("0" + hour).slice(-2),
				min = ("0" + min).slice(-2),
				newdate = $('#countdown-date').val() + ' ' + hour + ':' + min,
				origTime = moment.tz(newdate, offset),
				utc = origTime.clone().tz("UTC"),
				localTime = moment.utc(utc).toDate(),
				target = moment(localTime).format("YYYY/MM/DD HH:mm:ss");
				
				$('#' + elID).data('target', newdate);
				$('#' + elID).attr('data-target', newdate);
				
				$('#countdown-tz').trigger('change');
			} else {
				day = day * 24 * 60 * 60 * 1000;
				hour = hour * 60 * 60 * 1000;
				min = min * 60 * 1000;
				
				var newValue = day + hour + min, target = new Date().valueOf() + newValue;

				$('#' + elID).data('target', day + ':' + hour + ':' + min);
				$('#' + elID).attr('data-target', day + ':' + hour + ':' + min);
			}

			$('#countdown-type').trigger('change');
		}
	});
	
	// Countdown day Slider
	$('#countdown-day-slider').slider({
		min: 0,
		max: 365,
		value: 1,
		slide: function( event, ui ) {
			$('#countdown-day').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#countdown-day').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-countdown').val(),
			el = $('#' + elID), day = ui.value,
			hour = $('#countdown-hour').val(),
			min = $('#countdown-min').val();
				
			if ( $('#countdown-type').val() != 'date' ) {
				day = day * 24 * 60 * 60 * 1000;
				hour = hour * 60 * 60 * 1000;
				min = min * 60 * 1000;
				
				var newValue = day + hour + min, target = new Date().valueOf() + newValue;

				$('#' + elID).data('target', day + ':' + hour + ':' + min);
				$('#' + elID).attr('data-target', day + ':' + hour + ':' + min);
			}

			$('#countdown-type').trigger('change');
		}
	});
	
	// Date Font Size
	$('#date-font-size-slider').slider({
		min: 8,
		max: 72,
		value: 14,
		slide: function( event, ui ) {
			$('#date-font-size').val(ui.value);
		},
		change: function( event, ui ) {
			
			$('#date-font-size').val(ui.value);
			if ( settingsOpening == 1 )
				return false;
				
			var elID = $('#ib2-current-date').val(), el = $('#' + elID);
	        el.css('font-size', ui.value + 'px');
	        
	        if ( !doingStack )
		        $("body").trigger("ib2CommonChange");
		}
	});
	
	// Text Shadow Blur
	$('#text-el-shadow-blur-slider').slider({
		min: 0,
		max: 15,
		value: 0,
		slide: function( event, ui ) {
			$('#text-el-shadow-blur').val(ui.value);
		},
		change: function( event, ui ) {
			
			if ( $('#text-el-shadow').is(":checked") ) {
				$('#text-el-shadow-blur').val(ui.value);
				if ( settingsOpening == 1 )
					return false;
				
				var elID = $('#ib2-current-text').val(), color = $('#text-el-shadow-color').val();
		        
		        $('#' + elID).css({
	    			'text-shadow': '1px 1px ' + blur + 'px ' + color
	    		});
	    		
	    		if ( !doingStack )
		       		$("body").trigger("ib2CommonChange");
			}
		}
	});
	
	$('.ib2-slider-val').each(function(){
		$(this).bind("blur", function(){
			var $this = $(this), thisID = $this.attr('id'),
			sliderID = thisID + '-slider',
			newValue = $('#' + thisID).val(),
			value = $('#' + sliderID).slider("option", "value"),
			min = $('#' + sliderID).slider("option", "min"),
			max = $('#' + sliderID).slider("option", "max");
			
			if ( !isNumber(newValue) ) {
				alert("Please enter only number.");
				$('#' + thisID).val(value).focus();
			} else {
				if ( newValue.length < 3 && newValue > max ) {
					$('#' + thisID).val(max);
					$('#' + sliderID).slider("value", max);
				} else if ( newValue.length == min.length && newValue < min ) {
					$('#' + thisID).val(min);
					$('#' + sliderID).slider("value", min);
				} else {
					$('#' + sliderID).slider("value", newValue);
				}
			}
		});
	});
	
	// ======================== DATE PICKER ========================
	$('#countdown-date').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "yy-mm-dd"
	});

	// ======================== SIDE PANEL ========================
	$('.open-panel').each(function(){
		$(this).click(function(e){
			var mode = $(this).data('settings'),
			prevmode = $('#ib2-current-panel').val();
			$('#main-editor').removeClass('col-md-12');
			$('#main-editor').addClass('col-md-9');
			$('.editor-panel-content').hide();
			$('.settings-' + mode).show();
			$('#editor-panel').show();
			
			// reset all tabs display
			if ( !$('.settings-tab > ul > li > a.tab-settings-active').length )
				$('.settings-tab > ul > li').show();
			
			// get the width of the editor panel
			var panelWidth = $('#editor-panel').outerWidth(),
			panelTop = $('#editor-panel').offset().top,
			panelLeft = $('#editor-panel').offset().left;
			
			$('#editor-panel-inside').css({
				'width': panelWidth + 'px',
				'top': panelTop + 'px',
				'left': panelLeft + 'px'
			});
			
			$('#editor-panel-inside').fadeIn("slow");
			$('#editor-panel-inside-content').perfectScrollbar('update');
			
			if ( mode == 'premade' || mode == 'premade-button' ) {
				$('#back-panel').find('button').data('settings', prevmode);
				$('#back-panel').find('button').attr('data-settings', prevmode);
				$('#back-panel').show();
				$('#ib2-background-element').val($(this).data('element'));
			} else {
				$('#back-panel').hide();
			}
			
			$('#ib2-current-panel').val(mode);
			
			$('#screen-container').trigger('editor_resize');
			
			e.preventDefault();
		});
	});
	
	hideSidePanel = $('.hide-side-panel').each(function(){
		$(this).click(function(e){
			$('#editor-panel-inside').hide();
			
			$('#main-editor').removeClass('col-md-9');
			$('#main-editor').addClass('col-md-12');
			$('#editor-panel').hide();
		
			// tab settings
			$('.tab-settings-content').hide();
			$('.settings-tab').each(function(i){
				$(this).find('ul > li > a')
					.attr('title', 'Click to open settings')
					.removeClass('tab-settings-active');
				
				$(this).find('ul > li').find('i').attr('class', 'fa fa-chevron-right');
						
				$(this).find('ul > li').hide();	
			});
			
			update_popup_position();
			$('#screen-container').trigger('editor_resize');
			
			$('body').trigger("ib2autosave");
			e.preventDefault();
		});
	});
	
	// Floating Element Tools
	$("#element-pallete").draggable({ handle: "div.element-handle" });
	$('.ib2-element').mouseover(function(){
		var $this = $(this), text = $this.data('tooltip');
		if ( !$this.find('span').length ) {
			$this.append('<span>' + text + '</span>');
		}
	});

	// Graphics Drag
	$(".ib2-img-item").draggable({
		connectToSortable: ".ib2-section-content, .ib2-pop-content",
		helper: "clone",
		zIndex: 200,
		start: function(event, ui) {
			$is_drag = 1;
		}
	});
	
	// Graphics Load
	$('#graphic-type').change(function(){
		var type = $("option:selected", this).val(), id = 'graphics-list-' + type;
		
		$('.graphics-list').hide();
		if ( $('#' + id).length ) {
			$('#' + id).show();
		} else {
			$('#graphic-loader').show();
			
			var data = {
				action: 'ib2_get_graphics',
				folder: type
			};
			$.post(ajaxurl, data, function(response){
				$('#graphics-container').append(response);
				$('#graphic-loader').hide();
				
				$(".ib2-img-item")
					.unbind('draggable')
					.draggable({ 
						connectToSortable: ".ib2-section-content, .ib2-pop-content",
						helper: "clone",
						zIndex: 200,
						start: function(event, ui) {
							$is_drag = 1;
						}
					});
			});
		}
	});
	
	// DELETE ELEMENT
	$('#screen-container').on('click', '.ib2-del-btn', function(e){
		hideSidePanel.click();
		if ( confirm("Are you sure you want to delete this element?") ) {
			elID = $(this).parent().attr('id').replace('-edit-tools', '');
			if ( $('#' + elID).resizable("instance") )
				$('#' + elID).resizable("destroy");
				
			if ( $('#' + elID + ' > .el-content').resizable("instance") )
				$('#' + elID + ' > .el-content').resizable("destroy");
				
			if ( $('#' + elID).sortable("instance") )
				$('#' + elID).sortable("destroy");
				
			if ( $('#' + elID + '-css').length ) {
				$('#' + elID + '-css').remove();
			}
			
			if ( $('#' + elID + '-submit-css').length ) {
				$('#' + elID + '-submit-css').remove();
			}
			
			if ( $('#' + elID + '-countdown-css').length ) {
				$('#' + elID + '-countdown-css').remove();
			}
			
			if ( $('#' + elID + '-hover-css').length ) {
				$('#' + elID + '-hover-css').remove();
			}
			
			if ( $('#' + elID + '-sub-hover-css').length ) {
				$('#' + elID + '-sub-hover-css').remove();
			}
			
			if ( $('#' + elID).data('el') == 'quiz' )
				quiz_dropped = 0;
				
			if ( $('#' + elID).data('el') == 'optin3' )
				opt3_dropped = 0;
			
			if ( $('#' + elID).data('el') == 'section' || $('#' + elID).data('el') == 'wsection' ) {
				$('#' + elID).find('.ib2-content-el').each(function(i){
					var c = $(this), tp = c.data('el'), cid = c.attr('id');
					if ( tp == 'button' ) {
						$('#' + cid + '-css').remove();
					} else if ( tp == 'optin' ) {
						$('#' + cid + '-submit-css').remove();
					} else if ( tp == 'countdown' ) {
						$('#' + cid + '-countdown-css').remove();
					} else if ( tp == 'menu' ) {
						$('#' + cid + '-css').remove();
						$('#' + cid + '-hover-css').remove();
						$('#' + cid + '-sub-hover-css').remove();
					}
				});
			}
			
			// Remove Pop-Up
			if ( $('#' + elID + '-popup').length ) {
				$('#' + elID + '-popup').remove();
			}
			
			// Remove Hotspot Pop-Ups
			if ( $('#' + elID).find('.ib2-hotspot-el').length ) {
				$('#' + elID).find('.ib2-hotspot-el').each(function(){
					var hid = $(this).attr('id');
					if ( $('#' + hid + '-popup').length ) {
						$('#' + hid + '-popup').remove();
					}
				});
			}
			
			$('#' + elID).remove();
			$('#' + elID + '-edit-tools').remove();
			
			$('body').trigger("ib2autosave");
			$("body").trigger("ib2CommonChange");
		}

		e.preventDefault();
	});
	
	// BRING TO FRONT
	$('#screen-container').on('click', '.ib2-layerup-btn', function(e){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		zIndex = $('#' + elID).css('zIndex'), newDepth = parseInt(zIndex) + 1;
		
		if ( newDepth == 10 ) newDepth += 1;
		$('#' + elID).css('z-index', newDepth);
		
		e.preventDefault();
	});
	
	// SEND TO BACK
	$('#screen-container').on('click', '.ib2-layerdown-btn', function(e){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		zIndex = $('#' + elID).css('zIndex'), newDepth = parseInt(zIndex) - 1;
		
		if ( newDepth == 10 ) newDepth -= 1;
		$('#' + elID).css('z-index', newDepth);
		
		e.preventDefault();
	});
	
	// CLONE ELEMENT
	$('#screen-container').on('click', '.ib2-copy-btn', function(e){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		cloned = $('#' + elID), newID = 'ib2_el_' + generateID(8), type = cloned.data('el');

		cloned.removeClass('resize-border');
		cloned.find(' > .el-content').removeClass('resize-border');
		
		if ( cloned.resizable("instance") )
			cloned.resizable("destroy");
			
		if ( cloned.find(' > .el-content').resizable("instance") )
			cloned.find(' > .el-content').resizable("destroy");
				
		ib2_destroy();
			
		$('#' + elID + '-edit-tools').remove();
		
		cloned.clone().insertAfter(cloned);
		cloned.after().attr('id', newID);
		
		// Image Clone
		if ( cloned.hasClass('ib2-image-el') ) {
			cloned.find('img').attr('id', newID + '-img');
			if ( cloned.attr('data-target') && cloned.data('target') == 'popup' ) {
				var popID = cloned.find('a.ib2-open-popup').attr('href');
				if ( $(popID).length ) {
					var popCloned = $(popID);
					popCloned.clone().insertAfter(popCloned);
					popCloned.after().attr('id', newID + '-popup');
				}
				cloned.find('a.ib2-open-popup').attr('href', '#' + newID + '-popup');
			}
		}
		
		// Button Clone
		if ( cloned.hasClass('ib2-button-el') ) {
			if ( $('#' + elID + '-css').length ) {
				var clonecss = $('#' + elID + '-css');
				clonecss.clone().insertAfter(clonecss);
				clonecss.after().attr('id', newID + '-css');
				var pattern = new RegExp(elID, 'g');
				var newcsscontent = $('#' + newID + '-css').html().replace(pattern, newID);
				$('#' + newID + '-css').html(newcsscontent);
			}
			
			if ( cloned.attr('data-target') && cloned.data('target') == 'popup' ) {
				var popID = '#' + elID + '-popup';
				if ( $(popID).length ) {
					var popCloned = $(popID);
					popCloned.clone().insertAfter(popCloned);
					popCloned.after().attr('id', newID + '-popup');
				}
			}
		}
		
		// Optin Clone
		if ( cloned.hasClass('ib2-optin-el') ) {
			if ( $('#' + newID).find('#' + elID + '-submit').length ) {
				$('#' + newID).find('#' + elID + '-submit').attr('id', newID + '-submit');
			}
			
			if ( $('#' + elID + '-submit-css').length ) {
				var clonecss = $('#' + elID + '-submit-css');
				clonecss.clone().insertAfter(clonecss);
				clonecss.after().attr('id', newID + '-submit-css');
				var pattern = new RegExp(elID, 'g');
				var newcsscontent = $('#' + newID + '-submit-css').html().replace(pattern, newID);
				$('#' + newID + '-submit-css').html(newcsscontent);
			}
			
			if ( $('#' + newID).find('#' + elID + '-image').length ) {
				$('#' + newID).find('#' + elID + '-image').attr('id', newID + '-image');
			}
			
			if ( $('#' + newID).find('#' + elID + '-fb').length ) {
				$('#' + newID).find('#' + elID + '-fb').attr('id', newID + '-fb');
			}
		}
		
		// Box Clone
		if ( cloned.hasClass('ib2-box-el') ) {
			cloned.find('> .el-content > .ib2-section-content').attr('id', newID + '-box');
			
			if ( $('#' + newID + '-box').find('.ib2-content-el').length ) {
				$('#' + newID + '-box').find('.ib2-content-el').each(function(i){
					var ch = $(this), oldID = ch.attr('id'), chID = 'ib2_el_' + generateID(8);
					
					ch.attr('id', chID);
					
					if ( ch.data('el') == 'quiz' || ch.data('el') == 'optin3' || ch.data('el') == 'countdown' )
						ch.remove();
						
					if ( ch.hasClass('ib2-image-el') ) {
						ch.find('img').attr('id', chID + '-img');
						if ( ch.attr('data-target') && ch.data('target') == 'popup' ) {
							var popID = ch.find('a.ib2-open-popup').attr('href');
							if ( $(popID).length ) {
								var popCloned = $(popID);
								popCloned.clone().insertAfter(popCloned);
								popCloned.after().attr('id', chID + '-popup');
							}
							ch.find('a.ib2-open-popup').attr('href', '#' + chID + '-popup');
						}
					}
					
					if ( ch.hasClass('ib2-box-el') ) {
						ch.find('> .el-content > .ib2-section-content').attr('id', chID + '-box');
					}
					
					// Button Clone
					if ( ch.hasClass('ib2-button-el') ) {
						if ( $('#' + oldID + '-css').length ) {
							var clonecss = $('#' + oldID + '-css');
							clonecss.clone().insertAfter(clonecss);
							clonecss.after().attr('id', chID + '-css');
							var pattern = new RegExp(oldID, 'g');
							var newcsscontent = $('#' + chID + '-css').html().replace(pattern, chID);
							$('#' + chID + '-css').html(newcsscontent);
						}
						
						if ( ch.attr('data-target') && ch.data('target') == 'popup' ) {
							var popID = '#' + oldID + '-popup';
							if ( $(popID).length ) {
								var popCloned = $(popID);
								popCloned.clone().insertAfter(popCloned);
								popCloned.after().attr('id', chID + '-popup');
							}
						}
					}
					
					// Optin Clone
					if ( ch.hasClass('ib2-optin-el') ) {
						if ( $('#' + chID).find('#' + oldID + '-submit').length ) {
							$('#' + chID).find('#' + oldID + '-submit').attr('id', chID + '-submit');
						}
						
						if ( $('#' + oldID + '-submit-css').length ) {
							var clonecss = $('#' + oldID + '-submit-css');
							clonecss.clone().insertAfter(clonecss);
							clonecss.after().attr('id', chID + '-submit-css');
							var pattern = new RegExp(oldID, 'g');
							var newcsscontent = $('#' + chID + '-submit-css').html().replace(pattern, chID);
							$('#' + chID + '-submit-css').html(newcsscontent);
						}
						
						if ( $('#' + chID).find('#' + oldID + '-image').length ) {
							$('#' + chID).find('#' + oldID + '-image').attr('id', chID + '-image');
						}
						
						if ( $('#' + chID).find('#' + oldID + '-fb').length ) {
							$('#' + chID).find('#' + oldID + '-fb').attr('id', chID + '-fb');
						}
					}
				});
			}
			
			if ( $('#' + newID + '-box').find('.ib2-section-el').length ) {
				$('#' + newID + '-box').find('.ib2-section-el').each(function(i){
					var ch = $(this), chID = 'ib2_el_' + generateID(8);
					
					ch.attr('id', chID);
					
					if ( ch.find(' > .el-cols > .ib2-section-content').length ) {
						ch.find(' > .el-cols > .ib2-section-content').each(function(j){
							var gc = $(this), n = j+1, gcID = chID + '-box';
							
							if ( j > 0 )
								gcID = gcID + n;
								
							gc.attr('id', gcID);
						});
					}
				});
			}	
		}
					
		// Section Clone
		if ( type == 'section' || type == 'wsection' ) {
			if ( $('#' + newID).find(' > .el-cols > .ib2-section-content').length ) {
				$('#' + newID).find(' > .el-cols > .ib2-section-content').each(function(j){
					var gc = $(this), n = j+1, gcID = newID + '-box';
					
					if ( j > 0 )
						gcID = gcID + j;
						
					gc.attr('id', gcID);
				});
			}
			
			if ( $('#' + newID).find('.el-content-inner > .el-cols > .ib2-section-content').length ) {
				$('#' + newID).find('.el-content-inner > .el-cols > .ib2-section-content').each(function(j){
					var gc = $(this), n = j+1, gcID = newID + '-box';
					
					if ( j > 0 )
						gcID = gcID + j;
						
					gc.attr('id', gcID);
				});
			}
			
			if ( $('#' + newID).find('.ib2-content-el').length ) {
				$('#' + newID).find('.ib2-content-el').each(function(i){
					var ch = $(this), oldID = ch.attr('id'), chID = 'ib2_el_' + generateID(8);
					
					ch.attr('id', chID);
					
					if ( ch.data('el') == 'quiz' || ch.data('el') == 'optin3' || ch.data('el') == 'countdown' )
						ch.remove();
						
					if ( ch.hasClass('ib2-image-el') ) {
						ch.find('img').attr('id', chID + '-img');
						if ( ch.attr('data-target') && ch.data('target') == 'popup' ) {
							var popID = ch.find('a.ib2-open-popup').attr('href');
							if ( $(popID).length ) {
								var popCloned = $(popID);
								popCloned.clone().insertAfter(popCloned);
								popCloned.after().attr('id', chID + '-popup');
							}
							ch.find('a.ib2-open-popup').attr('href', '#' + chID + '-popup');
						}
					}
					
					if ( ch.hasClass('ib2-box-el') ) {
						ch.find('> .el-content > .ib2-section-content').attr('id', chID + '-box');
					}
					
					// Button Clone
					if ( ch.hasClass('ib2-button-el') ) {
						if ( $('#' + oldID + '-css').length ) {
							var clonecss = $('#' + oldID + '-css');
							clonecss.clone().insertAfter(clonecss);
							clonecss.after().attr('id', chID + '-css');
							var pattern = new RegExp(oldID, 'g');
							var newcsscontent = $('#' + chID + '-css').html().replace(pattern, chID);
							$('#' + chID + '-css').html(newcsscontent);
						}
						
						if ( ch.attr('data-target') && ch.data('target') == 'popup' ) {
							var popID = '#' + oldID + '-popup';
							if ( $(popID).length ) {
								var popCloned = $(popID);
								popCloned.clone().insertAfter(popCloned);
								popCloned.after().attr('id', chID + '-popup');
							}
						}
					}
					
					// Optin Clone
					if ( ch.hasClass('ib2-optin-el') ) {
						if ( $('#' + chID).find('#' + oldID + '-submit').length ) {
							$('#' + chID).find('#' + oldID + '-submit').attr('id', chID + '-submit');
						}
						
						if ( $('#' + oldID + '-submit-css').length ) {
							var clonecss = $('#' + oldID + '-submit-css');
							clonecss.clone().insertAfter(clonecss);
							clonecss.after().attr('id', chID + '-submit-css');
							var pattern = new RegExp(oldID, 'g');
							var newcsscontent = $('#' + chID + '-submit-css').html().replace(pattern, chID);
							$('#' + chID + '-submit-css').html(newcsscontent);
						}
						
						if ( $('#' + chID).find('#' + oldID + '-image').length ) {
							$('#' + chID).find('#' + oldID + '-image').attr('id', chID + '-image');
						}
						
						if ( $('#' + chID).find('#' + oldID + '-fb').length ) {
							$('#' + chID).find('#' + oldID + '-fb').attr('id', chID + '-fb');
						}
					}
				});
			}
			
			if ( $('#' + newID).find(' > .ib2-section-el').length ) {
				$('#' + newID).find('.ib2-section-el').each(function(i){
					var ch = $(this), chID = 'ib2_el_' + generateID(8);
					
					ch.attr('id', chID);
					
					if ( ch.find(' > .el-cols > .ib2-section-content').length ) {
						ch.find(' > .el-cols > .ib2-section-content').each(function(j){
							var gc = $(this), n = j+1, gcID = chID + '-box';
							
							if ( j > 0 )
								gcID = gcID + n;
								
							gc.attr('id', gcID);
						});
					}
				});
			}	
		}
		
		$('[id]').each(function (i) {
    		var ids = $('[id="' + this.id + '"]');
    		if ( ids.length > 1 ) {
    			var dupID = 'ib2_el_' + generateID(8);
    			$('[id="' + this.id + '"]:gt(0)').attr('id', dupID);
    		}
		});
		
		ib2_init();
	
		$('body').trigger("ib2autosave");
		$("body").trigger("ib2CommonChange");
		
		e.preventDefault();
	});
	
	// EDIT TEXT ELEMENT
	$('#screen-container').on('click', '.ib2-write-btn', function(e){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = $('#' + elID), type = el.data('el'), _type = type, tbWidth = $this.parent().outerWidth();
		
		$('.ib2-tab-title').trigger('blur');
		
		if ( $('#' + elID).resizable("instance") )
			$('#' + elID).resizable("destroy");
			
		if ( $('#' + elID + ' > .el-content').resizable("instance") )
			$('#' + elID + ' > .el-content').resizable("destroy");
			
		$('#' + elID).removeClass('resize-border');
		$('#' + elID + ' > .el-content').removeClass('resize-border');
		$this.parent().remove(); // remove the tools
		
		if ( type == 'text' ) {
			$('#ib2-current-text').val(elID);
			
			ib2_destroy();
	                	
			$('#' + elID).removeClass('resize-border');
			
			$('#' + elID).css('padding', '10px');
			
			ib2CreateEditor(elID);
			
			$is_text_edit = 1;
		} else if ( type == 'image' ) {
			$('#ib2-current-image').val(elID);
			$('.open-image-editor', {'data-element': 'image', 'data-type': 'image'}).trigger('click');
		}
		
	});
	
	// EDIT ELEMENT
	$('#screen-container').on('click', '.ib2-edit-btn', function(e){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = $('#' + elID), type = el.data('el'), _type = type, tbWidth = $this.parent().outerWidth();
		
		settingsOpening = 1;
		
		$('.ib2-tab-title').trigger('blur');
		
		if ( $('#' + elID).resizable("instance") )
			$('#' + elID).resizable("destroy");
			
		if ( $('#' + elID + ' > .el-content').resizable("instance") )
			$('#' + elID + ' > .el-content').resizable("destroy");
			
		$('#' + elID).removeClass('resize-border');
		$('#' + elID + ' > .el-content').removeClass('resize-border');
		$this.parent().remove(); // remove the tools
		
		// tab settings
		$('.tab-settings-content').hide();
		$('.settings-tab').each(function(i){
			$(this).find('ul > li > a')
				.attr('title', 'Click to open settings')
				.removeClass('tab-settings-active');
			
			$(this).find('ul > li').find('i').attr('class', 'fa fa-chevron-right');
		});
			
		if ( type == 'wbox' )
			type = 'box';
			
		if ( type == 'section' )
			type = 'box';
			
		if ( type == 'wsection' )
			type = 'box';
			
		var prevpanel = $('#ib2-current-panel').val();
		$('#main-editor').removeClass('col-md-12');
		$('#main-editor').addClass('col-md-9');
		$('.editor-panel-content').hide();
		$('.settings-' + type).show();
		$('#editor-panel').show();
		
		// reset all tabs display
		$('.settings-tab > ul > li').show();
		
		// get the width of the editor panel
		var panelWidth = $('#editor-panel').outerWidth(),
		panelTop = $('#editor-panel').offset().top;
		
		$('#editor-panel-inside').css({
			'width': panelWidth + 'px',
			'top': panelTop + 'px',
			'right': 0
		});
		
		$('#editor-panel-inside').fadeIn("slow");
		$('#editor-panel-inside-content').perfectScrollbar('update');
		
		$('#ib2-current-panel').val(type);
		
		$('#back-panel').hide();
		
		$('#screen-container').trigger('editor_resize');
		
		// Image Settings
		if ( type == 'image' ) {
			$('#ib2-current-image').val(elID);
			
			if ( $('#' + elID).data('target') == 'popup' ) {
				$('.img-url-target-group').hide();
				$('.img-popup-target-group').show();
			} else if ( $('#' + elID).data('target') == 'url' ) {
				$('.img-url-target-group').show();
				$('.img-popup-target-group').hide();
			} else {
				$('.img-url-target-group').hide();
				$('.img-popup-target-group').hide();
			}
			
			$('#image-link-type').val($('#' + elID).data('target'));
			
			if ( $('#' + elID).data('target') == 'url' && $('#' + elID).find('a.ib2-img-link').length ) {
				$('#img-link-url').val($('#' + elID).find('a').attr('href'));
				
				if ( $('#' + elID).find('a.ib2-img-link').attr('target') == '_blank' ) {
					$('#img-link-new').prettyCheckable('check');
				} else {
					$('#img-link-new').prettyCheckable('uncheck');
				}
			} else {
				$('#img-link-url').val('');
				$('#img-link-new').prettyCheckable('uncheck');
			}
			
			var as_ratio = 'no';
			if ( $('#' + elID).attr('data-aspect-ratio') ) {
				as_ratio = $('#' + elID).data('aspectRatio');
			}
			
			if ( as_ratio == 'yes' )
				$('#img-aspect-ratio').prettyCheckable('check');
			else
				$('#img-aspect-ratio').prettyCheckable('uncheck');
				
			if ( $('#' + elID).find('img').hasClass('img-rounded') ) {
				$('#image-style').val('rounded');
			} else if ( $('#' + elID).find('img').hasClass('img-circle') ) {
				$('#image-style').val('circle');
			} else if ( $('#' + elID).find('img').hasClass('img-thumbnail') ) {
				$('#image-style').val('thumbnail');
			} else {
				$('#image-style').val('none');
			}
			
			if ( $('#' + elID).attr('data-animation') && $('#' + elID).data('animation') != 'none' ) {
				$('#image-animation').val($('#' + elID).data('animation'));
			} else {
				$('#image-animation').val('none');
			}
			
			// Image Margins
			var imgTm = $('#' + elID).css('marginTop').replace(/px/gi, ''),
			imgBm =  $('#' + elID).css('marginBottom').replace(/px/gi, '');
			
			$('#img-tm-slider').slider('value', imgTm);
			$('#img-bm-slider').slider('value', imgBm);
			
			// Image size
			//var curWidth = $('#' + elID).find('img').width(),
			//curHeight = $('#' + elID).find('img').height();
			
			//$('#cur-img-width').val(curWidth);
			//$('#cur-img-height').val(curHeight);
			
			// caption
			if ( $('#' + elID).find('.el-caption').length ) {
				var caption = $('#' + elID).find('.el-caption').text(),
				capColor = $('#' + elID).find('.el-caption').css('color'),
				capBg = $('#' + elID).find('.el-caption').css('backgroundColor');
				
				$('#image-caption').val(caption);
				$('#image-caption-color').val(capColor);
				$('#image-caption-background').val(capBg);
			}
			
			var img = $('#' + elID).find('img').attr('src');
			
			if ( img != '' && img != $('#ib2-img-src').attr('src') )
				ib2_image(elID, img);
			else
				removeImgEl.click();
				
		}
		
		// Carousel/Slider Settings
		else if ( type == 'slides' ) {
			$('#ib2-current-slider').val(elID);
			
			// reset the fields
			$('.slide-settings').each(function(i){
				if ( i > 0 ) $(this).remove();
			});
			
			$('#carousel-width').text($('#page-width').val() + 'px');
			// build settings
			var sp = 0, slimg = '', sltitle = '', slurl = '', sContent = '';
			for ( num in carouselData[elID] ) {
				if ( sp > 0 ) {
					var sContent = ib2_slider_setting(num);
					$('#slide-images-settings').append(sContent);
				}
				
				var slimg = carouselData[elID][num].imageurl,
				sltitle = carouselData[elID][num].title,
				slurl = carouselData[elID][num].desturl;
				
				$('#slide-el-url-' + num).val(slimg);
				$('#slide-el-title-' + num).val(sltitle);
				$('#slide-el-desturl-' + num).val(slurl);
				
				sp++;
			}
			
			ib2_slider_num_sort();
		}
		
		// Text Settings
		else if ( type == 'text' ) {
			$('#ib2-current-text').val(elID);
			
			if ( $('#' + elID).attr('data-shadow') && $('#' + elID).data('shadow') == 'yes' ) {
				$('#text-el-shadow').prettyCheckable('check');
				$('.text-el-shadow-group').show();
				
				var textShadow = $('#' + elID).css('textShadow'),
				textShadow = textShadow.match(/(-?\d+px)|(rgb\(.+\))|([a-zA-Z]+)/g),
				shadowColor = textShadow[0] || '#808080',
				shadowBlur = textShadow[3] || 0;
				
				$('#text-el-shadow-color').iris("color", shadowColor);
				$('#text-el-shadow-blur-slider').slider("value", shadowBlur.replace('px', ''));
			} else {
				$('#text-el-shadow').prettyCheckable('uncheck');
				$('.text-el-shadow-group').hide();
				
				$('#text-el-shadow-color').iris("color", '#808080');
				$('#text-el-shadow-blur-slider').slider("value", 0);
			}
			
			if ( $('#' + elID).attr('data-animation') && $('#' + elID).data('animation') != 'none' ) {
				$('#text-animation').val($('#' + elID).data('animation'));
			} else {
				$('#text-animation').val('none');
			}
		}
		// Video Settings
		else if ( type == 'video' ) {
			$('#ib2-current-video').val(elID);

			$('.hosted-property').hide();
			$('.youtube-property').hide();
			$('.vimeo-property').hide();
			$('.embed-property').hide();
			
			if ( videoData[elID] ) {
				if ( videoData[elID].type == 'hosted' )
					$('.hosted-property').show();
				else if ( videoData[elID].type == 'youtube' )
					$('.youtube-property').show();
				else if ( videoData[elID].type == 'vimeo' )
					$('.vimeo-property').show();
				else if ( videoData[elID].type == 'embed' )
					$('.embed-property').show();
					
				$('#video-type').val(videoData[elID].type);
				$('#video-mp4').val(Base64.decode(videoData[elID].hosted.mp4));
				$('#video-ogg').val(Base64.decode(videoData[elID].hosted.ogg));
				$('#video-webm').val(Base64.decode(videoData[elID].hosted.webm));
				$('#video-embed').val(Base64.decode(videoData[elID].embed));
				$('#video-youtube').val(Base64.decode(videoData[elID].youtube.url));
				$('#video-vimeo').val(Base64.decode(videoData[elID].vimeo.url));
				$('#video-splash').val(Base64.decode(videoData[elID].hosted.splash));
				
				var embedcode = $('#video-embed').html(Base64.decode(videoData[elID].embed)).text();
				$('#video-embed').val(embedcode);
				
				if ( videoData[elID].autoplay == 1 )
					$('#video-autoplay').prettyCheckable('check');
				else
					$('#video-autoplay').prettyCheckable('uncheck');
				
				if ( videoData[elID].autoplay.controls == 1 )
					$('#video-no-control').prettyCheckable('check');
				else
					$('#video-no-control').prettyCheckable('uncheck');

			} else {
				$('.youtube-property').show();
				
				// reset...
				$('#video-type').val('youtube');
				$('#video-mp4').val('');
				$('#video-ogg').val('');
				$('#video-webm').val('');
				$('#video-embed').val('');
				$('#video-youtube').val('');
				$('#video-vimeo').val('');
				$('#video-splash').val('');
				
				$('#video-autoplay').prettyCheckable('uncheck');
				$('#video-no-control').prettyCheckable('uncheck');
			}
			
			// Video Margins
			var vidTm = $('#' + elID).css('marginTop').replace(/px/gi, ''),
			vidBm =  $('#' + elID).css('marginBottom').replace(/px/gi, '');
			
			$('#video-tm-slider').slider('value', vidTm);
			$('#video-bm-slider').slider('value', vidBm);
		}
		// Tabs Settings
		else if ( type == 'tabs' ) {
			$('#ib2-current-tabs').val(elID);
			$('#tabs-title-edits').html('');
			$('#' + elID).find('.ib2-tab-title').each(function(i){
				var t = $(this), j = i+1, txt = t.text();
				
				var field = '<div class="form-group">';
		    	field += '<label>Tab ' + j + ' Title</label><br />';
		    	field += '<input type="text" class="form-control tab-title-field" data-order="' + i + '" value="' + txt + '" />';
		  		field += '</div>';
		  		field += '<hr />';
		  		
		  		$('#tabs-title-edits').append(field);
		  		
			});
		}
		// HLine Settings
		else if ( type == 'hline' ) {
			$('#ib2-current-hline').val(elID);
			var color = $('#' + elID).find('.ib2-hline').css('borderTopColor'),
			width = $('#' + elID).find('.ib2-hline').css('borderTopWidth').replace(/px/gi, ''),
			style = $('#' + elID).find('.ib2-hline').css('borderTopStyle'),
			padding = $('#' + elID).find('.el-content').css('paddingTop').replace(/px/gi, '');
			
			$('#hline-color').iris('color', color);
			$('#hline-type').val(style);
			$('#hline-thick-slider').slider("value", width);
			$('#hline-space-slider').slider("value", padding);
			
		}
		
		// Code Settings
		else if ( type == 'code' ) {
			$('#ib2-current-code').val(elID);
			
			if ( $('#' + elID + ' > .el-content').resizable("instance") ) {
				$('#' + elID + ' > .el-content').resizable("destroy").removeClass('resize-border');
				$('#' + elID + '-edit-tools').remove();
			}
			
			$('#code-content').val($('#' + elID + ' > .el-content').html());
		}
		// ShortCode Settings
		else if ( type == 'shortcode' ) {
			$('#ib2-current-code').val(elID);
			
			$('#shortcode-content').val($('#' + elID).html());
		}
		// Comment Settings
		else if ( type == 'comment' ) {
			$('#ib2-current-comment').val(elID);
			
			var mode = $('#' + elID).data('comment');
			$('#comment-system').val(mode);
		}
		// Date Settings
		else if ( type == 'date' ) {
			$('#ib2-current-date').val(elID);
			
			var color = $('#' + elID).css('color'),
			font = $('#' + elID).css('fontFamily'),
			size = $('#' + elID).css('fontSize').replace('px', ''),
			tz = $('#' + elID).data('tz'),
			format = $('#' + elID).data('format');
			
			$('#date-format').val(format);
			$('#date-timezone').val(tz);
			$('#date-font-face').val(font);
			$('#date-font-color').iris("color", color);
			$('#date-font-size-slider').slider("value", size);
		}
		// Quiz Settings
		else if ( type == 'quiz' ) {
			$('#ib2-current-quiz').val(elID);
			
			var questions = $('#' + elID).data('questions'),
			titleColor = $('#' + elID).find('.ib2-quiz-page > h3').css('color'),
			titleSize = $('#' + elID).find('.ib2-quiz-page > h3').css('fontSize').replace('px', ''),
			titleFont = $('#' + elID).find('.ib2-quiz-page > h3').css('fontFamily'),
			textColor = $('#' + elID).find('.ib2-answer-list').css('color'),
			textSize = $('#' + elID).find('.ib2-answer-list').css('fontSize').replace('px', ''),
			textFont = $('#' + elID).find('.ib2-answer-list').css('fontFamily');
			
			$('#quiz-questions-slider').slider('value', questions);
			$('#quiz-question-font').val(titleFont);
			$('#quiz-question-color').iris("color", titleColor);
			$('#quiz-question-size-slider').slider('value', titleSize);
			$('#quiz-answer-font').val(textFont);
			$('#quiz-answer-color').iris("color", textColor);
			$('#quiz-answer-size-slider').slider('value', textSize);
		}
		
		// Share Settings
		else if ( type == 'share' ) {
			$('#ib2-current-share').val(elID);
			
			var mode = $('#' + elID).data('mode'), sharers = ["facebook", "twitter", "linkedin", "google"];
			$('#social-share-style').val(mode);
			for ( var i = 0; i < sharers.length; i++ ) {
				if ( $('#' + elID).find('.ib2-' + sharers[i] + '-share').is(":visible") ) {
					$('#share-' + sharers[i] + '-btn').prettyCheckable('check');
				} else {
					$('#share-' + sharers[i] + '-btn').prettyCheckable('uncheck');
				}
			}
		}
		// Countdown Settings
		else if ( type == 'countdown' ) {
			$('#ib2-current-countdown').val(elID);
			
			var mode = $('#' + elID).data('mode'),
			tz = $('#' + elID).data('tz'),
			target = $('#' + elID).data('target'),
			action = $('#' + elID).data('end'),
			cStyle = $('#' + elID).data('style'),
			url = decodeURIComponent($('#' + elID).data('url')),
			cid = $('#' + elID).data('cid'),
			fontColor = $('#' + elID).css('color'),
			backgroundColor = '#76a8bb',
			borderColor = '#76a8bb',
			borderWidth = 1,
			borderStyle = 'solid',
			fontFace = $('#' + elID).css('fontFamily'),
			fontSize = $('#' + elID).css('fontSize').replace('px', ''),
			textShadow = $('#' + elID).css('textShadow'),
			shadow = textShadow.match(/(-?\d+px)|(rgb\(.+\))|([a-zA-Z]+)/g),
			shadowColor = shadow[0];
			
			
			if ( $('#' + elID + ' > .ib2-digit').length ) {
				backgroundColor = $('#' + elID + ' > .ib2-digit').css('backgroundColor');
				borderColor = $('#' + elID + ' > .ib2-digit').css('borderLeftColor');
				borderWidth = $('#' + elID + ' > .ib2-digit').css('borderLeftWidth').replace('px', '');
				borderStyle = $('#' + elID + ' > .ib2-digit').css('borderLeftStyle') == 'none' ? 'solid' : $('#' + elID + ' > .ib2-digit').css('borderLeftStyle');
			}
			
			$('#countdown-type').val(mode);
			
			$('#countdown-color').iris("color", backgroundColor);
			$('#countdown-font-color').iris("color", fontColor);
			$('#countdown-text-shadow-color').iris("color", textShadow);
			
			$('#countdown-text-font').val(fontFace);
			$('#countdown-font-size-slider').slider('value', fontSize);
			
			$('#countdown-tz').val(tz);
			$('#countdown-style').val(cStyle);
			
			if ( mode == 'date' ) {
				$('.countdown-non-evergreen').show();
				$('.c-date-type').show();
				$('.c-non-date-type').hide();
				
				var parts = target.split(" "),
				date = parts[0],
				time = parts[1].split(":"),
				hour = time[0],
				min = time[1];
				
				$('#countdown-date').val(date);
				$('#countdown-hour-slider').slider("value", hour);
				$('#countdown-min-slider').slider("value", min);
			} else {
				$('.countdown-non-evergreen').show();
				if ( mode == 'evergreen' )
					$('.countdown-non-evergreen').hide();
					
				$('.c-date-type').hide();
				$('.c-non-date-type').show();
				
				var parts = target.split(":"),
				day = parts[0],
				hour = parts[1],
				min = parts[2];
				
				day = day / 1000 / 60 / 60 / 24;
				hour = hour / 1000 / 60 / 60;
				min = min / 1000 / 60;
				
				$('#countdown-day-slider').slider("value", day);
				$('#countdown-hour-slider').slider("value", hour);
				$('#countdown-min-slider').slider("value", min);
			}
			
			$('.countdown-box-style').hide();
			$('.countdown-text-group').hide();
			if ( cStyle == 'flat-box' || cStyle == 'glossy-box' ) {
				$('.countdown-box-style').show();
			}
			
			$('#countdown-text-before').val('');
			$('#countdown-text-after').val('');
			if ( cStyle == 'text' ) {
				var bText = ( $('#' + elID).data('before') != '' ) ? decodeURIComponent($('#' + elID).data('before')) : '',
				aText = ( $('#' + elID).data('after') != '' ) ? decodeURIComponent($('#' + elID).data('after')) : '';
				$('#countdown-text-before').val(bText);
				$('#countdown-text-after').val(aText);
				$('.countdown-text-group').show();
			}
			
			$('.expiry-action-redirect').hide();
			$('#countdown-action-type').val(action);
			if ( action == 'redirect' )
				$('.expiry-action-redirect').show();
				
			$('#countdown-url').val(url);
			
			$('#countdown-border-color').iris("color", borderColor);
			$('#countdown-border-style').val(borderStyle);
			$('#countdown-border-thick-slider').slider("value", borderWidth);
		}
		// Button Settings
		else if ( type == 'button' ) {
			$('#ib2-current-button').val(elID);
			$('#ib2-button-mode').val('normal');
			
			var element = $('#' + elID).find('a.ib2-button'),
			type = $('#' + elID).data('buttonType'),
			url = $('#' + elID).find('a.ib2-button').attr('href'),
			target = $('#' + elID).find('a.ib2-button').attr('target');
			
			ib2_button_setting(elID, element, type, url);
			
			if ( target == '_blank' ) {
				$('#button-link-new').prettyCheckable('check');
			} else {
				$('#button-link-new').prettyCheckable('uncheck');
			}
			
			if ( $('#' + elID).data('target') == 'popup' ) {
				$('.popup-target-group').show();
				$('.url-target-group').hide();
				$('#target-link-type').val('popup');
			} else {
				$('.popup-target-group').hide();
				$('.url-target-group').show();
				$('#target-link-type').val('url');
			}
			
			if ( $('#' + elID).attr('data-animation') && $('#' + elID).data('animation') != 'none' ) {
				$('#button-animation').val($('#' + elID).data('animation'));
			} else {
				$('#button-animation').val('none');
			}
		}
		// Optin Form Settings
		else if ( type == 'optin' ) {
			$('#ib2-current-optin').val(elID);
			
			var codeID = elID + '-rawcode';
			
			if ( $('#' + codeID).length ) {
				$('#optin-html-code').val($('#' + codeID).html());
			} else {
				$('#optin-html-code').val('');
			}
			
			if ( $('#' + codeID).length && $('#' + codeID).data('process') == 'yes' ) {
				$('.optin-unprocess-group').hide();
				$('#optin-process').hide();
				$('#change-optin').show();
				$('.optin-process-msg').show();
			} else {
				$('.optin-unprocess-group').show();
				$('#optin-process').show();
				$('#change-optin').hide();
				$('.optin-process-msg').hide();
			}
			
			if ( $('#' + elID).attr('data-form-mode') ) {
				var fmode = $('#' + elID).data('formMode');
				$('#optin-form-mode').val(fmode);
			}
			
			// Fields
			if ( $('#' + elID).find('input[type=text]').hasClass('input-lg') && !$('#' + elID).find('input[type=text]').hasClass('input-ln') ) {
				$('#field-size').val('big');
			} else if ( $('#' + elID).find('input[type=text]').hasClass('input-sm') && !$('#' + elID).find('input[type=text]').hasClass('input-ln') ) {
				$('#field-size').val('small');
			} else if ( $('#' + elID).find('input[type=text]').hasClass('input-sm') && $('#' + elID).find('input[type=text]').hasClass('input-ln') ) {
				$('#field-size').val('smalllong');
			} else if ( $('#' + elID).find('input[type=text]').hasClass('input-lg') && $('#' + elID).find('input[type=text]').hasClass('input-ln') ) {
				$('#field-size').val('biglong');
			} else if ( !$('#' + elID).find('input[type=text]').hasClass('input-lg') && !$('#' + elID).find('input[type=text]').hasClass('input-sm') && $('#' + elID).find('input[type=text]').hasClass('input-ln') ) {
				$('#field-size').val('normallong');
			} else {
				$('#field-size').val('normal');
			}
			
			if ( $('#' + elID).find('input[type=text]').hasClass('field-normal-thick') ) {
				$('#field-style').val('field-normal-thick');
			} else if ( $('#' + elID).find('input[type=text]').hasClass('field-sharp') ) {
				$('#field-style').val('field-sharp');
			} else if ( $('#' + elID).find('input[type=text]').hasClass('field-sharp-thick') ) {
				$('#field-style').val('field-sharp-thick');
			} else {
				$('#field-style').val('field-normal');
			}
			
			var fColor = $('#' + elID).find('input[type=text]').css('backgroundColor'),
			fBorderColor = $('#' + elID).find('input[type=text]').css('borderLeftColor');
			
			$('#field-background-color').iris("color", fColor);
			$('#field-border-color').iris("color", fBorderColor);
			
			// Image Button
			if ( $('#' + elID + '-image').is(":visible") ) {
				$('#optin-button-type').val('image');
				$('.image-button-group').show();
				$('.css-button-group').hide();
			} else {
				$('#optin-button-type').val('css');
				$('.image-button-group').hide();
				$('.css-button-group').show();
			}
			
			var buttonImg = $('#' + elID + '-image').attr('src');
			if ( buttonImg ) {
				ib2_optin_image(elID, buttonImg);
			} else {
				removeOptinImg.click();
			}
			
			// Facebook...
			var fb = $('#' + elID + '-fb'),
			fbtxt = fb.find('.ib2-facebook-optin-txt'),
			fbbtn = fb.find('.ib2-fb-button'),
			fbText = fbtxt.text(),
			fbLabel = fbbtn.text(),
			fbFontFace = fbtxt.css('fontFamily') || $('#body-text-font').val(),
			fbFontColor = fbtxt.css('color') || '#3a3a3a',
			fbFontSize = fbtxt.css('fontSize').replace('px', '') || 14;
			
			if ( fb.is(":visible") ) {
				$('#facebook-opt-enable').prettyCheckable('check');
				$('.facebook-opt-group').show();
			} else {
				$('#facebook-opt-enable').prettyCheckable('uncheck');
				$('.facebook-opt-group').hide();
			}
			
			$('#facebook-optin-label').val(fbLabel);
			$('#facebook-optin-text').val(fbText);
			$('#facebook-optin-font').val(fbFontFace);
			$('#facebook-optin-color').iris("color", fbFontColor);
			$('#facebook-optin-size-slider').slider("value", fbFontSize);
			
			if ( $('#' + elID).find('.form-fields').is(":visible") ) {
				$('#facebook-opt-only').prettyCheckable('uncheck');
			} else {
				$('#facebook-opt-only').prettyCheckable('check');
			}
			
			// Webinar
			if ( $('#' + elID).find('input[name=_webinar_key]').length ) {
				$('#optin-webinar').val($('#' + elID).find('input[name=_webinar_key]').val());
				if ( $('#optin-html-code').is(":visible") && $('#optin-html-code').val() == '' ) {
					$('.webinar-signup-group').show();
				} else {
					$('.webinar-signup-group').hide();
				}
			}
			
			if ( $('#' + elID).find('input[name=_webinar_redirect]').length ) {
				$('#optin-webinar-redirect').val($('#' + elID).find('input[name=_webinar_redirect]').val());	
			} else {
				$('#optin-webinar-redirect').val('');
			}
		}
		// Menu Settings
		else if ( type == 'menu' ) {
			$('#ib2-current-menu').val(elID);
			
			var menuSelected = $('#' + elID).data('menu'),
			menuStyle = $('#' + elID).data('style'),
			menuRadius = $('#' + elID + ' > nav > ul.ib2-navi').css('borderTopLeftRadius').replace(/px/gi, ''),
			bgColor = $('#' + elID + ' > nav > ul.ib2-navi').css('backgroundColor'),
			txtColor = $('#' + elID).find('ul.ib2-navi li > a').css('color'),
			subBgColor = $('#' + elID).find('ul.ib2-navi ul').css('backgroundColor'),
			subTxtColor = $('#' + elID).find('ul.ib2-navi-plain ul li a').css('color');
			
			var css = $('#' + elID + '-hover-css').html(),
			obj = ib2_css2json(css);
			
			if ( obj.color ) {
				var hoverTxtColor = obj.color.replace(" ", '');
				$('#menu-hover-text-color').iris('color', hoverTxtColor);
			}
			
			if ( obj.backgroundColor ) {
				var hoverBgColor = obj.backgroundColor.replace(" ", '');
				$('#menu-hover-background').iris('color', hoverBgColor);
			}
			
			var css2 = $('#' + elID + '-sub-hover-css').html(),
			obj2 = ib2_css2json(css2);
			
			if ( obj2.color ) {
				var subHoverTxtColor = obj2.color.replace(" ", '');
				$('#sub-menu-hover-text-color').iris('color', subHoverTxtColor);
			}
			
			if ( obj2.backgroundColor ) {
				var subHoverBgColor = obj2.backgroundColor.replace(" ", '');
				$('#sub-menu-hover-background').iris('color', subHoverBgColor);
			}
			
			if ( menuStyle == 'plain' || menuStyle == 'plain-pipe' ) {
				$('.non-plain-menu-group').hide();
			} else {
				$('.non-plain-menu-group').show();
			}
			
			$('#menu-list').val(menuSelected);
			$('#menu-style').val(menuStyle);
			$('#menu-corners-slider').slider("value", menuRadius);
			$('#menu-text-color').iris('color', txtColor);
			$('#menu-background').iris('color', bgColor);
			$('#sub-menu-text-color').iris('color', subTxtColor);
			$('#sub-menu-background').iris('color', subBgColor);
		}
		// Box Settings
		else if ( type == 'box' ) {
			$('#ib2-current-box').val(elID);
			
			$('#box-settings-title').text('Box Settings');
			
			if ( _type == 'wbox' )
				$('#box-settings-title').text('Background Box Settings');
				
			if ( _type == 'section' || _type == 'wsection' )
				$('#box-settings-title').text('Section Settings');
				
			if ( $('#' + elID).hasClass('ib2-columns-el') )
				$('#box-settings-title').text('Columns Settings');
				
			var glossy = false;
			if ( $('#' + elID).attr('data-glossy') && $('#' + elID).data('glossy') == 'yes' ) {
				glossy = true;
			}
			
			// Setting Values Setup For Box
			var bgEl = ( _type != 'box' ) ? $('#' + elID + ' > .el-content') : $('#' + elID + '-box');
			
			var boxColor = bgEl.css('backgroundColor'),
			borderLeftColor = bgEl.css('borderLeftColor'),
			borderRightColor = bgEl.css('borderRightColor'),
			borderTopColor = bgEl.css('borderTopColor'),
			borderBottomColor = bgEl.css('borderBottomColor'),
			borderLeftStyle = bgEl.css('borderLeftStyle'),
			borderRightStyle = bgEl.css('borderRightStyle'),
			borderTopStyle = bgEl.css('borderTopStyle'),
			borderBottomStyle = bgEl.css('borderBottomStyle'),
			borderLeftWidth = bgEl.css('borderLeftWidth').replace(/px/gi, ''),
			borderRightWidth = bgEl.css('borderRightWidth').replace(/px/gi, ''),
			borderTopWidth = bgEl.css('borderTopWidth').replace(/px/gi, ''),
			borderBottomWidth = bgEl.css('borderBottomWidth').replace(/px/gi, ''),
			borderTopRadius = bgEl.css('borderTopLeftRadius').replace(/px/gi, ''),
			borderBottomRadius = bgEl.css('borderBottomLeftRadius').replace(/px/gi, ''),
			boxShadow = bgEl.css('boxShadow'),
			boxOpac = bgEl.css('opacity'),
			bgImgType = $('#' + elID).data('imgMode'),
			marginTop = $('#' + elID).css('marginTop').replace(/px/gi, ''),
			marginBot = $('#' + elID).css('marginBottom').replace(/px/gi, ''),
			paddingTop = bgEl.css('paddingTop').replace(/px/gi, ''),
			paddingBottom = bgEl.css('paddingBottom').replace(/px/gi, ''),
			paddingLeft = bgEl.css('paddingLeft').replace(/px/gi, ''),
			paddingRight = bgEl.css('paddingRight').replace(/px/gi, ''),
			bgImg = bgEl.css('backgroundImage'),
			bgStyle = bgEl.css('backgroundRepeat'),
			bgPos = bgEl.css('backgroundPosition'),
			bgAtth = bgEl.css('backgroundAttachment'),
			bgUrl = /url\(\s*(['"]?)(.*?)\1\s*\)/g.exec(bgImg);
			
			// Margin & Padding
			$('#box-tm-slider').slider("value", marginTop);
			$('#box-bm-slider').slider("value", marginBot);
			$('#box-vp-slider').slider("value", paddingTop);
			$('#box-hp-slider').slider("value", paddingLeft);
			$('#box-vp2-slider').slider("value", paddingBottom);
			$('#box-hp2-slider').slider("value", paddingRight);
			
			// Background
			bgUrl = bgUrl ? bgUrl[2] : "";
			
			if ( bgUrl != '' ) {
				$('#box-image-prev').html('<img class="img-thumbnail img-responsive" src="' + bgUrl + '" border="0" />');
				$('#box-bg-url').val(bgUrl);
				$('#box-img-rmv').show();
			} else {
				$('#box-image-prev').html('');
				$('#box-bg-url').val('');
				$('#box-img-rmv').hide();
			}
			
			if ( bgPos == '0% 0%' ) {
				bgPos = 'left top';
			} else if ( bgPos == '0% 50%' ) {
				bgPos = 'left center';
			} else if ( bgPos == '0% 100%' ) {
				bgPos = 'left bottom';
			} else if ( bgPos == '50% 0%' ) {
				bgPos = 'center top';
			} else if ( bgPos == '50% 50%' ) {
				bgPos = 'center center';
			} else if ( bgPos == '50% 100%' ) {
				bgPos = 'center bottom';
			} else if ( bgPos == '100% 0%' ) {
				bgPos = 'right top';
			} else if ( bgPos == '100% 50%' ) {
				bgPos = 'right center';
			} else if ( bgPos == '100% 100%' ) {
				bgPos = 'right bottom';
			}
			
			$('#box-bgrepeat').val(bgStyle);
			$('#box-bgpos').val(bgPos);
			$('#box-bgattach').val(bgAtth);
			
			// Shadows
			if ( boxShadow == 'none' ) {
				$('#box-shadow-type').val('none');
				$('#box-shadow-color').iris("color", "#CCCCCC");
				$('#box-hshadow-slider').slider("value", 0);
				$('#box-vshadow-slider').slider("value", 0);
			} else {
				var shadow = boxShadow.match(/(-?\d+px)|(rgb\(.+\))|([a-zA-Z]+)/g);
				if ( typeof(shadow[5]) !== "undefined" && shadow[5] !== false ) {
					$('#box-shadow-type').val('inset');
				} else {
					$('#box-shadow-type').val('outset');
				}
				
				$('#box-shadow-color').iris("color", shadow[0]);
				$('#box-hshadow-slider').slider("value", shadow[1].replace(/px/gi, ''));
				$('#box-vshadow-slider').slider("value", shadow[2].replace(/px/gi, ''));
			}
			
			$('#box-opacity-slider').slider("value", (boxOpac * 100));
			
			// Single
			if ( boxColor == 'transparent' ) {
				$('#box-transparent').prettyCheckable('check');
				$('.box-background-form').hide();
			} else {
				$('#box-color').iris('color', boxColor);
				$('#box-transparent').prettyCheckable('uncheck');
				$('.box-background-form').show();
			}
			
			$('#box-border-color').iris('color', borderLeftColor);
			$('#box-border-type').val(borderLeftStyle);
			$('#box-border-thick-slider').slider("value", borderLeftWidth);
			
			// Left
			$('#box-left-border-color').iris('color', borderLeftColor);
			$('#box-left-border-type').val(borderLeftStyle);
			$('#box-left-border-thick-slider').slider("value", borderLeftWidth);
			
			// Right
			$('#box-right-border-color').iris('color', borderRightColor);
			$('#box-right-border-type').val(borderRightStyle);
			$('#box-right-border-thick-slider').slider("value", borderRightWidth);
			
			// Top
			$('#box-top-border-color').iris('color', borderTopColor);
			$('#box-top-border-type').val(borderTopStyle);
			$('#box-top-border-thick-slider').slider("value", borderTopWidth);
			
			// Bottom
			$('#box-bottom-border-color').iris('color', borderBottomColor);
			$('#box-bottom-border-type').val(borderBottomStyle);
			$('#box-bottom-border-thick-slider').slider("value", borderBottomWidth);
			
			$('#box-corners-slider').slider("value", borderTopRadius);
			$('#box-corners-bot-slider').slider("value", borderBottomRadius);
			
			if ( $('#' + elID).data('borderType') == 'multi' ) {
				$('#border-single-set').hide();
  				$('#border-multi-set').show();
				$('#indiv-box-border').prettyCheckable('check');
			} else {
				$('#border-single-set').show();
  				$('#border-multi-set').hide();
				$('#indiv-box-border').prettyCheckable('uncheck');
			}
			
			var delay = ( $('#' + elID).data('delay') ) ? $('#' + elID).data('delay') : 'none';
			if ( delay != 'none' ) {
				$('#enable_box_delay').prettyCheckable('check');
				var delays = $('#' + elID).data('delay').split(":"),
				hour = delays[0], min = delays[1], secs = delays[2];
				$('.delay-group').show();
			} else {
				$('#enable_box_delay').prettyCheckable('uncheck');
				var hour = 0, min = 0, secs = 0;
				$('.delay-group').hide();
			}

			if ( glossy )
				$('#box-color-glossy').prettyCheckable('check');
			else
				$('#box-color-glossy').prettyCheckable('uncheck');
			
			$('#box-delay-hour-slider').slider("value", hour);
			$('#box-delay-min-slider').slider("value", min);
			$('#box-delay-secs-slider').slider("value", secs);
			
			var animation = ( $('#' + elID).data('animation') ) ? $('#' + elID).data('animation') : 'none';
			$('#box-animation').val(animation);
			
			// reset glossy state	
			glossy = false;

			// background video
			$('#box-bgvid').prettyCheckable('uncheck');
			$('#bgvideo-set').hide();
			$('#bgvideo-mp4').val('');
			$('#bgvideo-ogg').val('');
			$('#bgvideo-webm').val('');
			if ( $('#' + elID + '_bgvid').length ) {
				$('#bgvideo-set').show();
				$('#box-bgvid').prettyCheckable('check');
				if ( $('#' + elID + '_bgvid').attr('data-mp4') ) {
					$('#bgvideo-mp4').val($('#' + elID + '_bgvid').data('mp4'));
					$('#bgvideo-ogg').val($('#' + elID + '_bgvid').data('ogg'));
					$('#bgvideo-webm').val($('#' + elID + '_bgvid').data('webm'));
				}
			}
		}
		
		if ( _type == 'wbox' )
			$('.not-wbox').hide();
			
		if ( $('#' + elID).hasClass('ib2-popup') || $('#' + elID).hasClass('ib2-slider-el') )
			$('.not-popup').hide();
			
		// fix edit buttons position
		var toolLeft = ( $('#' + elID).position().left + $('#' + elID).outerWidth() ) - tbWidth;
		$this.parent().css('left', toolLeft + 'px');

		settingsOpening = 0;
		
		e.preventDefault();
		e.stopPropagation();
	});
	
	// SEO CHECKBOXES
	$('.seo-checks').each(function(){
		var $this = $(this), id = $this.attr('id'),
		el = $('#' + id);
		
		el.prettyCheckable();
	});
	
	$('#advanced-seo').toggle(function(e){
		$('#advanced-seo-checks').show("slow");
		$(this).find('a').text('- Hide Advanced');
		
		e.preventDefault();
	}, function(e){
		$('#advanced-seo-checks').hide("slow");
		$(this).find('a').text('+ Show Advanced');
		
		e.preventDefault();
	});
	
	// Global Font Face
	$('#body-text-font').change(function(){
		var ff = $("option:selected", this).val();
		var style = '#screen-container { font-family: ' + ff + '; font-size: ' + $('#body-text-size').val() + 'px; color:' + $('#body-text-color').val() + '; }';
		style += '#screen-container a { color: ' + $('#body-link-color').val() + '; }';
		style += '#screen-container a:hover, #screen-container a:focus { color: ' + $('#body-link-hover-color').val() + '; }';

        $("#editor-body-typo").html(style);
        
        $("body").trigger("ib2GlobalChange", ["input", "body-text-font", ff, 'fontFace']);
	});
	
	// Page Background Image
	$('#background-image-upload').click(function(e){
		ib2media('screen-container', 'background');
		
		$("body").trigger("ib2GlobalChange", ["input", "body-bg-url", $('#body-bg-url').val(), 'backgroundImg']);
		e.preventDefault();
	});
	
	$('#background-repeat').on('change', function(){
		var option = $("option:selected", this).val();
		$('#screen-container').css('background-repeat', option);
		
		$("body").trigger("ib2GlobalChange", ["input", "background-repeat", option, 'backgroundRepeat']);
	});
  		
  	$('#background-pos').on('change', function(){
		var option = $("option:selected", this).val();
		$('#screen-container').css('background-position', option);
		
		$("body").trigger("ib2GlobalChange", ["input", "background-pos", option, 'backgroundPos']);
	});
	
	$('#background-attach').on('change', function(){
		var option = $("option:selected", this).val();
		$('#screen-container').css('background-attachment', option);
		
		$("body").trigger("ib2GlobalChange", ["input", "background-attach", option, 'backgroundAttach']);
	});
  		
	// Remove Page Background Image
	removeBgImg = $('.remove-background-img').click(function(e){
  		$('#screen-container').css('background-image', 'none');
  		$('#background-image-prev').find('img').remove();
		$('#body-bg-url').val('');
		
  		$(this).parent().hide();
  		
  		$("body").trigger("ib2GlobalChange", ["input", "body-bg-url", '', 'backgroundImg']);
  		
  		e.preventDefault();
  	});
  	
  	$('#body-bg-url').bind('keyup blur', function(){
  		var img = $(this).val();
  		
  		if ( img != '' )
	  		ib2_body_background('screen-container', img);	
  		else
  			removeBgImg.click();
  			
  		$("body").trigger("ib2GlobalChange", ["input", "body-bg-url", $('#body-bg-url').val(), 'backgroundImg']);
  	});
  	
  	$('#background-video-mute').prettyCheckable();
	$('#background-video-loop').prettyCheckable();
	$('#background-video-ctrl').prettyCheckable();
	
  	$('.ib2-background-item').each(function(e){
  		$(this).click(function(e){
  			var $this = $(this), type = $('#ib2-background-element').val(), elID, el;
  			if ( type == 'screen-container' ) {
  				elID = type;
  			} else {
  				elID = $('#ib2-current-' + type).val();
  			}
  			el = $('#' + elID);
  			if ( $this.hasClass('ib2-background-none') ) {
  				el.css('background-image', 'none');
  				if ( type == 'screen-container' ) {
  					removeBgImg.click();
  				} else if ( type == 'box' ) {
  					removeBoxImg.click();
  				} else if ( type == 'optin' ) {
  					removeOptinImg.click();
  				}
  			} else {
  				var img_url = $this.find('img').attr('src');
  				if ( type == 'screen-container' ) {
  					ib2_body_background(elID, img_url);
  					$('#body-bg-mode').val('premade');
  				} else if ( type == 'box' ) {
  					if ( el.data('el') == 'box' ) {
	  					ib2_box_background(elID, img_url);
  					} else {
  						ib2_section_background(elID, img_url);
  					}
  					$('#' + elID).data('imgMode', 'premade');
					$('#' + elID).attr('data-img-mode', 'premade');
  				} else if ( type == 'optin' ) {
  					ib2_optin_image(elID, img_url);
  				}
  			}
  			$('.border-selected').removeClass('border-selected');
  			$this.addClass('border-selected');
  			e.preventDefault();
  		});
  	});
	
	// ============================== CAROUSEL/SLIDER ==============================
	// Slider Image Upload
	$('body').on('click', '.slide-image-upload-btn', function(e){
		var elID = $('#ib2-current-slider').val(), num = $(this).data('slideNum');
		ib2media(elID, 'slider', num);
		
		$("body").trigger("ib2CommonChange");
		e.preventDefault();
	});
	
	$('body').on('keyup blur', '.slider-image-field', function(e){
		var elID = $('#ib2-current-slider').val(), num = $(this).data('slideNum');
		
		if ( !carouselData[elID] || typeof carouselData[elID] === 'undefined' || carouselData[elID] == null ) {
			carouselData[elID] = {};
		}
		
		if ( !carouselData[elID][num] || typeof carouselData[elID][num] === 'undefined' || carouselData[elID][num] == null ) {
			carouselData[elID][num] = {};
		}
		
		carouselData[elID][num].imageurl = $(this).val();
	});
	
	$('body').on('keyup blur', '.slider-title-field', function(e){
		var elID = $('#ib2-current-slider').val(), num = $(this).data('slideNum');
		
		if ( !carouselData[elID] || typeof carouselData[elID] === 'undefined' || carouselData[elID] == null ) {
			carouselData[elID] = {};
		}
		
		if ( !carouselData[elID][num] || typeof carouselData[elID][num] === 'undefined' || carouselData[elID][num] == null ) {
			carouselData[elID][num] = {};
		}
		
		carouselData[elID][num].title = $(this).val();
	});
	
	$('body').on('keyup blur', '.slider-url-field', function(e){
		var elID = $('#ib2-current-slider').val(), num = $(this).data('slideNum');
		
		if ( !carouselData[elID] || typeof carouselData[elID] === 'undefined' || carouselData[elID] == null ) {
			carouselData[elID] = {};
		}
		
		if ( !carouselData[elID][num] || typeof carouselData[elID][num] === 'undefined' || carouselData[elID][num] == null ) {
			carouselData[elID][num] = {};
		}
		
		carouselData[elID][num].desturl = $(this).val();
	});
	
	$('.add-slide-btn').click(function(e){
		var elID = $('#ib2-current-slider').val(), num = $(this).data('nextSlidenum'),
		newnum = parseInt(num) + 1, fieldContent = ib2_slider_setting(num);
		
		$('#slide-images-settings').append(fieldContent);
		ib2_slider_num_sort();
		
		$(this).attr('data-next-slidenum', newnum);
		$(this).data('nextSlidenum', newnum);
		
		if ( !carouselData[elID] || typeof carouselData[elID] === 'undefined' || carouselData[elID] == null ) {
			carouselData[elID] = {};
		}
		
		if ( !carouselData[elID][num] || typeof carouselData[elID][num] === 'undefined' || carouselData[elID][num] == null ) {
			carouselData[elID][num] = {};
		}
		
		carouselData[elID][num].imageurl = '';
		carouselData[elID][num].title = '';
		carouselData[elID][num].desturl = '';
		
		e.preventDefault();
	});
	
	$('body').on('click', '.delete-slide-settings', function(e){
		var elID = $('#ib2-current-slider').val(), num = $(this).data('slideNum');
		
		if ( confirm('Are you sure you want to delete this slide?') ) {
			$(this).parent().remove();
			delete carouselData[elID][num];
			setTimeout(function(){
				ib2_slider_num_sort();
			}, 1000);
		}
		e.preventDefault();
	});
	
	// ============================== FAVICON ==============================
	
	// Favicon Upload
	$('#favicon-upload').click(function(e){
		ib2media('favicon-url', 'favicon');
		e.preventDefault();
	});
	
	$("#favicon-url").bind("keyup blur paste", function(e){
		var $this = $(this), fav = $this.val();
		if ( fav != '' ) {
			ib2_favicon('favicon-url', fav, 'unknown');
		} else {
			removeFavicon.click();
		}
	});
	
	removeFavicon = $('.remove-favicon-el').click(function(e){
		$('#favicon-url').val('');
		$('#favicon-prev').html('');
		$('#favicon-el-rmv').hide();
		e.preventDefault();
	});
	
	// ============================== IMAGE ==============================
	
	$('#image-style').change(function(){
  		var elID = $('#ib2-current-image').val(),
  		opt = $("option:selected", this).val();
  		
  		$('#' + elID).find('img').removeClass('img-rounded img-thumbnail img-circle');
  		if ( opt != 'none' )
  			$('#' + elID).find('img').addClass('img-' + opt);
  			
  		$("body").trigger("ib2CommonChange");
 	});
 	
	$('#image-animation').change(function(){
  		var elID = $('#ib2-current-image').val(),
  		opt = $("option:selected", this).val();
  		
  		if ( opt == 'none' ) {
  			$('#' + elID).removeClass('ib2-text-animation');
  			$('#' + elID).data('animation', 'none');
  			$('#' + elID).attr('data-animation', 'none');
  		} else {
  			$('#' + elID).addClass('ib2-text-animation');
  			$('#' + elID).data('animation', opt);
  			$('#' + elID).attr('data-animation', opt);
  		}
  		
  		$("body").trigger("ib2CommonChange");
  	});
  	
	// Image Caption
	$('#image-caption').bind('keyup blur', function(){
		var elID = $('#ib2-current-image').val(),
		caption = $.trim($(this).val());
		
		if ( caption != '' ) {
			var width = $('#' + elID + ' > .el-content').width(),
				color = $('#image-caption-color').val(),
				background = $('#image-caption-background').val();
				
			if ( !$('#' + elID).find('.el-caption').length ) {
				$('#' + elID).append('<div class="el-caption"></div>');
			}
			
			$('#' + elID).find('.el-caption').text(caption);
			$('#' + elID).find('.el-caption').css({
				'width': width + 'px',
				'color': color,
				'background-color': background
			});
		} else {
			if ( $('#' + elID).find('.el-caption').length ) {
				$('#' + elID).find('.el-caption').remove();
			}
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	// Image Upload
	$('#image-upload').click(function(e){
		var elID = $('#ib2-current-image').val();
		ib2media(elID, 'image');
		
		$("body").trigger("ib2CommonChange");
		e.preventDefault();
	});
	
	// Image Link
	$("#img-link-url").bind("keyup blur paste", function(e){
		var $this = $(this), imgID = $('#ib2-current-image').val(),
		url = $this.val(), target = ( $('#img-link-new').is(":checked") ) ? '_blank' : '_self';
		
		if ( $('#image-link-type').val() == 'url' ) {
			if ( $('#' + imgID).find('a.ib2-img-link').length ) {
				$('#' + imgID).find('a.ib2-img-link').attr('href', url);
				$('#' + imgID).find('a.ib2-img-link').attr('target', target);
				$('#' + imgID).find('a.ib2-img-link').removeClass('ib2-open-popup');
			} else {
				$('#' + imgID).find('img').wrap('<a href="' + url + '" class="ib2-img-link" target="' + target + '"></a>');
			}
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#image-link-type').change(function(){
		var opt = $("option:selected", this).val(),
		elID = $('#ib2-current-image').val();
		
		if ( opt == 'popup' ) {
			if ( $('#' + elID).find('a.ib2-img-link').length ) {
				$('#' + elID).find('a.ib2-img-link').attr('href', '#' + elID + '-popup');
				$('#' + elID).find('a.ib2-img-link').addClass('ib2-open-popup');
			} else
				$('#' + elID).find('img').wrap('<a href="#' + elID + '-popup" class="ib2-img-link ib2-open-popup"></a>');
			
			$('.img-url-target-group').hide();
			$('.img-popup-target-group').show();
		} else if ( opt == 'url' ) {
			if ( $('#' + elID).find('a.ib2-img-link').length ) {
				$('#' + elID).find('a.ib2-img-link').attr('href', $("#img-link-url").val());
				$('#' + elID).find('a.ib2-img-link').removeClass('ib2-open-popup');
			} else
				$('#' + elID).find('img').wrap('<a href="' + $("#img-link-url").val() + '" class="ib2-img-link"></a>');
			
			if ( $('#img-link-new').is(":checked") )
				$('#' + elID).find('a.ib2-img-link').attr('target', '_blank');
			else
				$('#' + elID).find('a.ib2-img-link').attr('target', '_self');
				
			$('.img-url-target-group').show();
			$('.img-popup-target-group').hide();
		} else {
			if ( $('#' + elID).find('a.ib2-img-link').length )
				$('#' + elID).find('img').unwrap();
				
			$('.img-url-target-group').hide();
			$('.img-popup-target-group').hide();
		}
		
		$('#' + elID).data('target', opt);
		$('#' + elID).attr('data-target', opt);
	});
	
	// Pretty Checkbox for image aspect ratio...
	$('#img-aspect-ratio').prettyCheckable();
	$('#img-aspect-ratio').on('change', function(){
  		var elID = $('#ib2-current-image').val();
  		if ( $(this).is(":checked") ) {
  			$('#' + elID).attr('data-aspect-ratio', 'yes');
  			$('#' + elID).data('aspectRatio', 'yes');
  			
  			$('#' + elID + ' > .el-content').css('height', 'auto');
  			$('#' + elID + ' > .el-content > img').css('height', 'auto');
  		} else {
  			$('#' + elID).attr('data-aspect-ratio', 'no');
  			$('#' + elID).data('aspectRatio', 'no');
  		}
	  		
	  	
  		if ( $('#' + elID + ' > .el-content').resizable("instance") )
			$('#' + elID + ' > .el-content').resizable("destroy");
 	});
 	
	// Pretty Checkbox for image link target...
	$('#img-link-new').prettyCheckable();
	
	// Image link target...
  	$('#img-link-new').on('change', function(){
  		var elID = $('#ib2-current-image').val();
  		if ( $('#image-link-type').val() == 'url' && $('#' + elID).find('a.ib2-img-link').length ) {
  			if ( $(this).is(":checked") ) {
	  			$('#' + elID).find('a.ib2-img-link').attr('target', '_blank');
	  		} else {
	  			$('#' + elID).find('a.ib2-img-link').attr('target', '_self');
	  		}
	  		
	  		$("body").trigger("ib2CommonChange");
  		}
  	});

  	removeImgEl = $('.remove-img-el').click(function(e){
  		var elID = $('#ib2-current-image').val();
  		$('#' + elID).find('img').attr('src', $('#ib2-img-src').attr('src'));
  		$('#image-el-url').val('');
  		$('#image-el-prev').html('');
  		
  		var w = $('#' + elID).find('img').outerWidth(),
  			h = $('#' + elID).find('img').outerHeight();
  			
  		$('#' + elID).find('.el-content').css({
			'width': w + 'px',
			'height': h + 'px',
		});
				
  		$(this).parent().hide();
  		
  		$("body").trigger("ib2CommonChange");
  		e.preventDefault();
  	});
  	
  	$('#image-el-url').bind('keyup blur', function(){
  		var elID = $('#ib2-current-image').val(),
  		image = $(this).val();
  		
  		if ( image != '' ) {
  			$('#' + elID).find('img').attr('src', image);
  		
  			ib2_image(elID, image);
  		
  			var imgel = $('#' + elID).find('img'),
  			imgid = imgel.attr('id'), newimg = document.getElementById(imgid),
  			w = newimg.naturalWidth,
  			h = newimg.naturalHeight;
  			
			$('#' + elID).find('.el-content').css({
				'width': w + 'px',
				'height': 'auto',
			});
			
			$('#' + elID).find('.el-content > img').css({
				'width': w + 'px',
				'height': 'auto',
			});
			
			//$('#cur-img-width').val(w);
			//$('#cur-img-height').val(h);
		} else {
			removeImgEl.click();
		}
		
		$("body").trigger("ib2CommonChange");
  	});
	
	/*
	$('#cur-img-width').bind('blur keyup', function(){
		var elID = $('#ib2-current-image').val(),
		img = $('#' + elID).find('img'),
		origWidth = img.width(),
		origHeight = img.height(),
		newValue = $(this).val();
		
		if ( !isNumber(newValue) ) {
			alert("Please enter only number.");
			$(this).val(value).focus();
			return false;
		}
		
		$('#' + elID).find('.el-content').css('width', newValue + 'px');
		$('#' + elID).find('img').css('width', newValue + 'px');
		if ( $('#img-aspect-ratio').is(":checked") ) {
			var newHeight = Math.ceil(newValue * origHeight / origWidth);
			$('#' + elID).find('.el-content').css('height', newHeight + 'px');
			$('#' + elID).find('img').css('height', newHeight + 'px');
			
			$('#cur-img-height').val(newHeight);
		}
	});
	
	$('#cur-img-height').bind('blur keyup', function(){
		var elID = $('#ib2-current-image').val(),
		img = $('#' + elID).find('img'),
		origWidth = img.width(),
		origHeight = img.height(),
		newValue = $(this).val();
		
		if ( !isNumber(newValue) ) {
			alert("Please enter only number.");
			$(this).val(value).focus();
			return false;
		}
		
		$('#' + elID).find('.el-content').css('height', newValue + 'px');
		$('#' + elID).find('img').css('height', newValue + 'px');
		if ( $('#img-aspect-ratio').is(":checked") ) {
			var newWidth = Math.ceil(newValue * origHeight / origWidth);
			$('#' + elID).find('.el-content').css('width', newWidth + 'px');
			$('#' + elID).find('img').css('width', newWidth + 'px');
			
			$('#cur-img-width').val(newWidth);
		}
	});
	*/
	
  	// ============================== GENERATOR ==============================
  	$('body').on('click', '#generate-content', function(e){
  		var $this = $(this), elID = $('#ib2-current-text').val(),
  		type = $('#gen-content-type').val(),
  		bn = $('#gen-business-name').val(),
  		be = $('#gen-business-email').val(),
  		ba = $('#gen-business-addr').val(),
  		bc = $('#gen-business-country').val();
  		
  		$this.button('loading');
  		$('.generate-close-button').attr('disabled', 'disabled');
  		
  		if ( bn == '' ) {
  			$this.button('reset');
  			$('#gen-business-name').focus();
  			$('.generate-close-button').removeAttr('disabled');
  			alert('ERROR: Please enter your business/company name.');
  			return false;
  		}
  		
  		if ( type == 'policy' && be == '' ) {
  			$this.button('reset');
  			$('#gen-business-email').focus();
  			$('.generate-close-button').removeAttr('disabled');
  			alert('ERROR: Please enter a valid email address.');
  			return false;
  		}
  		
  		if ( type == 'policy' || type == 'tos' ) {
	  		if ( ba == '' ) {
	  			$('#gen-business-addr').focus();
	  			$this.button('reset');
	  			$('.generate-close-button').removeAttr('disabled');
	  			alert('ERROR: Please enter your business/company address.');
	  			return false;
	  		}
  		}
  		
  		var data = {
  			action: 'ib2_generate_legal',
  			ctype: type,
  			b_name: bn,
  			b_email: be,
  			b_addr: ba,
  			b_country: bc
  		};

  		$.post(ajaxurl, data, function( response ) {
  			$this.button('reset');
	  		$('.generate-close-button').removeAttr('disabled');
  			if ( response.success ) {
  				var content = response.content;
  				$('#' + elID).html(content);
  				
  				$('#ib2-generator-modal').modal('hide');
  			}
  		});
  		$("body").trigger("ib2CommonChange");
  		e.preventDefault();
  	});
  	
  	$('#screen-container').on('click', '.ib2-generate-btn', function(e){
  		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = $('#' + elID);
		
		$('#ib2-current-text').val(elID);
		$('#gen-content-type').val('disclaimer');
		$('.tos-only-field').hide();
		$('.policy-only-field').hide();
		
		$('#ib2-generator-modal').modal({
			keyboard: false,
			backdrop: 'static',
			show: true
		});

  		e.preventDefault();
  	});
  	
  	$('body').on('change', '#gen-content-type', function(){
  		var opt = $("option:selected", this).val();
  		
  		if ( opt == 'tos' ) {
  			$('.policy-only-field').hide();
  			$('.tos-only-field').show();
  		} else if ( opt == 'policy' ) {
  			$('.tos-only-field').hide();
  			$('.policy-only-field').show();
  		} else {
  			$('.tos-only-field').hide();
  			$('.policy-only-field').hide();
  		}
  	});
  	
  	// ============================== BUTTON ==============================
  	
  	// Button Icon
  	$('body').on('click', '.ib2-button-icon', function(e){
  		var $this = $(this), buttonID = $('#ib2-current-button').val(),
		el = ( $('#ib2-button-mode').val() == 'normal' ) ? $('#' + buttonID).find('a.ib2-button') : $('#' + buttonID);
		
		e.preventDefault();
		
		if ( $this.hasClass('ib2-no-icon') ) {
			el.find('i').remove();
			return false;
		}
		
		$this.parent().find('.selected').removeClass('selected');
		$this.addClass('selected');
		var icon = $this.data('icon');
		
		if ( !el.find('i').length ) {
			if ( $('#button-icon-position').val() == 'before' )
				el.prepend('<i></i>');
			else
				el.append('<i></i>');
		}
		
		el.find('i').attr('class', 'fa ' + icon);
		$("body").trigger("ib2CommonChange");
  	});
  	
  	// Button Icon Position
  	$('#button-icon-position').change(function(){
  		var opt = $("option:selected", this).val(), buttonID = $('#ib2-current-button').val(),
		el = ( $('#ib2-button-mode').val() == 'normal' ) ? $('#' + buttonID).find('a.ib2-button') : $('#' + buttonID);
		
		if ( !el.find('i').length ) {
			return false;
		}
		
		var iClass = el.find('i').attr('class');
		el.find('i').remove();
		
		if ( opt == 'before' ) {
			el.prepend('<i class="' + iClass + '"></i>');
		} else {
			el.append('<i class="' + iClass + '"></i>');
		}
		$("body").trigger("ib2CommonChange");
  	});
  	
  	
  	// Button Font
	$('#button-text-font').change(function(){
		var buttonID = $('#ib2-current-button').val(),
		el = ( $('#ib2-button-mode').val() == 'normal' ) ? $('#' + buttonID).find('a.ib2-button') : $('#' + buttonID),
		font = $("option:selected", this).val();
	    	
	    el.css('font-family', font);
	    
	    $("body").trigger("ib2CommonChange");
	});
	
	// Button Text Bold
	$('#button-style-bold').on('change', function(){
		var buttonID = $('#ib2-current-button').val(),
		el = ( $('#ib2-button-mode').val() == 'normal' ) ? $('#' + buttonID).find('a.ib2-button') : $('#' + buttonID);
		
		if ( $(this).is(":checked") ) {
			el.css('font-weight', 'bold');
		} else {
			el.css('font-weight', 'normal');
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	// Button Text Italic
	$('#button-style-italic').on('change', function(){
		var buttonID = $('#ib2-current-button').val(),
		el = ( $('#ib2-button-mode').val() == 'normal' ) ? $('#' + buttonID).find('a.ib2-button') : $('#' + buttonID);
		
		if ( $(this).is(":checked") ) {
			el.css('font-style', 'italic');
		} else {
			el.css('font-style', 'normal');
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	// Button Text Underline
	$('#button-style-underline').on('change', function(){
		var buttonID = $('#ib2-current-button').val(),
		el = ( $('#ib2-button-mode').val() == 'normal' ) ? $('#' + buttonID).find('a.ib2-button') : $('#' + buttonID);
		
		if ( $(this).is(":checked") ) {
			el.css('text-decoration', 'underline');
		} else {
			el.css('text-decoration', 'none');
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
  	// Button Style
	$('#button-style').change(function(){
		var buttonID = $('#ib2-current-button').val(),
		el = ( $('#ib2-button-mode').val() == 'normal' ) ? $('#' + buttonID).find('a.ib2-button') : $('#' + buttonID),
		type = $("option:selected", this).val(),
		buttonColor = $('#button-color').iris("color"),
	    borderColor = getAltColor(buttonColor, 'darker');
	    	
	    $('#' + buttonID).data('buttonType', type);
	    $('#' + buttonID).attr('data-button-type', type);
	    
    	if ( type == 'flat' ) {
    		el.css({
	        	'background-color': buttonColor,
	        	'border-color': borderColor,
	        	'background-image': 'none'
	        });
    	} else if ( type == 'glossy' ) {
    		var glossyColor = getAltColor(buttonColor, 'lighter');
    		el.css({
	        	'background-color': buttonColor,
	        	'background-image': 'linear-gradient(center bottom , ' + buttonColor + ' 0%, ' + glossyColor + ')',
	        	'background-image': '-moz-linear-gradient(center bottom , ' + buttonColor + ' 0%, ' + glossyColor + ')',
	        	'border-color': borderColor
	        });	
    	}
    	
    	// Style for hover button
    	var selector = ( $('#ib2-button-mode').val() == 'normal' ) ? '#' + buttonID + ' > .el-content > a.ib2-button' : '#' + buttonID,
		buttonHoverColor = $('#button-hover-color').iris("color"),
	    borderHoverColor = getAltColor(buttonHoverColor, 'darker');
	    
	    var css = '' + selector + ':hover, ' + selector + ':active {';
    	if ( type == 'flat' ) {
	        css += 'color: ' + $('#button-text-hover-color').val() + ' !important; text-shadow: 1px 1px 0 ' + $('#button-tshadow-hover-color').val() + ' !important; background-color:' + buttonHoverColor + ' !important; border-color:' + borderHoverColor + ' !important; background-image: none !important; }';
    	} else if ( type == 'glossy' ) {
    		var glossyHoverColor = getAltColor(buttonHoverColor, 'lighter');
    		css += 'color: ' + $('#button-text-hover-color').val() + ' !important; text-shadow: 1px 1px 0 ' + $('#button-tshadow-hover-color').val() + ' !important; background-color:' + buttonHoverColor + ' !important; border-color:' + borderHoverColor + ' !important; ';
	        css += 'background-image: linear-gradient(center bottom , ' + buttonHoverColor + ' 0%, ' + glossyHoverColor + ') !important; ';
	        css += 'background-image: -moz-linear-gradient(center bottom , ' + buttonHoverColor + ' 0%, ' + glossyHoverColor + ') !important; ';
			css += '}';
    	}
    	
    	$('#' + buttonID + '-css').html(css);
    	
    	$("body").trigger("ib2CommonChange");
	});

  	// Button Text Label
	$("#button-text").bind("keyup", function(e){
		var $this = $(this), btnID = $('#ib2-current-button').val(),
		el = ( $('#ib2-button-mode').val() == 'normal' ) ? $('#' + btnID).find('a.ib2-button') : $('#' + btnID),
		text = $this.val();
		
		if ( text == '' ) {
			text = 'Click Here';
			$this.val(text);
			$this.focus();	
		}
		
		el.text(text);
		
		$("body").trigger("ib2CommonChange");
	});
	
  	// Button Link
	$("#button-link-url").bind("keyup blur paste", function(e){
		var $this = $(this), btnID = $('#ib2-current-button').val(),
		url = $this.val(), target = ( $('#button-link-new').is(":checked") ) ? '_blank' : '_self';
		
		if ( url != '' ) {
			$('#' + btnID).find('a.ib2-button').attr('href', url);
			$('#' + btnID).find('a.ib2-button').attr('target', target);
		} else {
			$('#' + btnID).find('a.ib2-button').attr('href', '');
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
  	// Pretty Checkbox for button link target...
	$('#button-link-new').prettyCheckable();
	
	// Button link target...
  	$('#button-link-new').on('change', function(){
  		var elID = $('#ib2-current-button').val();
  		if ( $('#' + elID).find('a.ib2-button').length ) {
  			if ( $(this).is(":checked") ) {
	  			$('#' + elID).find('a.ib2-button').attr('target', '_blank');
	  		} else {
	  			$('#' + elID).find('a.ib2-button').attr('target', '_self');
	  		}
  		}
  		
  		$("body").trigger("ib2CommonChange");
  	});
  	
  	$('#button-animation').change(function(){
  		var elID = $('#ib2-current-button').val(),
  		opt = $("option:selected", this).val();
  		
  		if ( opt == 'none' ) {
  			$('#' + elID).removeClass('ib2-text-animation');
  			$('#' + elID).attr('data-animation', 'none');
  			$('#' + elID).data('animation', 'none');
  		} else {
  			$('#' + elID).addClass('ib2-text-animation');
  			$('#' + elID).attr('data-animation', opt);
  			$('#' + elID).data('animation', opt);
  		}
  		
  		$("body").trigger("ib2CommonChange");
  	});
  	
  	// ============================== TEXT ==============================
  	$('#text-el-shadow').prettyCheckable();
  	
  	$('body').on('change', '#text-el-shadow', function(){
  		var $this = $(this), elID = $('#ib2-current-text').val();
  		
  		if ( $this.is(":checked") ) {
  			var color = $('#text-el-shadow-color').val(),
  			blur = $('#text-el-shadow-blur').val();
  			$('#' + elID).css({
  				'text-shadow': '1px 1px ' + blur + 'px ' + color
  			});
  			
  			$('.text-el-shadow-group').show();
  			
  			$('#' + elID).data('shadow', 'yes');
  			$('#' + elID).attr('data-shadow', 'yes');
  		} else {
  			$('#' + elID).css({
  				'text-shadow': 'none'
  			});
  			$('.text-el-shadow-group').hide();
  			
  			$('#' + elID).data('shadow', 'none');
  			$('#' + elID).attr('data-shadow', 'none');
  		}
  		
  		$("body").trigger("ib2CommonChange");
  	});
  	
  	$('#text-animation').change(function(){
  		var elID = $('#ib2-current-text').val(),
  		opt = $("option:selected", this).val();
  		
  		if ( opt == 'none' ) {
  			$('#' + elID).removeClass('ib2-text-animation');
  			$('#' + elID).data('animation', 'none');
  			$('#' + elID).attr('data-animation', 'none');
  		} else {
  			$('#' + elID).addClass('ib2-text-animation');
  			$('#' + elID).data('animation', opt);
  			$('#' + elID).attr('data-animation', opt);
  		}
  		
  		$("body").trigger("ib2CommonChange");
  	});
  	
  	// ============================== BOX ==============================
  	
  	$('#enable_box_delay').prettyCheckable();
  	$('body').on('change', '#enable_box_delay', function(){
  		var elID = $('#ib2-current-box').val();
		
  		if ( $(this).is(":checked") ) {
  			$('.delay-group').show();
  			$('#' + elID).addClass('ib2-box-delay');
  			
  			var hour = $('#box-delay-hour').val(),
  			min = $('#box-delay-min').val(),
  			secs = $('#box-delay-secs').val();
  			
  			$('#' + elID).data('delay', hour + ':' + min + ':' + secs);
  			$('#' + elID).attr('data-delay', hour + ':' + min + ':' + secs);
  		} else {
  			$('.delay-group').hide();
  			$('#' + elID).removeClass('ib2-box-delay');
  			$('#' + elID).data('delay', 'none');
  			$('#' + elID).attr('data-delay', 'none');
  		}
  		
  		$("body").trigger("ib2CommonChange");
  	});
  	
  	$('#box-animation').change(function(){
  		var elID = $('#ib2-current-box').val(),
  		opt = $("option:selected", this).val();
  		
  		if ( opt == 'none' ) {
  			$('#' + elID).removeClass('ib2-box-animation');
  			$('#' + elID).data('animation', 'none');
  			$('#' + elID).attr('data-animation', 'none');
  		} else {
  			$('#' + elID).addClass('ib2-box-animation');
  			$('#' + elID).data('animation', opt);
  			$('#' + elID).attr('data-animation', opt);
  		}
  		
  		$("body").trigger("ib2CommonChange");
  	});
  	
  	// Box Shadow Type
	$('#box-shadow-type').change(function(){
		var elID = $('#ib2-current-box').val(),
		el = ( $('#' + elID).data('el') != 'box' ) ? $('#' + elID + ' > .el-content') : $('#' + elID + '-box'),
		opt = $("option:selected", this).val();
        if ( opt == 'none' ) {
        	el.css({
        		'box-shadow': 'none',
        		'-webkit-box-shadow': 'none'
        	});
        } else {
        	var shadow = '';
        	if ( opt == 'inset' ) shadow += 'inset ';
        	shadow += $('#box-hshadow').val() + 'px ';
        	shadow += $('#box-vshadow').val() + 'px ';
        	shadow += ( opt == 'inset' ) ? '10px 4px ' : '8px 3px ';
        	shadow += $('#box-shadow-color').iris("color");
        	
        	el.css({
        		'box-shadow': shadow,
        		'-webkit-box-shadow': shadow
        	});
        }
        
        $("body").trigger("ib2CommonChange");
	});
	
	// Pretty Checkbox for box individual border settings... 
	$('#indiv-box-border').prettyCheckable();
	
	// Check if the box pretty checkbox has changed...
  	$('#indiv-box-border').on('change', function(){
  		var elID = $('#ib2-current-box').val();
  		if ( $(this).is(":checked") ) {
  			$('#border-single-set').hide();
  			$('#border-multi-set').show();
  			
  			$('#' + elID).data('borderType', 'multi');
  			$('#' + elID).attr('data-border-type', 'multi');
  			
  			// Set Values
  			$('#box-left-border-type,#box-right-border-type,#box-top-border-type,#box-bottom-border-type')
  				.val($('#box-border-type').val());
  			$('#box-left-border-color,#box-right-border-color,#box-top-border-color,#box-bottom-border-color')
  				.iris("color", $('#box-border-color').iris("color"));
  			$('#box-left-border-thick-slider,#box-right-border-thick-slider,#box-top-border-thick-slider,#box-bottom-border-thick-slider')
  				.slider("value", $('#box-border-thick-slider').slider("value"));
  		} else {
  			$('#border-single-set').show();
  			$('#border-multi-set').hide();
  			
  			$('#' + elID).data('borderType', 'single');
  			$('#' + elID).attr('data-border-type', 'single');
  			
  			// Set Values
  			$('#box-border-type')
  				.val($('#box-bottom-border-type').val());
  			$('#box-border-color')
  				.iris("color", $('#box-bottom-border-color').iris("color"));
  			$('#box-border-thick-slider')
  				.slider("value", $('#box-bottom-border-thick-slider').slider("value"));
  				
  			var el = ( $('#' + elID).data('el') != 'box' ) ? $('#' + elID + ' > .el-content') : $('#' + elID + '-box');
  			el.css({
  				'border-width': $('#box-border-thick-slider').slider("value") + 'px',
  				'border-color': $('#box-border-color').iris("color"),
  				'border-style': $('#box-border-type').val(),
  			});
  		}
  		
  		$("body").trigger("ib2CommonChange");
  	});
  	
  	// Box Border Type
	$('#box-border-type').change(function(){
		var boxID = $('#ib2-current-box').val(),
		el = ( $('#' + boxID).data('el') != 'box' ) ? $('#' + boxID + ' > .el-content') : $('#' + boxID + '-box'),
		opt = $("option:selected", this).val();
		
        el.css({
        	'border-style': opt,
        });
        
        $("body").trigger("ib2CommonChange");
	});
	
	// Box Left Border Type
	$('#box-left-border-type').change(function(){
		var boxID = $('#ib2-current-box').val(),
		el = ( $('#' + boxID).data('el') != 'box' ) ? $('#' + boxID + ' > .el-content') : $('#' + boxID + '-box'),
		opt = $("option:selected", this).val();
        el.css({
        	'border-left-style': opt,
        });
        
        $("body").trigger("ib2CommonChange");
	});
	
	// Box Right Border Type
	$('#box-right-border-type').change(function(){
		var boxID = $('#ib2-current-box').val(),
		el = ( $('#' + boxID).data('el') != 'box' ) ? $('#' + boxID + ' > .el-content') : $('#' + boxID + '-box'),
		opt = $("option:selected", this).val();
        el.css({
        	'border-right-style': opt,
        });
        
        $("body").trigger("ib2CommonChange");
	});
	
	// Box Top Border Type
	$('#box-top-border-type').change(function(){
		var boxID = $('#ib2-current-box').val(),
		el = ( $('#' + boxID).data('el') != 'box' ) ? $('#' + boxID + ' > .el-content') : $('#' + boxID + '-box'),
		opt = $("option:selected", this).val();
        el.css({
        	'border-top-style': opt,
        });
        
        $("body").trigger("ib2CommonChange");
	});
	
	// Box Bottom Border Type
	$('#box-bottom-border-type').change(function(){
		var boxID = $('#ib2-current-box').val(),
		el = ( $('#' + boxID).data('el') != 'box' ) ? $('#' + boxID + ' > .el-content') : $('#' + boxID + '-box'),
		opt = $("option:selected", this).val();
        el.css({
        	'border-bottom-style': opt,
        });
        
        $("body").trigger("ib2CommonChange");
	});
	
	// Pretty Checkbox for Content Area transparent background...
	$('#box-transparent').prettyCheckable();
	
  	boxTrans = $('#box-transparent').on('change', function(){
  		var elID = $('#ib2-current-box').val(),
		el = ( $('#' + elID).data('el') != 'box' ) ? $('#' + elID + ' > .el-content') : $('#' + elID + '-box');
		if ( $(this).is(":checked") ) {
			$('.box-background-form').hide();
  			el.css('background-color', 'transparent');
  		} else {
  			$('.box-background-form').show();
  			el.css('background-color', $('#box-color').iris("color"));
  			$('#box-color-glossy').trigger('change');
  		}
  		
  		$("body").trigger("ib2CommonChange");
  	});
  	
  	// Glossy Effect
  	$('#box-color-glossy').prettyCheckable();
  	$('#box-color-glossy').on('change', function(){
  		var elID = $('#ib2-current-box').val(),
		el = ( $('#' + elID).data('el') == 'box' ) ? $('#' + elID + '-box') : $('#' + elID + ' > .el-content');
		if ( $(this).is(":checked") ) {
			var color = $('#box-color').iris("color"),
			lightColor = getAltColor(color, 'lighter'),
			glossyColor = getAltColor(lightColor, 'lighter');
			
			$('#' + elID).attr('data-glossy', 'yes');
  			$('#' + elID).data('glossy', 'yes');
  			
  			el.css({
  				'background-color': color,
  				'background-image': 'linear-gradient(to bottom, ' + glossyColor + ' 5%, ' + color + ' 100%)',
  				'background-image': '-moz-linear-gradient(to bottom, ' + glossyColor + ' 5%, ' + color + ' 100%)'
  			});
  		} else {
			$('#' + elID).attr('data-glossy', 'no');
  			$('#' + elID).data('glossy', 'no');
  			
  			el.css('background-color', $('#box-color').iris("color"));
  			
  			var bgImg = el.css('backgroundImage');
  			if ( bgImg.indexOf("linear-gradient") >= 0 ) {
  				el.css('background-image', 'none');
  			}
  		}
  		
  		$("body").trigger("ib2CommonChange");
  		
  	});
  	
  	// Box Background Image
	$('#box-image-upload').click(function(e){
		var elID = $('#ib2-current-box').val(),
		type = ( $('#' + elID).data('el') == 'box' ) ? 'box' : 'section';
		ib2media(elID, type);
		
		$("body").trigger("ib2CommonChange");
		e.preventDefault();
	});
	
	$('#box-bgrepeat').on('change', function(){
		var option = $("option:selected", this).val(),
		elID = $('#ib2-current-box').val(),
		el = ( $('#' + elID).data('el') != 'box' ) ? $('#' + elID + ' > .el-content') : $('#' + elID + '-box');
		el.css('background-repeat', option);
		
		$("body").trigger("ib2CommonChange");
	});
  		
  	$('#box-bgpos').on('change', function(){
		var option = $("option:selected", this).val(),
		elID = $('#ib2-current-box').val(),
		el = ( $('#' + elID).data('el') != 'box' ) ? $('#' + elID + ' > .el-content') : $('#' + elID + '-box');
		el.css('background-position', option);
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#box-bgattach').on('change', function(){
		var option = $("option:selected", this).val(),
		elID = $('#ib2-current-box').val(),
		el = ( $('#' + elID).data('el') != 'box' ) ? $('#' + elID + ' > .el-content') : $('#' + elID + '-box');
		el.css('background-attachment', option);
		
		$("body").trigger("ib2CommonChange");
	});
	
	// Remove Box Background Image
	removeBoxImg = $('.remove-box-img').click(function(e){
		var elID = $('#ib2-current-box').val(),
		el = ( $('#' + elID).data('el') != 'box' ) ? $('#' + elID + ' > .el-content') : $('#' + elID + '-box');
		
  		el.css('background-image', 'none');
  		$('#box-image-prev').find('img').remove();
		$('#box-bg-url').val('');
  		$(this).parent().hide();
  		
  		$("body").trigger("ib2CommonChange");
  		e.preventDefault();
  	});
	
	$('#box-bg-url').bind('keyup blur', function(){
		var image = $(this).val(), elID = $('#ib2-current-box').val();
		if ( image != '' ) {
			if (  $('#' + elID).data('el') == 'box' )
				ib2_box_background(elID, image);
			else
				ib2_section_background(elID, image);
		} else {
			removeBoxImg.click();
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	// ============================== DIVIDER ==============================
	
	// Horizontal Line Type
	$('#hline-type').change(function(){
		var boxID = $('#ib2-current-hline').val(),
		el = $('#' + boxID).find('.ib2-hline'), opt = $("option:selected", this).val();
        el.css({
        	'border-top-style': opt,
        });
        
        $("body").trigger("ib2CommonChange");
	});

	// ============================== VIDEO ==============================
	
	// Video Autoplay
	$('#video-autoplay').prettyCheckable();
	$('#video-autoplay').on("change", function(){
		var type = $('#video-type').val();
		ib2_video_data(type);
	});
	
	// Video No Control
	$('#video-no-control').prettyCheckable();
	$('#video-no-control').on("change", function(){
		var type = $('#video-type').val();
		ib2_video_data(type);
	});
	
	// Video Type
	vidType = $('#video-type').change(function(){
		var elID = $('#ib2-current-video').val(),
		option = $("option:selected", this).val();
		if ( option == 'hosted' ) {
			$('.youtube-property').hide();
			$('.vimeo-property').hide();
			$('.hosted-property').show();
			$('.embed-property').hide();
		} else if ( option == 'youtube' ) {
			$('.hosted-property').hide();
			$('.vimeo-property').hide();
			$('.youtube-property').show();
			$('.embed-property').hide();
		} else if ( option == 'vimeo' ) {
			$('.hosted-property').hide();
			$('.youtube-property').hide();
			$('.vimeo-property').show();
			$('.embed-property').hide();
		} else if ( option == 'embed' ) {
			$('.hosted-property').hide();
			$('.youtube-property').hide();
			$('.vimeo-property').hide();
			$('.embed-property').show();
		}
		
		$('#' + elID).attr('data-video-type', option);
		ib2_video_data(option);
	});
	
	// Update video
	$('.video-url-field').each(function(){
		$(this).bind('blur keyup', function(){
			var type = $('#video-type').val();
			ib2_video_data(type);
		});
	});

	// Upload video splash
	$('#upload-vid-splash').click(function(e){
		ib2media('video-splash', 'splash');
		e.preventDefault();	
	});
	
	// ============================== SOCIAL SHARE ==============================
	
	// Social Share Style
	$('#social-share-style').change(function(){
		var elID = $('#ib2-current-share').val(),
		option = $("option:selected", this).val(),
		el = $('#' + elID), imgFolder = $('#ib2-img-folder').val(),
		sharers = ["facebook", "twitter", "linkedin", "google"];
		
		el.find('.ib2-share-btn').each(function(i){
			var c = $(this), img = c.find('img'),
			newImg = imgFolder + '' + sharers[i] + '-share-' + option + '.png';
			img.attr('src', newImg);
		});
		
		if ( option == 'big' ) {
			$('#' + elID + ' > .el-content').css('width', '275px');
		} else if ( option == 'small' ) {
			$('#' + elID + ' > .el-content').css('width', '250px');
		} else {
			$('#' + elID + ' > .el-content').css('width', '375px');
		}
		
		el.data('mode', option);
		el.attr('data-mode', option);
		
		$("body").trigger("ib2CommonChange");
	});
	
	// Social share PrettyCheckable
	$('.ib2-share-chkbox').each(function(){
		var p = $(this),
		elID = $('#ib2-current-share').val(),
		el = $('#' + elID);
		
		// Init
		p.prettyCheckable();
	});
	
	$('body').on('change', '.ib2-share-chkbox', function(){
		var elID = $('#ib2-current-share').val(),
		el = $('#' + elID), t = $(this), social = t.val();
		
		if ( t.is(":checked") ) {
			el.find('.ib2-' + social + '-share').show();
		} else {
			el.find('.ib2-' + social + '-share').hide();
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#share-custom-url').bind('keyup blur', function(){
		var url = $(this).val(), elID = $('#ib2-current-share').val();
		
		$('#' + elID).attr('data-custom-url', url);
		$('#' + elID).data('customUrl', url);
	});
	
	// ============================== CODE ==============================
	
	// Code Element
	$('#code-content').bind('keyup blur', function(){
		var elID = $('#ib2-current-code').val();
		$('#' + elID + ' > .el-content').html($(this).val());
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#shortcode-content').bind('keyup blur', function(){
		var elID = $('#ib2-current-code').val();
		$('#' + elID).html($(this).val());
		
		$("body").trigger("ib2CommonChange");
	});
	
	// =========================== OPTIN =======================
	removeOptinImg = $('.remove-button-img').click(function(e){
		var $this = $(this), elID = $('#ib2-current-optin').val();
		
		$('#' + elID + '-image').attr('src', '');
		$('#button-image-prev').html('');
		$('#image-button-url').val('');
		$this.parent().hide();
		
		$("body").trigger("ib2CommonChange");
		e.preventDefault();
	});
	
	$('#optin-form-mode').change(function(){
		var $this = $(this), elID = $('#ib2-current-optin').val(),
		opt = $("option:selected", this).val();
		
		if ( opt == 'horizontal' ) {
			$('#' + elID).find('form').addClass('form-inline');
			//$('#' + elID).find('.form-fields').css('display', 'inline-block');
			$('#' + elID).find('.form-fields').removeClass('col-md-12').addClass('col-md-8');
			$('#' + elID).find('.button-fields').removeClass('col-md-12 button-container').addClass('col-md-4');
			$('#' + elID + ' > .el-content').css('width', '625px');
		} else if ( opt == 'semi-horizontal' ) {
			$('#' + elID).find('form').addClass('form-inline');
			//$('#' + elID).find('.form-fields').css('display', 'inline-block');
			$('#' + elID).find('.form-fields').removeClass('col-md-8').addClass('col-md-12');
			$('#' + elID).find('.button-fields').removeClass('col-md-4').addClass('button-container col-md-12');
			$('#' + elID + ' > .el-content').css('width', '446px');
		} else {
			$('#' + elID).find('form').removeClass('form-inline');
			//$('#' + elID).find('.form-fields').css('display', 'block');
			$('#' + elID).find('.form-fields').removeClass('col-md-8').addClass('col-md-12');
			$('#' + elID).find('.button-fields').removeClass('col-md-4 button-container').addClass('col-md-12');
			$('#' + elID + ' > .el-content').css('width', '300px');
		}
		
		$('#' + elID).data('formMode', opt);
		$('#' + elID).attr('data-form-mode', opt);
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#image-button-url').bind('keyup blur', function(){
		var image = $(this).val(), elID = $('#ib2-current-optin').val();
		
		if ( image != '' ) {
			if ( image.match(/\.(jpeg|jpg|gif|png|bmp)$/) != null ) {
				ib2_optin_image(elID, image);
			}
		} else {
			removeOptinImg.click();
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#button-image-upload').click(function(e){
		var elID = $('#ib2-current-optin').val();
		ib2media(elID, 'optin_button');
		
		$("body").trigger("ib2CommonChange");
		e.preventDefault();
	});
	
	$('#optin-button-type').change(function(){
		var opt = $("option:selected", this).val(),
		elID = $('#ib2-current-optin').val();
		
		if ( opt == 'css' ) {
			$('#' + elID).find('#' + elID + '-submit').show();
			$('#' + elID).find('#' + elID + '-image').hide();
			$('.css-button-group').show();
			$('.image-button-group').hide();
		} else {
			$('#' + elID).find('#' + elID + '-submit').hide();
			$('#' + elID).find('#' + elID + '-image').show();
			$('.css-button-group').hide();
			$('.image-button-group').show();
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#field-size').change(function(){
		var opt = $("option:selected", this).val(),
		elID = $('#ib2-current-optin').val();
		
		if ( opt == 'big' ) {
			$('#' + elID).find('input[type=text], select, textarea').removeClass('input-sm input-ln').addClass('input-lg');
			$('#' + elID).find('.ib2-form-submit').removeClass('submit-sm').addClass('submit-lg');
		} else if ( opt == 'small' ) {
			$('#' + elID).find('input[type=text], select, textarea').removeClass('input-lg input-ln').addClass('input-sm');
			$('#' + elID).find('.ib2-form-submit').removeClass('submit-lg').addClass('submit-sm');
		} else if ( opt == 'smalllong' ) {
			$('#' + elID).find('input[type=text], select, textarea').removeClass('input-lg').addClass('input-sm input-ln');
			$('#' + elID).find('.ib2-form-submit').removeClass('submit-lg').addClass('submit-sm');
		} else if ( opt == 'normallong' ) {
			$('#' + elID).find('input[type=text], select, textarea').removeClass('input-lg input-sm').addClass('input-ln');
			$('#' + elID).find('.ib2-form-submit').removeClass('submit-sm submit-lg');
		} else if ( opt == 'biglong' ) {
			$('#' + elID).find('input[type=text], select, textarea').removeClass('input-sm').addClass('input-lg input-ln');
			$('#' + elID).find('.ib2-form-submit').removeClass('submit-sm').addClass('submit-lg');
		} else {
			$('#' + elID).find('input[type=text], select, textarea').removeClass('input-sm input-lg input-ln');
			$('#' + elID).find('.ib2-form-submit').removeClass('submit-sm submit-lg');
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#field-style').change(function(){
		var opt = $("option:selected", this).val(),
		elID = $('#ib2-current-optin').val();
		
		$('#' + elID).find('input[type=text], select, textarea')
			.removeClass('field-normal field-normal-thick field-sharp field-sharp-thick')
			.addClass(opt);

		$("body").trigger("ib2CommonChange");
	});
	
	$('#facebook-opt-enable').prettyCheckable();
	
	$('body').on('change', '#facebook-opt-enable', function(){
		var $this = $(this), elID = $('#ib2-current-optin').val();
		if ( $this.is(":checked") ) {
			$('#' + elID).find('.ib2-facebook-optin').show();
			$('.facebook-opt-group').show();
		} else {
			$('#' + elID).find('.ib2-facebook-optin').hide();
			$('.facebook-opt-group').hide();
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#facebook-opt-only').prettyCheckable();
	
	$('body').on('change', '#facebook-opt-only', function(){
		var $this = $(this), elID = $('#ib2-current-optin').val();
		if ( $this.is(":checked") ) {
			$('#' + elID).find('.form-fields').hide();
			$('#' + elID).find('.ib2-form-submit').hide();
			$('#' + elID).find('.form-control').each(function(i){
				if ( $(this).attr('type') == 'text' ) {
					$(this).attr('type', 'hidden');
				}
			});
		} else {
			$('#' + elID).find('.form-fields').show();
			$('#' + elID).find('.ib2-form-submit').show();
			$('#' + elID).find('.form-control').each(function(i){
				if ( $(this).attr('type') == 'hidden' ) {
					$(this).attr('type', 'text');
				}
			});
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#facebook-optin-label').bind("keyup blur", function(){
		var $this = $(this), text = $this.val(), elID = $('#ib2-current-optin').val();
		$('#' + elID + '-fb').find('.ib2-fb-button').text(text);
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#facebook-optin-font').change(function(){
		var $this = $(this), opt = $("option:selected", this).val(),
		elID = $('#ib2-current-optin').val();
		$('#' + elID + '-fb').find('.ib2-facebook-optin-txt').css('font-family', opt);
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#facebook-optin-text').bind("keyup blur", function(){
		var $this = $(this), text = $this.val(), elID = $('#ib2-current-optin').val();
		if ( text == '' ) {
			$('#' + elID + '-fb').find('.ib2-facebook-optin-txt').hide();
		} else {
			$('#' + elID + '-fb').find('.ib2-facebook-optin-txt').show();
		}
		$('#' + elID + '-fb').find('.ib2-facebook-optin-txt').text(text);
		
		$("body").trigger("ib2CommonChange");
	});
	
	// Webinar Integration
	$('#optin-webinar-redirect').bind('keyup blur', function(){
		var $this = $(this), url = $(this).val(),
		elID = $('#ib2-current-optin').val();
		
		if ( $('#optin-webinar').val() != '' && url != '' ) {
			if ( !$('#' + elID).find('input[name=_webinar_redirect]').length ) {
				$('#' + elID).find('.form-fields').append('<input type="hidden" name="_webinar_redirect">');
			}
			
			$('#' + elID).find('input[name=_webinar_redirect]').val(url);
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#optin-webinar').change(function(){
		var $this = $(this), opt = $("option:selected", this).val(),
		elID = $('#ib2-current-optin').val(), origAction = '#',
		origMethod = 'post';
		
		if ( opt != '' ) {
			var newAction = $('#ib2-webinar-action').val();
			if ( $('#' + elID).find('form').attr('action') ) {
				origAction = $('#' + elID).find('form').attr('action');
			}
			
			if ( $('#' + elID).find('form').attr('method') ) {
				origMethod = $('#' + elID).find('form').attr('method');
			}
			
			$('#' + elID).find('form').attr('action', newAction);
			$('#' + elID).find('form').attr('method', 'post');
			
			if ( origAction != '#' ) {
				if ( $('#' + elID).find('input[name=_orig_action_url]').length ) {
					// do nothing
				} else {
					$('#' + elID).find('.form-fields').append('<input type="hidden" name="_orig_action_url" value="' + origAction + '">');
				}
			}
			
			if ( $('#' + elID).find('input[name=_orig_method]').length ) {
				// do nothing
			} else {
				$('#' + elID).find('.form-fields').append('<input type="hidden" name="_orig_method" value="' + origMethod + '">');
			}
			
			if ( $('#' + elID).find('input[name=_webinar_key]').length ) {
				$('#' + elID).find('input[name=_webinar_key]').val(opt);
			} else {
				$('#' + elID).find('.form-fields').append('<input type="hidden" name="_webinar_key" value="' + opt + '">');
			}
			
			if ( $('#optin-html-code').val() != '' ) {
				$('.webinar-signup-group').hide();
			} else {
				$('.webinar-signup-group').show();
			}
			
			if ( $('#optin-html-code').val() == '' && $('#optin-webinar-redirect').val() != '' ) {
				if ( $('#' + elID).find('input[name=_webinar_redirect]').length ) {
					$('#' + elID).find('input[name=_webinar_redirect]').val($('#optin-webinar-redirect').val());
				} else {
					$('#' + elID).find('.form-fields').append('<input type="hidden" name="_webinar_redirect" value="' + $('#optin-webinar-redirect').val() + '">');
				}
			} else {
				$('#' + elID).find('input[name=_webinar_redirect]').remove();
			}
		} else {
			$('.webinar-signup-group').hide();
			if ( $('#' + elID).find('input[name=_orig_action_url]').length ) {
				$('#' + elID).find('form').attr('action', $('#' + elID).find('input[name=_orig_action_url]').val());
				$('#' + elID).find('input[name=_orig_action_url]').remove();
			}
			
			if ( $('#' + elID).find('input[name=_orig_method]').length ) {
				$('#' + elID).find('form').attr('method', $('#' + elID).find('input[name=_orig_method]').val());
				$('#' + elID).find('input[name=_orig_method]').remove();
			}
			
			if ( $('#' + elID).find('input[name=_webinar_key]').length ) {
				$('#' + elID).find('input[name=_webinar_key]').remove();
			}
			
			if ( $('#' + elID).find('input[name=_webinar_redirect]').length ) {
				$('#' + elID).find('input[name=_webinar_redirect]').remove();
			}
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	// Optin Form Processing...
	$('#optin-process-btn').click(function(e){
		e.preventDefault();
		var $this = $(this), code = $('#optin-html-code').val(), _code = code,
		optin_id = $('#ib2-current-optin').val(), codeID = optin_id + '-rawcode';
		if ( code == '' ) {
			alert('ERROR: Please enter the HTML code version of your opt-in form.');
			return false;
		}
		
		code = htmlEntity(code, 1);
		
		var webinar = 0,
		webinarKey = '';
		if ( $('#' + optin_id).find('input[name=_webinar_key]').length ) {
			webinar = 1;
			webinarKey = $('#' + optin_id).find('input[name=_webinar_key]').val();	
		}
		var classes = '';
		var styles = '';
		if ( $('#' + optin_id).find('input[type=text]').eq(0).length ) {
			var txtInput = $('#' + optin_id).find('input[type=text]').eq(0);
			if ( txtInput.hasClass('input-sm') )
				classes += ' input-sm';
				
			if ( txtInput.hasClass('input-lg') )
				classes += ' input-lg';
				
			if ( txtInput.hasClass('input-ln') )
				classes += ' input-ln';
				
			if ( txtInput.hasClass('field-normal') )
				classes += ' field-normal';
				
			if ( txtInput.hasClass('field-normal-thick') )
				classes += ' field-normal-thick';
				
			if ( txtInput.hasClass('field-sharp') )
				classes += ' field-sharp';
				
			if ( txtInput.hasClass('field-sharp-thick') )
				classes += ' field-sharp-thick';
				
			if ( txtInput.attr('style') )
				styles = txtInput.attr('style');
		}
		
		$this.button('loading');
		$('#optin-html-code').attr('disabled', true);
		var data = {
				action: 'ib2_process_optin',
				code: code,
				optin_id: optin_id,
				post_id: $('#ib2-post-id').val(),
				classes: classes,
				styles: styles
		};
			
		// Save optin code to a temporary placeholder
		if ( !$('#' + codeID).length ) {
			$('#optin-code-placeholder').append('<div id="' + codeID + '" class="ib2-optin-rawcode" data-process="no"></div>');
		}
		
		$.post(ajaxurl, data, function( response ){
			if ( response.success != 'false' ) {
				$('#' + optin_id).find('form').attr('action', response.action);
				$('#' + optin_id).find('form').attr('method', response.method);
				$('#' + optin_id).find('.form-fields').html(response.html);
				
				if ( webinar == 1 ) {
					var newAction = $('#ib2-webinar-action').val();
					$('#' + optin_id).find('form').attr('action', newAction);
					$('#' + optin_id).find('form').attr('method', 'post');
					
					if ( $('#' + optin_id).find('input[name=_orig_action_url]').length ) {
						$('#' + optin_id).find('input[name=_orig_action_url]').val(response.action);
					} else {
						$('#' + optin_id).find('.form-fields').append('<input type="hidden" name="_orig_action_url" value="' + response.action + '">');
					}
				
					if ( $('#' + optin_id).find('input[name=_orig_method]').length ) {
						$('#' + optin_id).find('input[name=_orig_method]').val(response.method);
					} else {
						$('#' + optin_id).find('.form-fields').append('<input type="hidden" name="_orig_method" value="' + response.method + '">');
					}
				
					if ( $('#' + optin_id).find('input[name=_webinar_key]').length ) {
						$('#' + optin_id).find('input[name=_webinar_key]').val(webinarKey);
					} else {
						$('#' + optin_id).find('.form-fields').append('<input type="hidden" name="_webinar_key" value="' + webinarKey + '">');
					}
					
					if ( $('#' + optin_id).find('input[name=_webinar_redirect]').length ) {
						$('#' + optin_id).find('input[name=_webinar_redirect]').remove();
					}
					
					$('.webinar-signup-group').hide();
				}
			}
			
			$this.button('reset');
			$('#optin-html-code').removeAttr('disabled');
			
			$('.optin-unprocess-group').hide();
			$('#optin-process').hide();
			$('#change-optin').show();
			$('.optin-process-msg').show();
			
			$('#' + codeID).html(response.rawcode);
			
			$('#' + codeID).attr('data-process', 'yes');
			$('#' + codeID).data('process', 'yes');
			
			
		});
	});
	
	$('.change-optin-code').click(function(e){
		$('.optin-unprocess-group').show();
		$('#optin-process').show();
		$('#change-optin').hide();
		$('.optin-process-msg').hide();
		
		e.preventDefault();
	});
	
	// Disable opt-in form submit button
	$('#main-editor').on('submit click', '.ib2-form-submit', function(e){
		e.preventDefault();
	});
	
	// Manage Opt-In Fields
	$('#optin-manage-fields').click(function(e){
		var $this = $(this), elID = $('#ib2-current-optin').val(),
		el = $('#' + elID);
		
		$('#ib2-fields-modal').modal({
			backdrop: true,
			show: true
		});
		
		$('#fields-sortable').hide();
		$('#fields-loader').show();
		
		$('#fields-sortable').find('tbody').html('');
		el.find('.ib2-opt-field').each(function(i){
			var tagName = $(this).prop("tagName"), label,
			name = ( $(this).attr('name') ) ? $(this).attr('name') : '', 
			type = ( $(this).attr('type') ) ? $(this).attr('type') : tagName.toLowerCase(),
			reqchecked = ( $(this).hasClass('ib2-required') ) ? ' checked="checked"' : '',
			emchecked = ( $(this).hasClass('ib2-validate-email') ) ? ' checked="checked"' : '',
			hdchecked = ( $(this).hasClass('ib2-field-hidden') ) ? ' checked="checked"' : '',
			reqval = ( $(this).hasClass('ib2-required') ) ? '1' : '0',
			emval = ( $(this).hasClass('ib2-validate-email') ) ?  '1' : '0',
			hdval = ( $(this).hasClass('ib2-field-hidden') ) ? '1' : '0';
			
			if ( emchecked != '' ) {
				hdchecked = ' disabled';
				hdval = '0';
			}
			
			if ( type == 'select' ) {
				label = ( $(this).parent().find('label').length ) ? $(this).parent().find('label').text() : '';
			} else if ( type == 'checkbox' || type == 'radio' ) {
				label = ( $(this).parent().find('.label-txt').length ) ? $(this).parent().find('.label-txt').text() : '';
			} else {
				label = $(this).attr('placeholder');
			}
			
			$ienv_selected = ( $(this).hasClass('field-mail-icon') ) ? ' selected="selected"' : '';
			$iuser_selected = ( $(this).hasClass('field-user-icon') ) ? ' selected="selected"' : '';
			$ihome_selected = ( $(this).hasClass('field-home-icon') ) ? ' selected="selected"' : '';
			$iphone_selected = ( $(this).hasClass('field-phone-icon') ) ? ' selected="selected"' : '';
			$imobile_selected = ( $(this).hasClass('field-mobile-icon') ) ? ' selected="selected"' : '';
			$isearch_selected = ( $(this).hasClass('field-search-icon') ) ? ' selected="selected"' : '';
			$iclock_selected = ( $(this).hasClass('field-clock-icon') ) ? ' selected="selected"' : '';
			$icon_disabled = ( type != 'text' ) ? ' disabled' : '';
			
			var html = '<tr id="' + name + '">';
			html += '<td class="optin-field-handle" style="cursor:move">::</td>';
			html += '<td><input type="hidden" class="type-field" value="' + type + '" /><div class="form-group"><input type="text" class="form-control name-field" value="' + name + '" /></div></td>';
		    html += '<td><div class="form-group"><input type="text" class="form-control label-field" value="' + label + '" /></div></td>';
		    html += '<td><div class="form-group"><select class="form-control icon-field"' + $icon_disabled + '>';
				html += '<option value="none">None</option>';
				html += '<option value="mail"' + $ienv_selected + '>Mail icon</option>';
				html += '<option value="user"' + $iuser_selected + '>User icon</option>';
				html += '<option value="home"' + $ihome_selected + '>Home icon</option>';
				html += '<option value="phone"' + $iphone_selected + '>Phone icon</option>';
				html += '<option value="mobile"' + $imobile_selected + '>Mobile Phone icon</option>';
				html += '<option value="search"' + $isearch_selected + '>Search icon</option>';
				html += '<option value="clock"' + $iclock_selected + '>Clock icon</option>';
				html += '</select></div></td>';
		    html += '<td><input type="checkbox" value="1" class="req-check"' + reqchecked + ' /></td>';
		    html += '<td><input type="checkbox" value="1" class="email-check"' + emchecked + ' /></td>';
		    html += '<td><input type="checkbox" value="1" class="hide-check"' + hdchecked + ' /></td>';
		    html += '<td>';
		    
		    if ( !$(this).hasClass('ib2-validate-email') )
		    	html += '<button class="btn btn-danger btn-xs remove-optin-field">Delete</button>';
		    else
		    	html += '<button class="btn btn-danger btn-xs remove-optin-field" style="display:none">Delete</button>';
		    	
		    html += '</td>';
		    
		    $('#fields-sortable').find('tbody').append(html);
		});
		
		$('#fields-sortable').show();
		$('#fields-loader').hide();
		
		$('#fields-sortable').find('tbody').unbind('sortable').sortable({ handle: ".optin-field-handle" });
		
		e.preventDefault();
	});
	
	$('#fields-sortable').find('tbody').sortable({ handle: ".optin-field-handle" });
	
	$('body').on('click', '#add-optin-field', function(e){
		var fieldcount = $('#fields-sortable > tbody > tr').length;
		var fieldname = 'custom_field_name_' + fieldcount;
		
		var html = '<tr id="' + fieldname + '">';
			html += '<td class="optin-field-handle" style="cursor:move">::</td>';
			html += '<td><input type="hidden" class="type-field" value="text" /><div class="form-group"><input type="text" class="form-control name-field" value="' + fieldname + '" /></div></td>';
		    html += '<td><div class="form-group"><input type="text" class="form-control label-field" value="Custom Field Label ' + fieldcount + '" /></div></td>';
		    html += '<td><div class="form-group"><select class="form-control icon-field">';
				html += '<option value="none" selected="selected">None</option>';
				html += '<option value="mail">Mail icon</option>';
				html += '<option value="user">User icon</option>';
				html += '<option value="home">Home icon</option>';
				html += '<option value="phone">Phone icon</option>';
				html += '<option value="mobile">Mobile Phone icon</option>';
				html += '<option value="search">Search icon</option>';
				html += '<option value="clock">Clock icon</option>';
				html += '</select></div></td>';
		    html += '<td><input type="checkbox" value="1" class="req-check" /></td>';
		    html += '<td><input type="checkbox" value="1" class="email-check" /></td>';
		    html += '<td><input type="checkbox" value="1" class="hide-check" /></td>';
		    html += '<td>';
		    html += '<button class="btn btn-danger btn-xs remove-optin-field">Delete</button>';
		    html += '</td>';
		    
		    $('#fields-sortable').find('tbody').append(html);
		    
		    e.preventDefault();
	});
	
	$('body').on('click', '.email-check', function(e){
		if ( $(this).is(":checked") ) {
			$(this).parents('tr').find('.hide-check')
				.removeAttr('checked')
				.attr('disabled', true);
				
			$(this).parents('tr').find('.remove-optin-field').hide();
		} else {
			$(this).parents('tr').find('.hide-check')
				.removeAttr('disabled');
				
			$(this).parents('tr').find('.remove-optin-field').show();
		}
	});
	
	$('body').on('click', '.remove-optin-field', function(e){
		$(this).parents('tr').remove();
		e.preventDefault();
	});
	
	$('body').on('click', '#btn-save-opt-fields', function(e){
		var $this = $(this), optin_id = $('#ib2-current-optin').val(), html = '';

		$('#fields-sortable').find('tbody').find('tr').each(function(){
			var $this = $(this), fieldname = $this.attr('id'),
			type = $this.find('.type-field').val(),
			newname = $this.find('.name-field').val(),
			newlabel = $this.find('.label-field').val();
			
			var el = $('#' + optin_id).find('[name="' + fieldname + '"]');
			
			if ( !el.length ) { // this is a new text field... insert it first...
				var newhtml = '<div class="ib2-field-group form-group">';
				newhtml += '<input class="form-control ib2-opt-field" type="text" name="' + newname + '" placeholder="' + newlabel + '" />';
				newhtml += '</div>';
				
				$('#' + optin_id).find('.form-fields').append(newhtml);
				
				el = $('#' + optin_id).find('[name=' + newname + ']');
			}
			
			// change field label
			if ( type == 'text' || type == 'textarea' ) {
				el.attr('placeholder', newlabel);
			} else if ( type == 'select' ) {
				if ( el.parent().find('label').length )
					el.parent().find('label').text(newlabel);
				else
					el.parent().prepend.html('<label>' + newlabel + '</label>');
			} else {
				el.parent().find('label-txt').text(newlabel);
			}
			
			// Icon Class
			el.removeClass('field-mail-icon field-user-icon field-home-icon field-phone-icon field-mobile-icon field-search-icon field-clock-icon');
			if ( $this.find('.icon-field').val() != 'none' ) {
				el.addClass('field-' + $this.find('.icon-field').val() + '-icon');
			}
			
			// Required class
			if ( $this.find('.req-check').is(":checked") ) {
				el.addClass('ib2-required');
			} else {
				el.removeClass('ib2-required');
			}
			
			// Email validation class
			if ( $this.find('.email-check').is(":checked") ) {
				el.addClass('ib2-validate-email');
			} else {
				el.removeClass('ib2-validate-email');
			}
			
			// Field hidden class
			if ( $this.find('.hide-check').is(":checked") ) {
				el.parents('.ib2-field-group').css('display', 'none');
				el.addClass('ib2-field-hidden');
			} else {
				el.parents('.ib2-field-group').css('display', 'block');
				el.removeClass('ib2-field-hidden');
			}
				
			// change field name
			el.attr('name', newname);
			
			var newEl = $('#' + optin_id).find('[name=' + newname + ']');
			if ( type == 'checkbox' ) {
				html += '<div class="ib2-field-group checkbox">';
				html += newEl.parent().parent().html();
			} else if ( type == 'radio' ) {
				html += '<div class="ib2-field-group radio">';
				html += newEl.parent().parent().html();
			} else {
				html += '<div class="ib2-field-group form-group">';
				html += newEl.parent().html();
			}
			
			html += '</div>';
			
		});
		
		$('#' + optin_id).find('.form-fields').find('.ib2-field-group').remove();
		$('#' + optin_id).find('.form-fields').prepend(html);
		$('#ib2-fields-modal').modal("hide");
		e.preventDefault();
	});
	
	$('#optin-edit-submit').click(function(e){
		var prevmode = $('#ib2-current-panel').val();
		$('#main-editor').removeClass('col-md-12');
		$('#main-editor').addClass('col-md-9');
		$('.editor-panel-content').hide();
		$('.settings-button').show();
		$('#editor-panel').show("slide", { direction: "right" }, 500);
		
		var optin_id = $('#ib2-current-optin').val(),
		elID = optin_id + '-submit',
		element = $('#' + elID),
		type = element.data('buttonType');
		
		$('#ib2-current-button').val(optin_id + '-submit');
		$('#ib2-button-mode').val('optin');
		
		$('#back-panel').find('button').data('settings', prevmode);
		$('#back-panel').find('button').attr('data-settings', prevmode);
		$('#back-panel').show();
		
		$('#ib2-current-panel').val('button');
				
		ib2_button_setting(elID, element, type);
		
	});
	
	// =========================== SEARCH IMAGES =======================
	$('.background-image-search').each(function(){
		$(this).click(function(e){
			var $this = $(this);
			$('#ib2-background-element').val($this.data('element'));
			$('#ib2-isearch-type').val($this.data('type'));
			
			$('#ib2-imgsearch-modal').modal({
				backdrop: true,
				show: true
			});
			e.preventDefault();	
		});
	});
	
	$('body').on('submit', 'form#ib2-img-search', function(e){
		e.preventDefault();
		
		var $this = $(this), q = $('#image-query').val();
		
		if ( q != '' ) {
			$('#isearch-loader').show();
			
			var data = {
				action: 'ib2_search_images',
				query: q,
				start: 0
			};
			
			$('#isearch-load-more').hide();
			$('#isearch-results').html('');
			
			$.post(ajaxurl, data, function(response){
				if ( response.success ) {
					$('#isearch-results').html(response.output);
					$('#isearch-results').append('<div class="clearfix"></div>');
					$('#ib2-isearch-page').val('1');
					$('#ib2-isearch-pages').val(response.pages);
					$('#ib2-isearch-term').val(q);
					if ( response.pages > 1 ) {
						$('#isearch-load-more').show();
					}
				}
				
				$('#isearch-loader').hide();
			});
		}
		return false;
	});
	
	$('body').on('click', '#isearch-load-more', function(e){
		var $this = $(this), q = $('#ib2-isearch-term').val(),
		page = parseInt($('#ib2-isearch-page').val()),
		start = page * 8,
		pages = parseInt($('#ib2-isearch-pages').val()),
		next_page = page + 1;
		
		$('#isearch-loader').show();
		$this.attr('disabled', true);
			
		var data = {
			action: 'ib2_search_images',
			query: q,
			start: start
		};
			
		$.post(ajaxurl, data, function(response){
			if ( response.success ) {
				$('#isearch-results').find('.clearfix').remove();
				$('#isearch-results').append(response.output);
				$('#isearch-results').append('<div class="clearfix"></div>');
				$('#ib2-isearch-page').val(next_page);
			}
			$this.removeAttr('disabled');
			if ( pages == next_page )
				$this.hide();
				
			$('#isearch-loader').hide();
		});
		e.preventDefault();
	});
	
	$('body').on('mouseenter', '.isearch-thumb', function(e){
		$(this).find('button').show();
	});
	
	$('body').on('mouseleave', '.isearch-thumb', function(e){
		$(this).find('button').hide();
	});
	
	$('body').on('click', '.use-this-image', function(e){
		var $this = $(this), type = $('#ib2-isearch-type').val(),
		type = $('#ib2-background-element').val(),
		img = $this.parent().find('img').data('src'),
		more = $('#isearch-load-more').css('display');
		
		if ( type == 'box' ) {
			var boxID = $('#ib2-current-box').val();	
		} else if ( type == 'image' ) {
			var imageID = $('#ib2-current-image').val();
		}
		
		// downloading image...
		$('#isearch-downloader').show();
		$('#isearch-results').hide();
		$('#isearch-load-more').hide();
		
		var data = {
			action: 'ib2_download_image',
			img_url: img,
			post_id: $('#ib2-post-id').val()
		};
			
		$.post(ajaxurl, data, function(response){
			if ( response.success ) {
				var image = response.img;
				if ( type == 'screen-container' ) {
					ib2_body_background('screen-container', image);
					$('#body-bg-mode').val('search');
				} else if ( type == 'box' ) {
					if ( $('#' + boxID).data('el') == 'box' ) {
						ib2_box_background(boxID, image);
					} else {
						ib2_section_background(boxID, image);
					}
					$('#' + boxID).data('imgMode', 'search');
					$('#' + boxID).attr('data-img-mode', 'search');
				} else if ( type == 'image' ) {
					$('#' + imageID).find('img').attr('src', image);
					var newImgWidth = response.width,
					newImgHeight = response.height;
					$('#' + imageID).find('.el-content').css({
						'width': newImgWidth + 'px',
					});
					
					$('#' + imageID).find('.el-content > img').css({
						'width': newImgWidth + 'px',
						'height': 'auto'
					});
					
					//$('#cur-img-width').val(newImgWidth);
					//$('#cur-img-height').val(newImgHeight);
			
					ib2_image(imageID, image);
				}
				$('#ib2-imgsearch-modal').modal("hide");
			}
			$('#isearch-downloader').hide();
			$('#isearch-results').show();
			
			if ( more == 'block' )
				$('#isearch-load-more').show();
				
		});
		
		$("body").trigger("ib2CommonChange");
		e.preventDefault();
	});
	
	// =========================== BUTTON TARGET =======================
	
	$('#target-link-type').change(function(){
		var opt = $("option:selected", this).val(),
		elID = $('#ib2-current-button').val();
		
		if ( opt == 'popup' ) {
			$('.popup-target-group').show();
			$('.url-target-group').hide();
			
			$('#' + elID).data('target', 'popup');
			$('#' + elID).attr('data-target', 'popup');
			
			$('#' + elID).find('a.ib2-button').addClass('open-popup');
		} else {
			$('.popup-target-group').hide();
			$('.url-target-group').show();
			
			$('#' + elID).data('target', 'url');
			$('#' + elID).attr('data-target', 'url');
			
			$('#' + elID).find('a.ib2-button').removeClass('open-popup');
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	// =========================== MENU =======================
	
	function ib2_menu_css( elID ) {
		var el = $('#' + elID), textColor = $('#menu-text-color').val(),
		hoverTextColor = $('#menu-hover-text-color').val(),
		bgColor = $('#menu-background').val(), hoverBgColor = $('#menu-hover-background').val(),
		subTextColor = $('#sub-menu-text-color').val(),
		subHoverTextColor = $('#sub-menu-hover-text-color').val(),
		subBgColor = $('#sub-menu-background').val(),
		subHoverBgColor = $('#sub-menu-hover-background').val(),
		menuStyle = $("#menu-style").val(),
		subBorderTop = getAltColor(subBgColor, 'lighter'),
		subBorderBot = getAltColor(subBgColor, 'darker');
		
		if ( !$('#' + elID + '-css').length ) {
			$('head').append('<style type="text/css" class="ib2-element-css" id="' + elID + '-css"></style>');
		}
		
		var css = '';
		if ( menuStyle != 'plain' && menuStyle != 'plain-pipe' ) {
			css += '#' + elID + ' ul.ib2-navi { background-color: ' + bgColor + '; } ';
			
			if ( menuStyle == 'glossy' ) {
				var glossyColor = getAltColor(bgColor, 'lighter');
				css += '#' + elID + ' ul.ib2-navi { background-image: linear-gradient(to bottom, ' + glossyColor + ' 0%, ' + bgColor + ' 100%); ';
		    	css += 'background-image: -moz-linear-gradient(top, ' + glossyColor + ' 0%, ' + bgColor + ' 100%); } ';
			}
		}
		
		css += '#' + elID + ' ul.ib2-navi li a { color: ' + textColor + '; } ';
		css += '#' + elID + ' ul.ib2-navi ul { background-color: ' + subBgColor + '; } ';
		css += '#' + elID + ' ul.ib2-navi ul li { border-top-color: ' + subBorderTop + '; border-bottom-color: ' + subBorderBot + '; } ';
		css += '#' + elID + ' ul.ib2-navi ul li a { color: ' + subTextColor + '; } ';
		
		$('#' + elID + '-css').html(css);
		
		if ( !$('#' + elID + '-hover-css').length ) {
			$('head').append('<style type="text/css" class="ib2-element-css" id="' + elID + '-hover-css"></style>');
		}
		
		css = '';
		if ( menuStyle != 'plain' && menuStyle != 'plain-pipe' ) {
			css += '#' + elID + ' ul.ib2-navi li:hover, ul.ib2-navi li:focus { background-color: ' + hoverBgColor + ' !important; } ';
			if ( menuStyle == 'glossy' ) {
				var glossyColor = getAltColor(hoverBgColor, 'darker');
				css += '#' + elID + ' ul.ib2-navi li:hover, ul.ib2-navi li:focus { background-image: linear-gradient(to bottom, ' + glossyColor + ' 5%, ' + hoverBgColor + ' 100%) !important; ';
		    	css += 'background-image: -moz-linear-gradient(top, ' + glossyColor + ' 5%, ' + hoverBgColor + ' 100%) !important; } ';
			}
		}
		css += '#' + elID + ' ul.ib2-navi > li > a:hover, #' + elID + ' ul.ib2-navi > li > a:focus { color: ' + hoverTextColor + ' !important; } ';
		
		$('#' + elID + '-hover-css').html(css);
		
		if ( !$('#' + elID + '-sub-hover-css').length ) {
			$('head').append('<style type="text/css" class="ib2-element-css" id="' + elID + '-sub-hover-css"></style>');
		}
		
		css = '';
		css += '#' + elID + ' ul.ib2-navi li ul li a:hover, #' + elID + ' ul.ib2-navi li ul li a:focus { color: ' + subHoverTextColor + '; background-color: ' + subHoverBgColor + ' !important; text-decoration: none !important; } ';
	
		$('#' + elID + '-sub-hover-css').html(css);
	}

	$('#menu-style').change(function(){
		var $this = $(this), opt = $("option:selected", this).val();
		elID = $('#ib2-current-menu').val(), el = $('#' + elID);
		
		el.find('nav > ul').attr('class', 'ib2-navi ib2-navi-' + opt);
		
		if ( opt == 'plain' || opt == 'plain-pipe' ) {
			$('.non-plain-menu-group').hide();
		} else {
			$('.non-plain-menu-group').show();
		}
		
		el.data('style', opt);
		el.attr('data-style', opt);
				
		ib2_menu_css(elID);
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#menu-list').change(function(){
		var $this = $(this), opt = $("option:selected", this).val();
		elID = $('#ib2-current-menu').val(), el = $('#' + elID);
		
		if ( opt != '' ) {
			$this.attr('disabled', true);
			el.find('nav').html('Loading...');
			$.post(ajaxurl, {
				action: 'ib2_change_menu',
				menu: opt,
				id: elID,
				style: $('#menu-style').val()
			}, function( response ) {
				el.data('menu', opt);
				el.attr('data-menu', opt);
				
				el.find('nav').html(response);
				
				$this.removeAttr('disabled');
			});
		}
		
		$("body").trigger("ib2CommonChange");
	});
	
	// =========================== ATTENTION BAR =======================
	
	$('#attention-bar-text').bind('blur keyup', function(){
		$('.att-text').html($(this).val());
		
		$("body").trigger("ib2GlobalChange", ["input", "attention-bar-text", $(this).val(), 'attentionBarText']);
	});
	
	$('#attention-bar-anchor').bind('blur keyup', function(){
		$('.att-url').text($(this).val());
		
		$("body").trigger("ib2GlobalChange", ["input", "attention-bar-anchor", $(this).val(), 'attentionBarAnchor']);
	});
	
	$('#attention-bar-url').bind('blur keyup', function(){
		$('.att-url').attr('href', $(this).val());
		
		$("body").trigger("ib2GlobalChange", ["input", "attention-bar-url", $(this).val(), 'attentionBarUrl']);
	});
	
	$('#attention-bar-font').change(function(){
		var opt = $("option:selected", this).val();
		$('.ib2-notification-text').css('fontFamily', opt);
		
		$("body").trigger("ib2GlobalChange", ["input", "attention-bar-font", opt, 'attentionBarFont']);
	});
	
	$('#attention-bar-enable').prettyCheckable();
	$('body').on('click', 'label[for=nb-hide]', function(e){
		$('#screen-container').css('paddingTop', '0px');
	});
	$('body').on('click', 'label[for=nb-show]', function(e){
		var attHeight = $('.ib2-notification-text').outerHeight() - 2;
		$('#screen-container').css('paddingTop', attHeight + 'px');
	});
	
	$('body').on('change', '#attention-bar-enable', function(){
		var $this = $(this);
		if ( $this.is(":checked") ) {
			$('.attention-bar-group').show();
			if ( !$('.ib2-notification-bar').length ) {
				var nb = '<div class="ib2-notification-bar">';
					nb += '<input id="nb-hide" name="attbar" type="radio" value="hide">';
					nb += '<input id="nb-show" name="attbar" type="radio" value="show" checked="checked">';
	
					nb += '<label for="nb-hide">hide</label>';
					nb += '<label for="nb-show" style="background-color: ' + $('#attention-bar-background').val() + ';" >show</label>';
	
					nb += '<div class="ib2-notification-text" style="background-color: ' + $('#attention-bar-background').val() + '; border-color: #FFF; color: #FFF;"><span class="att-text">' + $('#attention-bar-text').val() + '</span> <a class="att-url" style="color:#FFF" href="' + $('#attention-bar-url').val() + '" target="_blank">' + $('#attention-bar-anchor').val() + '</a></div>';
				nb += '</div>';

				$('#screen-container').append(nb);
				
				var attHeight = $('.ib2-notification-text').outerHeight() - 2;
				$('#screen-container').css('paddingTop', attHeight + 'px');
			}
		} else {
			$('.ib2-notification-bar').remove();
			$('#screen-container').css('paddingTop', '0px');
			$('.attention-bar-group').hide();
		}
	});
	
	// =========================== BOX BACKGROUND VIDEO =======================
	$('#box-bgvid').prettyCheckable();
	$('#box-bgvid').on('change', function(){
  		var elID = $('#ib2-current-box').val();
  		if ( $(this).is(":checked") ) {
  			$('#bgvideo-set').show();
  			$('#' + elID + ' > .el-content').prepend('<div class="ib2-bgvideo-param" id="' + elID + '_bgvid"></div>');

  			if ( $('#bgvideo-mp4').val() != '' ) {
  				$('#' + elID + '_bgvid').attr('data-mp4', $('#bgvideo-mp4').val());
 				$('#' + elID + '_bgvid').data('mp4', $('#bgvideo-mp4').val());
  			}

  			if ( $('#bgvideo-ogg').val() != '' ) {
  				$('#' + elID + '_bgvid').attr('data-ogg', $('#bgvideo-ogg').val());
 				$('#' + elID + '_bgvid').data('ogg', $('#bgvideo-ogg').val());
  			}

  			if ( $('#bgvideo-webm').val() != '' ) {
  				$('#' + elID + '_bgvid').attr('data-webm', $('#bgvideo-webm').val());
 				$('#' + elID + '_bgvid').data('webm', $('#bgvideo-webm').val());
  			}
  		} else {
  			$('#bgvideo-set').hide();
  			$('#' + elID + ' > .el-content').find('.ib2-bgvideo-param').remove();
  		}
 	});

 	$('#bgvideo-mp4').bind('keyup blur', function(){
 		var elID = $('#ib2-current-box').val(), vid = $(this).val();

 		$('#' + elID + '_bgvid').attr('data-mp4', vid);
 		$('#' + elID + '_bgvid').data('mp4', vid);
 	});

 	$('#bgvideo-ogg').bind('keyup blur', function(){
 		var elID = $('#ib2-current-box').val(), vid = $(this).val();

 		$('#' + elID + '_bgvid').attr('data-ogg', vid);
 		$('#' + elID + '_bgvid').data('ogg', vid);
 	});

 	$('#bgvideo-webm').bind('keyup blur', function(){
 		var elID = $('#ib2-current-box').val(), vid = $(this).val();

 		$('#' + elID + '_bgvid').attr('data-webm', vid);
 		$('#' + elID + '_bgvid').data('webm', vid);
 	});

	// =========================== HOTSPOT =======================
	$('#hotspot-blink').prettyCheckable();
	$('#hotspot-blink').on('change', function(){
  		var elID = $('#ib2-current-hotspot').val();
  		if ( $(this).is(":checked") ) {
  			$('#' + elID).addClass('hotspot-blink');
  		} else {
  			$('#' + elID).removeClass('hotspot-blink');
  		}
 	});
 	
	$('#hotspot-type').change(function(){
		var opt = $("option:selected", this).val(),
		elID = $('#ib2-current-hotspot').val(),
		prev = $('#' + elID).data('toggle');
		
		if ( prev == 'popover' )
			$('#' + elID).popover("destroy");
				
		if ( prev == 'tooltip' )
			$('#' + elID).tooltip("destroy");
				
		if ( opt == 'tooltip' ) {
			$('#' + elID).tooltip();
		} else if ( opt == 'popover' ) {
			$('#' + elID).popover();
		}
		
		$('.hotspot-type-prop').hide();
		$('.' + opt + '-prop').show();
		
		$('#' + elID).attr('data-toggle', opt);
		$('#' + elID).data('toggle', opt);
	});
	
	$('#hotspot-trigger').change(function(e){
		var $this = $(this), elID = $('#ib2-current-hotspot').val(),
		opt = $("option:selected", this).val(), type = $('#' + elID).data('toggle');
		
		$('#' + elID).attr('data-trigger', opt);
		$('#' + elID).data('trigger', opt);
		
		if ( type == 'popover' ) {
			$('#' + elID).popover("destroy");
			$('#' + elID).popover();
		} else if ( type == 'tooltip' ) {
			$('#' + elID).tooltip("destroy");
			$('#' + elID).tooltip();
		}
	});
	
	$('#hotspot-border-type').change(function(e){
		var $this = $(this), elID = $('#ib2-current-hotspot').val(),
		opt = $("option:selected", this).val();
		
		$('#' + elID).css('border-style', opt);
	});
	
	$('#tooltip-text, #popover-title').bind("keyup blur", function(e){
		var $this = $(this), elID = $('#ib2-current-hotspot').val(),
		text = $this.val(), type = $('#' + elID).data('toggle');
		
		$('#' + elID).attr('title', text);
		
		if ( type == 'popover' ) {
			$('#' + elID).popover("destroy");
			$('#' + elID).popover();
		} else if ( type == 'tooltip' ) {
			$('#' + elID).tooltip("destroy");
			$('#' + elID).tooltip();
		}
	});
	
	$('#popover-text').bind("keyup blur", function(e){
		var $this = $(this), elID = $('#ib2-current-hotspot').val(),
		text = $this.val();
		
		$('#' + elID).attr('data-content', text);
		$('#' + elID).data('content', text);
		
		$('#' + elID).popover("destroy");
		$('#' + elID).popover();
	});
	
	$('#screen-container').on('click', '.ib2-hotspot-btn', function(e){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = $('#' + elID), newID = elID + '-' + generateID(8), count = el.find(' > .el-content').find('.ib2-hotspot-el').length;
		
		if ( $is_popup == 1 ) {
			alert('ERROR: This feature cannot be used in PopUp mode.');
			return false;
		}
		
		el.addClass('ib2-image-hotspot-el');
		
		var top = Math.floor(Math.random()*(55-1+1)+1);
		var left = Math.floor(Math.random()*(55-1+1)+1);
		var attr = 'style="top:' + top + '%; left:' + left + '%; width:30px; height:30px; background-color:#212121; border: 2px solid #009900; border-radius:50%; opacity: 0.8"';
		attr += ' data-toggle="tooltip" title="Double Click To Edit" data-placement="auto" data-trigger="hover" data-content="My Pop-Over Content"';
		el.find(' > .el-content').append('<div class="ib2-hotspot-el" id="' + newID + '" ' + attr + '></div>');
		
		$('#' + newID).tooltip();
		
		e.preventDefault();
	});
	
	$('body').on('click', '.delete-hotspot', function(e){
		var $this = $(this), elID = $('#ib2-current-hotspot').val(),
		type = $('#' + elID).data('toggle'), parts = elID.split("-"),
		imgID = parts[0];
		
		if ( type == 'popover' ) {
			$('#' + elID).popover("destroy");
		} else if ( type == 'tooltip' ) {
			$('#' + elID).tooltip("destroy");
		} else if ( type == 'popup' ) {
			$('#' + elID + '-popup').remove();
		}
		
		if ( $('#' + imgID + ' > .el-content').find('.ib2-hotspot-el').length <= 1 ) {
			//$('#' + imgID + ' > .el-content > img').addClass('img-responsive');
			$('#' + imgID).removeClass('ib2-image-hotspot-el');
		}
		
		$('#' + elID).remove();
		hideSidePanel.click();
		
		e.preventDefault();
	});
	
	// EDIT HOTSPOT
	$('#screen-container').on('dblclick', '.ib2-hotspot-el', function(e){
		var $this = $(this), elID = $(this).attr('id'), prevmode = $('#ib2-current-panel').val();
		
			settingsOpening = 1;
			
			$('#main-editor').removeClass('col-md-12');
			$('#main-editor').addClass('col-md-9');
			$('.editor-panel-content').hide();
			$('.settings-hotspot').show();
			$('#editor-panel').show();
			
			$('#ib2-current-hotspot').val(elID);
			
			// reset all tabs display
			if ( !$('.settings-tab > ul > li > a.tab-settings-active').length )
				$('.settings-tab > ul > li').show();
			
			// get the width of the editor panel
			var panelWidth = $('#editor-panel').outerWidth(),
			panelTop = $('#editor-panel').offset().top,
			panelLeft = $('#editor-panel').offset().left;
			
			$('#editor-panel-inside').css({
				'width': panelWidth + 'px',
				'top': panelTop + 'px',
				'left': panelLeft + 'px'
			});
			
			$('#editor-panel-inside').fadeIn("slow");
			$('#editor-panel-inside-content').perfectScrollbar('update');

			$('#ib2-current-panel').val('hotspot');
			
			$('#screen-container').trigger('editor_resize');
			
			var w = $('#' + elID).width(), h = $('#' + elID).height(), bg = $('#' + elID).css('backgroundColor'),
			bs = $('#' + elID).css('borderLeftStyle'), bc = $('#' + elID).css('borderLeftColor'),
			bw = $('#' + elID).css('borderLeftWidth').replace(/px/gi, ''),
			title = $('#' + elID).attr('title'), content = $('#' + elID).attr('data-content'),
			trigger = $('#' + elID).attr('data-trigger'), type = $('#' + elID).attr('data-toggle'),
			opac = $('#' + elID).css('opacity');
		
			if ( $('#' + elID).attr('data-original-title') )
				title = $('#' + elID).attr('data-original-title');
				
			$('#hotspot-type').val(type);
			$('.hotspot-type-prop').hide();
			$('.' + type + '-prop').show();
			
			$('#tooltip-text').val(title);
			$('#popover-title').val(title);
			$('#popover-text').val(content);
			$('#hotspot-border-type').val(bs);
			$('#hotspot-trigger').val(trigger);
			$('#hotspot-width-slider').slider("value", w);
			$('#hotspot-height-slider').slider("value", h);
			$('#hotspot-opac-slider').slider("value", opac);
			$('#hotspot-border-thick-slider').slider("value", bw);
			$('#hotspot-color').iris("color", bg);
			$('#hotspot-border-color').iris("color", bc);
			
			if ( $('#' + elID).hasClass('hotspot-blink') ) {
				$('#hotspot-blink').prettyCheckable("check");
			} else {
				$('#hotspot-blink').prettyCheckable("uncheck");
			}
			
			settingsOpening = 0;
			
			e.preventDefault();
	});
	
	$('#screen-container').on('mouseover', '.ib2-hotspot-el', function(){
		var $this = $(this), parts = $this.attr('id').split('-'), elID = parts[0];
		ib2_destroy();
		
		$this.draggable({ containment: "parent" });
	});
	
	$('#screen-container').on('mouseout', '.ib2-hotspot-el', function(){
		var $this = $(this), parts = $this.attr('id').split('-'), elID = parts[0];
		
		if ( $this.draggable("instance") )
			$this.draggable("destroy");
			
		ib2_init();
	});
	
	// =========================== WELCOME GATE =======================
	$('#wgate-enable').prettyCheckable();
	
	// =========================== EXIT SPLASH =======================
	
	$('#exit-splash-enable').prettyCheckable();
	
	// =========================== RIGHT CLICK =======================
	
	$('#right-click-enable').prettyCheckable();
	$('#right-click-img').prettyCheckable();
	
	// =========================== BOTTOM SLIDER =======================
	
	$('#main-slider-enable').prettyCheckable();
	$('#main-slider-close').prettyCheckable();
	
	$('body').on('change', '#main-slider-enable', function(){
		var $this = $(this);
		if ( $this.is(":checked") ) {
			$('.main-slider-group').show();
			$('#ib2-bottom-slider').show();
			$('html, body').animate({
				scrollTop: $('#ib2-bottom-slider').offset().top
	 		}, 1000);
		} else {
			$('.main-slider-group').hide();
			$('#ib2-bottom-slider').hide();
		}
	});
	// =========================== POPUP =======================
	
	$('#main-popup-enable').prettyCheckable();
	
	$('#screen-container').on('click', '.ib2-close-pop', function(e){
		$('#screen-container').trigger('popupclose');
		e.preventDefault();
	});
	
	$(document).keyup(function(e) {
		if ( e.keyCode == 27 && $is_popup == 1 ) {
			$('#screen-container').trigger('popupclose');
		}
	});
	
	$('#screen-container').on('click', '.ib2-popup-bg', function(e){
		$('#screen-container').trigger('popupclose');
	});
	
	$('#screen-container').on('popupclose', function(){
		var elID = $('#ib2-current-popup').val();
		if ( $('.ib2-pop-content').sortable("instance") )
			$('.ib2-pop-content').sortable("destroy");
			
		if ( $('.ib2-pop-content').resizable("instance") )
			$('.ib2-pop-content').resizable("destroy");
			
		$('.ib2-pop-content').find('.ib2-section-content').each(function(){
			var pc = $(this);
			if ( $(this).sortable("instance") )
				$(this).sortable("destroy");
				
			if ( $(this).hasClass('ib2-section-col') ) {
				colDestroy($(this).parent());
			}
		});
		
		$('#' + elID).removeClass('resize-border');
		$('#' + elID + '-edit-tools').remove();
		
		$('.ib2-popup-bg').fadeOut("medium");
		$('#' + elID).fadeOut("medium");
		
		colDestroy($('#' + elID + ' > .el-cols'));
		$is_popup = 0;
		
		$('body').trigger('popupclose');
		ib2_init();
	});
	
	$('.edit-target-popup').each(function(){
		$(this).click(function(e){
			var $this = $(this), popFrom = $this.data('popupFrom'),
			elID = $('#ib2-current-' + popFrom).val();
			
			if ( popFrom == 'popup' ) {
				if ( !$('#main-popup-enable').is(":checked") ) {
					alert('ERROR: Please enable the PopUp feature first to edit the PopUp content.');
					return false;
				}
				
				elID = $('#ib2-main-popup-id').val();
				if ( elID == '' ) {
					elID = 'ib2_el_' + generateID(8);
					$('#ib2-main-popup-id').val(elID);
				}
			}
			
			if ( $is_popup == 1 ) {
				alert('ERROR: Cannot call a Pop-up from another Pop-Up.');
				return false;
			}
			
			if ( !$('#' + elID + '-popup').length ) {
				var paddingLeft = $('#box-hp').val(),
				paddingTop = $('#box-vp').val(),
				boxPadding = paddingTop + 'px ' + paddingLeft + 'px';
				
				var s = '<div id="' + elID + '-popup" class="container ib2-section-el ib2-popup" style="width:700px; max-width:100%;" data-el="section" data-border-type="single" data-img-mode="upload">';
					s += '<div class="el-content el-cols" style="background-color:#FFFFFF;padding:' + boxPadding + '">';
						s += '<div id="' + elID + '-popup-box" class="ib2-pop-content" style="width:100%; min-height:400px;"></div>';
					s += '</div>';
					s += '<a href="#" class="ib2-close-pop"><img src="' + $('#ib2-img-folder').val() + 'pop-close.png" border="0" /></a>';
				s += '</div>';
				
				$('#screen-container').append(s);
			}
			
			$('#ib2-current-popup').val(elID + '-popup');
			
			ib2_popup_position($('#' + elID + '-popup'));
			
			if ( !$('.ib2-popup-bg').length )
				$('#screen-container').append('<div class="ib2-popup-bg"></div>');
				
			$('.ib2-popup-bg').show();
			$('#' + elID + '-popup').show();

			ib2_destroy();
			
			if ( $('#' + elID + '-popup').find('.ib2-section-col').length ) {
				colInit($('#' + elID + '-popup > .el-cols'));
			}
			
			$('.ib2-pop-content')
				.unbind('sortable')
				.sortable({
					update: ib2_element_drop,
					placeholder: 'sortable-line'
				});
			
			if ( $('.ib2-pop-content').find('.ib2-section-content').length ) {
				ib2_reinit_sortable($('.ib2-pop-content').find('.ib2-section-content'), 'content');
			}
			
			if ( $('.ib2-pop-content').find('.ib2-section-el').length ) {
				$('.ib2-pop-content').find('.ib2-section-el').each(function(i){
					var $pc = $(this);
					if ( $pc.find('> .el-cols').length ) {
						colInit($pc.find('> .el-cols'));
					}
				});
			}
			
			$is_popup = 1;
			
			// Disable undo/redo feature ...
			$("#ib2-undo").parent().addClass('disabled');
			$("#ib2-redo").parent().addClass('disabled');
			
			e.preventDefault();
		});
	});
	
	function ib2_popup_position( element ) {
		var pos  = 'absolute';
		var top  = (($(window).height() / 2) - (element.outerHeight() / 2));
		var left = (($('#screen-container').width() / 2) - (element.outerWidth() / 2));

		if ( top < 0 ) top = 0;
		if ( left < 0 ) left = 0;
	
		// IE6 fix
		if ( $.browser.msie && parseInt($.browser.version) <= 6 ) {
			top = top + $(window).scrollTop();
			pos = 'absolute';
		}

		element.css({
	    	'position' : pos,
	    	'top' : top,
	    	'left' : left
		});
	}
	
	function update_popup_position() {
		var elID = $('#ib2-current-popup').val(), el = $('#' + elID);
		
		if ( el.length && el.is(":visible") ) {
			ib2_popup_position(el);
		}
	}
	
	// =========================== IMAGE EDITOR ===========================
	
	function launchImageEditor(id, src) {
		if ( featherEditor ) {
			featherEditor.launch({
				image: id,
				url: src
			});
		}
		return false;
	}
   
	$('.open-image-editor').each(function(){
		$(this).click(function(e){
			var $this = $(this), element = $this.data('element');
			var pos = 'absolute';
			var top  = (($(window).height() / 2) - ($('#ib2-image-editor').outerHeight() / 2));
			var left = (($(window).width() / 2) - ($('#ib2-image-editor').outerWidth() / 2));

			if ( top < 0 ) top = 0;
			if ( left < 0 ) left = 0;
	
			// IE6 fix
			if ( $.browser.msie && parseInt($.browser.version) <= 6 ) {
				top = top + $(window).scrollTop();
				pos = 'absolute';
			}

			$('#ib2-image-editor').css({
		    	'position' : pos,
		    	'top' : top,
		    	'left' : left
			});
		
			$('#ib2-image-editor').show();
			$("#save-background-ui").show();
			
			var id, src;
			if ( element == 'screen-container' ) { 
				id = 'background-image-prev-img';
				src = $('#background-image-prev-img').attr('src');
				
				$('#ib2-imed-id').val('screen-container');
				$('#ib2-imed-type').val('background');
				
			} else if ( element == 'image' ) {
				var imgID = $('#ib2-current-image').val();
				if ( $('#' + imgID).find('img').attr('id') ) {
					id = $('#' + imgID).find('img').attr('id');
				} else {
					id = imgID + '-img';
					$('#' + imgID).find('img').attr('id', id);
				}
				src = $('#' + imgID).find('img').attr('src');
				
				$('#ib2-imed-id').val(imgID);
				$('#ib2-imed-type').val('image');

			} else if ( element == 'box' ) {
				var boxID = $('#ib2-current-box').val();
				
				id = 'box-image-prev-img';
				src = $('#box-image-prev-img').attr('src');
				
				$('#ib2-imed-id').val(boxID);
				$('#ib2-imed-type').val('background');
			}
			
			launchImageEditor(id, src);
			e.preventDefault();
		});
	});
	
	$('body').on('click', '.close-image-editor', function(e){
		$('#ib2-image-editor').hide();
		$("#save-background-ui").hide();
			
		e.preventDefault();
	});
	
	// =========================== DATE ===========================
	$('#date-format, #date-timezone').change(function(){
		var elID = $('#ib2-current-date').val(),
		opt = $('#date-format').val();
		
		var newDate = moment().tz($('#date-timezone').val());
		$('#' + elID).html(newDate.format(opt));
		
		$('#' + elID).data('format', opt);
		$('#' + elID).attr('data-format', opt);
		
		$('#' + elID).data('tz', $('#date-timezone').val());
		$('#' + elID).attr('data-tz', $('#date-timezone').val());
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#date-font-face').change(function(){
		var elID = $('#ib2-current-date').val(),
		opt = $("option:selected", this).val();
		
		$('#' + elID).css('font-family', opt);
		
		$("body").trigger("ib2CommonChange");
	});
	
	// =========================== COMMENT ===========================
	
	$('#comment-system').change(function(){
		var elID = $('#ib2-current-comment').val(),
		opt = $("option:selected", this).val();
		
		if ( opt == 'facebook' ) {
			$('#' + elID + ' > .el-content').html('<div id="' + elID + '-fbcom" class="fb-comment-left"><div class="fb-comment-right"></div></div>');
		} else if ( opt == 'disqus' ) {
			$('#' + elID + ' > .el-content').html('<div id="' + elID + '-disqus" class="disqus-left"><div class="disqus-right"></div></div>');
		}
		
		$('#' + elID).data('comment', opt);
		$('#' + elID).attr('data-comment', opt);
		
		$("body").trigger("ib2CommonChange");
	});
	
	// =========================== COUNTDOWN ===========================
	
	$('#screen-container').on('click', '.ib2-close-reveal-btn', function(e){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', '');
		
		$('#' + elID + '-content').hide();
		e.preventDefault();
	});
	
	$('#screen-container').on('click', '.ib2-show-reveal-btn', function(e){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', '');
		
		$('#' + elID + '-content').show();
		e.preventDefault();
	});
	
	$('body').on('change', '#countdown-date', function(){
		var offset = $('#countdown-tz').val(), elID = $('#ib2-current-countdown').val(),
		hour = ("0" + $('#countdown-hour').val()).slice(-2),
		min = ("0" + $('#countdown-min').val()).slice(-2),
		newdate = $(this).val() + ' ' + hour + ':' + min,
		origTime = moment.tz(newdate, offset),
				utc = origTime.clone().tz("UTC"),
				localTime = moment.utc(utc).toDate(),
				target = moment(localTime).format("YYYY/MM/DD HH:mm:ss");
		
		$('#' + elID).data('target', newdate);
		$('#' + elID).attr('data-target', newdate);
				
		if ( $('#countdown-style').val() == 'circular' ) {
			var format = circular_countdown_format(elID);
			$('#' + elID).html(format);

			if ( $('#' + elID).attr('data-day-start') )
				$('#' + elID).removeAttr('data-day-start');
				
			$('#' + elID).countdown(target)
				.on('update.countdown', function(event) {
					if ( !$('#' + elID).attr('data-day-start') ) {
						$('#' + elID).attr('data-day-start', event.offset.totalDays);
						$('#' + elID).data('dayStart', event.offset.totalDays);
					}
					
					// update time object
					circular_update_timer(elID, event.offset.totalDays, event.offset.hours, event.offset.minutes, event.offset.seconds);

					if ( layerMinutes[elID] ) {
				    	circular_draw(elID);
				    } else {
				    	create_circular_countdown(elID);
				    }
					
				});
		} else {
			var format = normal_countdown_format(elID, $('#countdown-style').val());
			$('#' + elID).html(format);

			$('#' + elID).countdown(target)
				.on('update.countdown', function(event) {
					normal_countdown_update(elID, event.offset.totalDays, event.offset.hours, event.offset.minutes, event.offset.seconds);
				});
		}
		
		setTimeout(function(){
			$('#' + elID).countdown('stop');
		}, 5000);
	});
	
	$('#countdown-action-type').change(function(){
		var elID = $('#ib2-current-countdown').val(),
		opt = $("option:selected", this).val();
		
		$('.expiry-action-redirect').hide();
		$('#' + elID + '-content').hide();
		if ( opt == 'redirect' ) {
			$('.expiry-action-redirect').show();
		} else if ( opt == 'reveal' ) {
			if ( !$('#' + elID + '-content').length ) {
				var reveal = $('<div>', { 'class': 'ib2-section-content ib2-countdown-reveal', 'id': elID + '-content' }),
				newID = 'ib2_el_' + generateID(8);
				
				reveal.insertAfter('#' + elID);
				reveal.append('<div id="' + newID + '" class="ib2-content-el ib2-text-el" data-el="text"><p>This is the hidden content that will be reveal after the countdown\'s end. You can modify this text, add more paragraph, change font style, etc. You can also add elements into this area.</p></div>');
			
				ib2_reinit_sortable($('.ib2-section-content'), 'content');
			} else {
				$('#' + elID + '-content').show();
			}
		}
		
		$('#' + elID).data('end', opt);
		$('#' + elID).attr('data-end', opt);
	});
	
	$('#countdown-url').keyup(function(){
		var elID = $('#ib2-current-countdown').val(),
		url = encodeURIComponent($(this).val());
		
		$('#' + elID).data('url', url);
		$('#' + elID).attr('data-url', url);
	});
	
	$('#countdown-text-before').blur(function(){
		var $this = $(this);
		if ( $('#countdown-style').val() == 'text' ) {
			var elID = $('#ib2-current-countdown').val(),
			text = $this.val() != '' ? encodeURIComponent($this.val()) : '';
			
			$('#' + elID).data('before', text);
			$('#' + elID).attr('data-before', text);
			
			if ( !$('#' + elID).find('.c-before-text').length ) {
				$('#' + elID).prepend('<span class="c-before-text"></span>');
			}
			
			var aText = $this.val() + ' ';
			$('#' + elID).find('.c-before-text').text(aText);
		}
	});
	
	$('#countdown-text-after').blur(function(){
		var $this = $(this);
		if ( $('#countdown-style').val() == 'text' ) {
			var elID = $('#ib2-current-countdown').val(),
			text = $this.val() != '' ? encodeURIComponent($this.val()) : '';
			
			$('#' + elID).data('after', text);
			$('#' + elID).attr('data-after', text);
			
			if ( text == '' ) return;
			
			if ( !$('#' + elID).find('.c-after-text').length ) {
				$('#' + elID).append('<span class="c-after-text"></span>');
			}
			
			var bText = ' ' + $this.val();
			$('#' + elID).find('.c-after-text').text(bText);
		}
	});
	
	$('#countdown-text-font').change(function(){
		var opt = $("option:selected", this).val(),
		elID = $('#ib2-current-countdown').val();
		
		$('#' + elID).css('font-family', opt);
		
		$("body").trigger("ib2CommonChange");
	});
	
	$('#countdown-type, #countdown-tz').change(function(){
		var elID = $('#ib2-current-countdown').val(),
		date = $('#countdown-date').val(),
		day = $('#countdown-day').val(),
		hour = $('#countdown-hour').val(),
		min = $('#countdown-min').val();

		$('#' + elID).countdown('remove');
		$('#' + elID).html('');
		
		if ( $('#countdown-type').val() == 'date' ) {
			$('.c-date-type').show();
			$('.c-non-date-type').hide();
			
			hour = ("0" + hour).slice(-2);
			min = ("0" + min).slice(-2);
			
			var offset = $('#countdown-tz').val(), elID = $('#ib2-current-countdown').val(),
			newdate = $('#countdown-date').val() + ' ' + hour + ':' + min,
			origTime = moment.tz(newdate, offset),
				utc = origTime.clone().tz("UTC"),
				localTime = moment.utc(utc).toDate(),
				target = moment(localTime).format("YYYY/MM/DD HH:mm:ss");
			
			$('#' + elID).data('target', newdate);
			$('#' + elID).attr('data-target', newdate);
			
		} else {
			
			if ( $('#countdown-type').val() == 'evergreen' ) {
				$('#' + elID + '-content').hide();
			}
			
			$('.c-date-type').hide();
			$('.c-non-date-type').show();
				
			day = day * 24 * 60 * 60 * 1000;
			hour = hour * 60 * 60 * 1000;
			min = min * 60 * 1000;
				
			var newValue = day + hour + min, target = new Date().valueOf() + newValue;
			
			$('#' + elID).data('target', day + ':' + hour + ':' + min);
			$('#' + elID).attr('data-target', day + ':' + hour + ':' + min);
		}
		
		if ( $('#countdown-style').val() == 'circular' ) {
			var format = circular_countdown_format(elID);
			
			$('#' + elID).html(format);
			create_circular_countdown(elID);
			
			if ( $('#' + elID).attr('data-day-start') )
				$('#' + elID).removeAttr('data-day-start');
				
			circular_update_timer(elID, 0, 0, 0, 0);
			$('#' + elID).countdown(target)
				.on('update.countdown', function(event) {
					if ( !$('#' + elID).attr('data-day-start') ) {
						$('#' + elID).attr('data-day-start', event.offset.totalDays);
						$('#' + elID).data('dayStart', event.offset.totalDays);
					}
					
					// update time object
					circular_update_timer(elID, event.offset.totalDays, event.offset.hours, event.offset.minutes, event.offset.seconds);

					if ( layerMinutes[elID] ) {
				    	circular_draw(elID);
				    } else {
				    	create_circular_countdown(elID);
				    }
					
				});
		} else {
			var format = normal_countdown_format(elID, $('#countdown-style').val());
			$('#' + elID).html(format);

			$('#' + elID).countdown(target)
				.on('update.countdown', function(event) {
					normal_countdown_update(elID, event.offset.totalDays, event.offset.hours, event.offset.minutes, event.offset.seconds);
				});
		}
		
		setTimeout(function(){
			$('#' + elID).countdown('stop');
		}, 6000);
		
		$('#' + elID).data('mode', $('#countdown-type').val());
		$('#' + elID).attr('data-mode', $('#countdown-type').val());
		$('#' + elID).data('tz', $('#countdown-tz').val());
		$('#' + elID).attr('data-tz', $('#countdown-tz').val());
	});

	$('#countdown-style').change(function(){
		var opt = $("option:selected", this).val(),
		elID = $('#ib2-current-countdown').val();

		$('#' + elID).removeClass('ib2-countdown-style');
		if ( opt != 'text' ) {
			$('#' + elID).css('font-size', $('#countdown-font-size').val() + 'px');
			$('#' + elID).addClass('ib2-countdown-style');
			if ( opt == 'glossy-box' )
				$('#' + elID).addClass('ib2-countdown-shadow');
			else
				$('#' + elID).removeClass('ib2-countdown-shadow');
				$('.countdown-text-group').hide();
		} else {
			$('#' + elID).css('font-size', $('#countdown-font-size').val() + 'px');
			$('#' + elID).removeClass('ib2-countdown-style');
			$('#' + elID).removeClass('ib2-countdown-shadow');
			
			$('.countdown-text-group').show();
		}
		
		$('.countdown-box-style').hide();
		if ( opt == 'fancy-text' ) {
			if ( $('#' + elID + '-countdown-css').length )
				$('#' + elID + '-countdown-css').html('');
		} else {
			var color = $('#countdown-color').val(),
	    	borderColor = $('#countdown-border-color').val();
	    	
	    	var css = '';
	    	if ( opt == 'flat-box' ) {
	    		$('.countdown-box-style').show();
		        css += '#' + elID + '.ib2-countdown-style > .ib2-digit { ';
		        css += 'background-color: ' + color + '; ';
		        css += 'background-image: none; ';
		        css += 'border-width: ' + $('#countdown-border-thick').val() + 'px; ';
		        css += 'border-style: ' + $('#countdown-border-style').val() + '; ';
		        css += 'border-color: ' + borderColor + '; }';
			} else if ( opt == 'glossy-box' ) {
				$('.countdown-box-style').show();
				var glossyColor = getAltColor(color, 'lighter');
				css += '#' + elID + '.ib2-countdown-style > .ib2-digit { ';
		        css += 'background-color: ' + color + '; ';
		        css += 'background-image: linear-gradient(to bottom, ' + glossyColor + ' 5%, ' + color + ' 100%); ';
		        css += 'background-image: -moz-linear-gradient(top, ' + glossyColor + ' 5%, ' + color + ' 100%); ';
		        css += 'border-width: ' + $('#countdown-border-thick').val() + 'px; ';
		        css += 'border-style: ' + $('#countdown-border-style').val() + '; ';
		        css += 'border-color: ' + borderColor + '; }';
			}
			
			$('#' + elID + '-countdown-css').html(css);
		}
		
		$('#' + elID).data('style', opt);
		$('#' + elID).attr('data-style', opt);
		
		if ( opt == 'circular' )
			$('#' + elID).addClass('ib2-circular-countdown');
		else
			$('#' + elID).removeClass('ib2-circular-countdown');
			
		$('#countdown-type').trigger('change');
	});
	
	$('#countdown-border-style').change(function(){
		var elID = $('#ib2-current-countdown').val(),
    	el = $('#' + elID), color = $('#countdown-color').val(),
    	opt = $("option:selected", this).val();
    	
    	var css = '';
    	if ( $('#countdown-style').val() == 'flat-box' ) {
	        css += '#' + elID + '.ib2-countdown-style > .ib2-digit { ';
	        css += 'background-color: ' + color + '; ';
	        css += 'background-image: none; ';
	        css += 'border-width: ' + $('#countdown-border-thick').val() + 'px; ';
	        css += 'border-style: ' + opt + '; ';
	        css += 'border-color: ' + $('#countdown-border-color').val() + '; }';
		} else if ( $('#countdown-style').val() == 'glossy-box' ) {
			var glossyColor = getAltColor(color, 'lighter');
			css += '#' + elID + '.ib2-countdown-style > .ib2-digit { ';
	        css += 'background-color: ' + color + '; ';
	        css += 'background-image: linear-gradient(to bottom, ' + glossyColor + ' 5%, ' + color + ' 100%); ';
	        css += 'background-image: -moz-linear-gradient(top, ' + glossyColor + ' 5%, ' + color + ' 100%); ';
	        css += 'border-width: ' + $('#countdown-border-thick').val() + 'px; ';
	        css += 'border-style: ' + opt + '; ';
	        css += 'border-color: ' + $('#countdown-border-color').val() + '; }';
		}
		
		$('#' + elID + '-countdown-css').html(css);
		
		$("body").trigger("ib2CommonChange");
	});
	
	function normal_countdown_format( elID, style ) {
		var format = '';
		format += '<span class="ib2-digit"><span id="' + elID + '-tdays">0</span><span class="ib2-digit-txt"> day </span></span>';
		format += '<span class="ib2-digit"><span id="' + elID + '-thours">0</span><span class="ib2-digit-txt"> hour </span></span>';
		format += '<span class="ib2-digit"><span id="' + elID + '-tminutes">0</span><span class="ib2-digit-txt"> minute </span></span>';
		format += '<span class="ib2-digit"><span id="' + elID + '-tseconds">0</span><span class="ib2-digit-txt"> second </span></span>';
					
		var beforeText = '', afterText = '';
		if ( style == 'text' && $('#' + elID).data('before') != '' ) {
			var btext = decodeURIComponent($('#' + elID).data('before'));
			beforeText = '<span class="c-before-text"></span>' + btext + ' ';
			
			format = beforeText + format;
		}
		
		if ( style == 'text' && $('#' + elID).data('after') != '' ) {
			var atext = decodeURIComponent($('#' + elID).data('after'));
			afterText = ' <span class="c-after-text"></span>' + atext;
			format = format + afterText;
		}
		
		return format;
	}
	
	function normal_countdown_update( elID, days, hours, minutes, seconds ) {
		if ( $('#' + elID + '-tseconds').length ) {
			$('#' + elID + '-tseconds').text(seconds);
			$('#' + elID + '-tminutes').text(minutes);
			$('#' + elID + '-thours').text(hours);
			$('#' + elID + '-tdays').text(days);
			
			if ( seconds > 1 )
				$('#' + elID + '-tseconds').next('.ib2-digit-txt').text(' seconds ');
			else
				$('#' + elID + '-tseconds').next('.ib2-digit-txt').text(' second ');
				
			if ( minutes > 1 )
				$('#' + elID + '-tminutes').next('.ib2-digit-txt').text(' minutes ');
			else
				$('#' + elID + '-tminutes').next('.ib2-digit-txt').text(' minute ');
				
			if ( hours > 1 )
				$('#' + elID + '-thours').next('.ib2-digit-txt').text(' hours ');
			else
				$('#' + elID + '-thours').next('.ib2-digit-txt').text(' hour ');
				
			if ( days > 1 )
				$('#' + elID + '-tdays').next('.ib2-digit-txt').text(' days ');
			else
				$('#' + elID + '-tdays').next('.ib2-digit-txt').text(' day ');
		}
	}
	
	// CIRCULAR COUNTDOWN
    /*!
	 * CREDIT
	 * jQuery Final Countdown
	 *
	 * @author Pragmatic Mates, http://pragmaticmates.com
	 * @version 1.1.1
	 * @license GPL 2
	 * @link https://github.com/PragmaticMates/jquery-final-countdown
	 */

    function convertToDeg( degree ) {
        return (Math.PI/180) * degree - (Math.PI/180) * 90;
    }
    
    function create_circular_countdown( elID ) {
    	var borderWidth = 10;
		
		if ( !circularTime[elID] || circularTime[elID] == null || typeof circularTime[elID] === "undefined" ) return false;
		
		// Seconds BG
        var seconds_bg_width = $('#' + elID + '-bg-seconds').width();
        var secondsBgStage = new Kinetic.Stage({
            container: elID + '-bg-seconds',
            width: seconds_bg_width,
            height: seconds_bg_width
        });

        circleBgSeconds[elID] = new Kinetic.Shape({
            drawFunc: function(context) {
                var seconds_bg_width = $('#' + elID + '-bg-seconds').width();
                var radius = seconds_bg_width / 2 - borderWidth / 2;
                var x = seconds_bg_width / 2;
                var y = seconds_bg_width / 2;

                context.beginPath();
                context.arc(x, y, radius, convertToDeg(0), convertToDeg(360));
                context.fillStrokeShape(this);
            },
            stroke: '#c2c2c2',
            strokeWidth: borderWidth
        });
        
        layerBgSeconds[elID] = new Kinetic.Layer();
        layerBgSeconds[elID].add(circleBgSeconds[elID]);
        secondsBgStage.add(layerBgSeconds[elID]);
        
        // Seconds
        var seconds_width = $('#' + elID + '-seconds').width();
        var secondsStage = new Kinetic.Stage({
            container: elID + '-seconds',
            width: seconds_width,
            height: seconds_width
        });

        circleSeconds[elID] = new Kinetic.Shape({
            drawFunc: function(context) {
                var seconds_width = $('#' + elID + '-seconds').width();
                var radius = seconds_width / 2 - borderWidth / 2;
                var x = seconds_width / 2;
                var y = seconds_width / 2;

                context.beginPath();
                context.arc(x, y, radius, convertToDeg(0), convertToDeg(360 - (circularTime[elID].secs * 6)));
                context.fillStrokeShape(this);
            },
            stroke: '#E7B708',
            strokeWidth: borderWidth
        });

        layerSeconds[elID] = new Kinetic.Layer();
        layerSeconds[elID].add(circleSeconds[elID]);
        secondsStage.add(layerSeconds[elID]);

		// Minutes Background
        var minutes_bg_width = $('#' + elID + '-bg-minutes').width();
        var minutesBgStage = new Kinetic.Stage({
            container: elID + '-bg-minutes',
            width: minutes_bg_width,
            height: minutes_bg_width
        });

        circleBgMinutes[elID] = new Kinetic.Shape({
            drawFunc: function(context) {
                var minutes_bg_width = $('#' + elID + '-bg-minutes').width();
                var radius = minutes_bg_width / 2 - borderWidth / 2;
                var x = minutes_bg_width / 2;
                var y = minutes_bg_width / 2;

                context.beginPath();
                context.arc(x, y, radius, convertToDeg(0), convertToDeg(360));
                context.fillStrokeShape(this);
            },
            stroke: '#c2c2c2',
            strokeWidth: borderWidth
        });

        layerBgMinutes[elID] = new Kinetic.Layer();
        layerBgMinutes[elID].add(circleBgMinutes[elID]);
        minutesBgStage.add(layerBgMinutes[elID]);
        
        // Minutes
        var minutes_width = $('#' + elID + '-minutes').width();
        var minutesStage = new Kinetic.Stage({
            container: elID + '-minutes',
            width: minutes_width,
            height: minutes_width
        });

        circleMinutes[elID] = new Kinetic.Shape({
            drawFunc: function(context) {
                var minutes_width = $('#' + elID + '-minutes').width();
                var radius = minutes_width / 2 - borderWidth / 2;
                var x = minutes_width / 2;
                var y = minutes_width / 2;

                context.beginPath();
                context.arc(x, y, radius, convertToDeg(0), convertToDeg(360 - (circularTime[elID].mins * 6)));
                context.fillStrokeShape(this);
            },
            stroke: '#ACC742',
            strokeWidth: borderWidth
        });

        layerMinutes[elID] = new Kinetic.Layer();
        layerMinutes[elID].add(circleMinutes[elID]);
        minutesStage.add(layerMinutes[elID]);
		
		// Hours Background
        var hours_bg_width = $('#' + elID + '-bg-hours').width();
        var hoursBgStage = new Kinetic.Stage({
            container: elID + '-bg-hours',
            width: hours_bg_width,
            height: hours_bg_width
        });

        circleBgHours[elID] = new Kinetic.Shape({
            drawFunc: function(context) {
                var hours_bg_width = $('#' + elID + '-bg-hours').width();
                var radius = hours_bg_width / 2 - borderWidth/2;
                var x = hours_bg_width / 2;
                var y = hours_bg_width / 2;

                context.beginPath();
                context.arc(x, y, radius, convertToDeg(0), convertToDeg(360));
                context.fillStrokeShape(this);
            },
            stroke: '#c2c2c2',
            strokeWidth: borderWidth
        });

        layerBgHours[elID] = new Kinetic.Layer();
        layerBgHours[elID].add(circleBgHours[elID]);
        hoursBgStage.add(layerBgHours[elID]);
        
        // Hours
        var hours_width = $('#' + elID + '-hours').width();
        var hoursStage = new Kinetic.Stage({
            container: elID + '-hours',
            width: hours_width,
            height: hours_width
        });

        circleHours[elID] = new Kinetic.Shape({
            drawFunc: function(context) {
                var hours_width = $('#' + elID + '-hours').width();
                var radius = hours_width / 2 - borderWidth/2;
                var x = hours_width / 2;
                var y = hours_width / 2;

                context.beginPath();
                context.arc(x, y, radius, convertToDeg(0), convertToDeg(350 - (circularTime[elID].hours * 360 / 24)));
                context.fillStrokeShape(this);
            },
            stroke: '#7995D5',
            strokeWidth: borderWidth
        });

        layerHours[elID] = new Kinetic.Layer();
        layerHours[elID].add(circleHours[elID]);
        hoursStage.add(layerHours[elID]);

		// Days Background
        var days_bg_width = $('#' + elID + '-bg-days').width();
        var daysBgStage = new Kinetic.Stage({
            container: elID + '-bg-days',
            width: days_bg_width,
            height: days_bg_width
        });

        circleBgDays[elID] = new Kinetic.Shape({
            drawFunc: function(context) {
                var days_bg_width = $('#' + elID + '-bg-days').width();
                var radius = days_bg_width/2 - borderWidth/2;
                var x = days_bg_width / 2;
                var y = days_bg_width / 2;

                context.beginPath();
                context.arc(x, y, radius, convertToDeg(0), convertToDeg(360));
                context.fillStrokeShape(this);
            },
            stroke: '#c2c2c2',
            strokeWidth: borderWidth
        });

        layerBgDays[elID] = new Kinetic.Layer();
        layerBgDays[elID].add(circleBgDays[elID]);
        daysBgStage.add(layerBgDays[elID]);
        
        // Days
        var days_width = $('#' + elID + '-days').width();
        var daysStage = new Kinetic.Stage({
            container: elID + '-days',
            width: days_width,
            height: days_width
        });

		var firstDay = circularTime[elID].days;
		if ( $('#' + elID).attr('data-day-start') ) {
			firstDay = parseInt($('#' + elID).data('dayStart'));
		} else {
			$('#' + elID).attr('data-day-start', circularTime[elID].days);
			$('#' + elID).data('dayStart', circularTime[elID].days);
		}
		
        circleDays[elID] = new Kinetic.Shape({
            drawFunc: function(context) {
                var days_width = $('#' + elID + '-days').width();
                var radius = days_width/2 - borderWidth/2;
                var x = days_width / 2;
                var y = days_width / 2;

                context.beginPath();
                if ( circularTime[elID].days == 0 ) {
                    context.arc(x, y, radius, convertToDeg(0), convertToDeg(0));
                } else {
                	var dayLeft = 360 - ((360 / firstDay) * (firstDay - circularTime[elID].days));
                	if ( dayLeft == 0 )
                		dayLeft = 360;
                    context.arc(x, y, radius, convertToDeg(0), convertToDeg(dayLeft));
                }
                context.fillStrokeShape(this);
            },
            stroke: '#E16464',
            strokeWidth: borderWidth
        });

        layerDays[elID] = new Kinetic.Layer();
        layerDays[elID].add(circleDays[elID]);
        daysStage.add(layerDays[elID]);
    }
    
    function circular_draw( elID ) {
    	if ( circularTime[elID] ) {
    		
    		if ( $('#' + elID + '-days').length && $('#' + elID + '-days').width() > 0 )
	            layerDays[elID].draw();
			
			if ( $('#' + elID + '-hours').length && $('#' + elID + '-hours').width() > 0 )
				layerHours[elID].draw();
	
			if ( $('#' + elID + '-minutes').length && $('#' + elID + '-minutes').width() > 0 )
				layerMinutes[elID].draw();
    
    		if ( $('#' + elID + '-seconds').length && $('#' + elID + '-seconds').width() > 0 )
	            layerSeconds[elID].draw();
		}
    }
    
    function circular_bg_draw( elID ) {
    	if ( circularTime[elID] ) {
    		
    		if ( $('#' + elID + '-days').length && $('#' + elID + '-days').width() > 0 )
	            layerBgDays[elID].draw();
			
			if ( $('#' + elID + '-hours').length && $('#' + elID + '-hours').width() > 0 )
				layerBgHours[elID].draw();
	
			if ( $('#' + elID + '-minutes').length && $('#' + elID + '-minutes').width() > 0 )
				layerBgMinutes[elID].draw();
    
    		if ( $('#' + elID + '-seconds').length && $('#' + elID + '-seconds').width() > 0 )
	            layerBgSeconds[elID].draw();
		}
    }
    
    function circular_update_timer( elID, days, hours, mins, secs ) {
    	if ( !circularTime[elID] || circularTime[elID] == null || typeof circularTime[elID] === "undefined" ) {
			circularTime[elID] = {};
    	}
    							
    	circularTime[elID].secs = secs;
    	circularTime[elID].hours = hours;
    	circularTime[elID].mins = mins;
    	circularTime[elID].days = days;
    	
    	if ( $('#' + elID + '-seconds').length ) {
	    	$('#' + elID + '-seconds').next('.text').find('.val').text(secs);
		    $('#' + elID + '-minutes').next('.text').find('.val').text(mins);
		    $('#' + elID + '-hours').next('.text').find('.val').text(hours);
		    $('#' + elID + '-days').next('.text').find('.val').text(days);
		}
    }
    
    function circular_countdown_format( elID ) {
    	var format = '';
    	format += '<div class="clock row">';
			// Days
			format += '<div class="clock-item clock-days countdown-time-value col-sm-6 col-md-3">';
				format += '<div class="wrap">';
					format += '<div class="inner">';
						format += '<div id="' + elID + '-bg-days" class="clock-background"></div>';
						format += '<div id="' + elID + '-days" class="clock-canvas"></div>';
						format += '<div class="text">';
							format += '<p class="val">0</p>';
							format += '<p class="type-days type-time">day(s)</p>';
						format += '</div>';
					format += '</div>';
				format += '</div>';
			format += '</div>';
			
			// Hours
			format += '<div class="clock-item clock-hours countdown-time-value col-sm-6 col-md-3">';
				format += '<div class="wrap">';
					format += '<div class="inner">';
						format += '<div id="' + elID + '-bg-hours" class="clock-background"></div>';
						format += '<div id="' + elID + '-hours" class="clock-canvas"></div>';
						format += '<div class="text">';
							format += '<p class="val">0</p>';
							format += '<p class="type-hours type-time">hour(s)</p>';
						format += '</div>';
					format += '</div>';
				format += '</div>';
			format += '</div>';
		
			// Minutes
			format += '<div class="clock-item clock-minutes countdown-time-value col-sm-6 col-md-3">';
				format += '<div class="wrap">';
					format += '<div class="inner">';
						format += '<div id="' + elID + '-bg-minutes" class="clock-background"></div>';
						format += '<div id="' + elID + '-minutes" class="clock-canvas"></div>';
						format += '<div class="text">';
							format += '<p class="val">0</p>';
							format += '<p class="type-minutes type-time">minute(s)</p>';
						format += '</div>';
					format += '</div>';
				format += '</div>';
			format += '</div>';
			
			// Seconds
			format += '<div class="clock-item clock-seconds countdown-time-value col-sm-6 col-md-3">';
				format += '<div class="wrap">';
					format += '<div class="inner">';
						format += '<div id="' + elID + '-bg-seconds" class="clock-background"></div>';
						format += '<div id="' + elID + '-seconds" class="clock-canvas"></div>';
						format += '<div class="text">';
							format += '<p class="val">0</p>';
							format += '<p class="type-seconds type-time">second(s)</p>';
						format += '</div>';
					format += '</div>';
				format += '</div>';
			format += '</div>';
			
		format += '</div>';
		
		return format;					
    }
    
	// =========================== SCREEN SWITCHER ===========================
	
	$('.switch-screen').each(function(){
		$(this).click(function(e){
			var $this = $(this), mode = $this.data('mode');
			$('#current-screen-view').attr('class', 'fa fa-' + mode + ' fa-2x');
			$('.screen-view-list > li').show();
			$this.parent().hide();
			
			if ( mode == 'tablet' ) {
				$('#screen-container').removeClass('col-md-12');
				$('#screen-container').css({
					'width': '767px',
					'margin-left': 'auto',
					'margin-right': 'auto'
				});
				$('#ib2-page-guide-l, #ib2-page-guide-r').hide();
			} else if ( mode == 'mobile' ) {
				$('#screen-container').removeClass('col-md-12');
				$('#ib2-page-guide-l, #ib2-page-guide-r').hide();
				$('#screen-container').css({
					'width': '480px',
					'margin-left': 'auto',
					'margin-right': 'auto'
				});
			} else {
				$('#screen-container').addClass('col-md-12');
				$('#ib2-page-guide-l, #ib2-page-guide-r').show();
				$('#screen-container').css({
					'width': '100%',
					'margin-left': 0,
					'margin-right': 0
				});
			}
			e.preventDefault();
		});
	});
	
	// =========================== VARIATION ===========================
	$('.ib2-variant-weight').each(function(){
		$(this).bind('keyup', function(){
			var $this = $(this), num = $this.val();
			
			if ( !isNumber(num) ) {
				alert('Please enter a number');
				return false;
			}
			
			if ( num % 1 != 0 ) {
				$this.val(Math.round(num));
			}
			
			var total = 0;
			$('.ib2-variant-weight').each(function(){
				total += parseInt($(this).val());
			});
			
			$('#weight-diff').html('');
			if ( total > 100 ) {
				var diff = total - 100, newNum = num - diff;
				
				if ( newNum < 0 ) newNum = 0;
				
				$this.val(newNum);
			}
			
			if ( total < 100 ) {
				var diff = 100 - total, remaining = diff;
				
				$('#weight-diff').html('Please assign the <strong>remaining ' + remaining + '%</strong> to avoid rotation error.');
			}
		});
	});
	
	$('#conversion-page-type').change(function(){
		var opt = $("option:selected", this).val();
		if ( opt == 'wp' ) {
			$('.wp-thank-pages').show();
			$('.ex-thank-pages').hide();
		} else {
			$('.wp-thank-pages').hide();
			$('.ex-thank-pages').show();
		}
	}).change();
	
	$('body').on('click', '#variant-create', function(e){
		var $this = $(this);
		
		exitingEditor = true;
		save_page($this);
	});
	
	$('body').on('click', '#restore_prev_save', function(e){
		var $this = $(this);
		if ( confirm('Are you sure want to restore the content from previous save?\nThis action cannot be undone.') ) {
			exitingEditor = true;
			return true;
		} else {
			return false;
		}
	});
	
	$('body').on('click', '.ib2-create-var-link', function(e){
		var $this = $(this);
		
		exitingEditor = true;
		save_page($this);
		e.preventDefault();
	});
	
	$('#new-split-variant').click(function(e){
		$('#variant-type').val('duplicate');
		$('#ib2-templates').hide();
		$('#ib2-variation-modal').modal({
			backdrop: true,
			show: true
		});
		
		changeTemplate = 0;
		e.preventDefault();	
	});
	
	$('body').on('change', '#variant-type', function(){
		var opt = $("option:selected", this).val();
		$('#variant-create').data('mode', opt);
		$('#variant-create').attr('data-mode', opt);
			
		if ( opt == 'template' ) {
			$('#ib2-templates').show();
			$('#non-templates-area').hide();
			
			$('#ib2-templates > ul.nav-pills > li.active > a').trigger('click');
		} else {
			$('#ib2-templates').hide();
			$('#non-templates-area').show();
		}
	});
	
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
				
	$('body').on('click', '.ib2-tmpl-type', function(e){
		var $this = $(this), type = $this.data('type'),
		text = $this.text(), keyword = '',
		mode = ( $this.parent().parent().hasClass('new-variant-type') ) ? 'variant' : 'change';
		
		$this.parent().parent().find('.active').removeClass('active');
		$this.parent().addClass('active');
		
		$this.parents('.ib2-tmpls').find('.ib2-templates-area').find('h3').text(text + ' Templates');
		$this.parents('.ib2-tmpls').find('.ib2-template-loader').show();
		$this.parents('.ib2-tmpls').find('.ib2-templates-content').html('');

		var subtype_field = $('#ib2-tmpl-subtype');
		if ( mode == 'variant' ) {
			subtype_field = $('#ib2-tmpl-subtype');
			keyword = $('#ib2-tmpl-tags').val();
		} else {
			subtype_field = $('#ib2-tmpl-subtype2');
			keyword = $('#ib2-tmpl-tags2').val();
		}
		
		var newoptions = '<option value=""> -- Sub-Type -- </option>';
		newoptions += subtype_data[type];
		
		subtype_field.html(newoptions);
		
		$.post(ajaxurl, {
			action: 'ib2_show_templates',
			type: type,
			subtype: '',
			keyword: keyword
		}, function(response){
			$this.parents('.ib2-tmpls').find('.ib2-templates-content').html(response);
			$this.parents('.ib2-tmpls').find('.ib2-template-loader').hide();
		});
		
		e.preventDefault();
	});
	
	$('body').on('change', '#ib2-tmpl-subtype, #ib2-tmpl-subtype2', function(){
		var $this = $("option:selected", this), subtype = $this.val(),
		id = $(this).attr('id'), type = 'sales',
		keyword = ( id == 'ib2-tmpl-subtype2' ? $('#ib2-tmpl-tags2').val() : $('#ib2-tmpl-tags').val() );
		
		if ( id == 'ib2-tmpl-subtype2' )
			type = $('.change-template-type').find('li.active > a').data('type');
		else
			type = $('.new-variant-type').find('li.active > a').data('type');
			
		$('.ib2-template-loader').show();
		$('.ib2-templates-content').html('');
		
		$.post(ajaxurl, {
			action: 'ib2_show_templates',
			type: type,
			subtype: subtype,
			keyword: keyword
		}, function(response){
			$('.ib2-templates-content').html(response);
			$('.ib2-template-loader').hide();
		});
	});
	
	$('body').on('submit', 'form#template-search-form, form#template-search-form2', function(e){
		e.preventDefault();
		
		var $this = $(this), subtype = $this.find('select').val(),
		id = $(this).attr('id'), type = 'sales',
		keyword = $this.find('input[type=text]').val();
		
		if ( id == 'template-search-form2' )
			type = $('.change-template-type').find('li.active > a').data('type');
		else
			type = $('.new-variant-type').find('li.active > a').data('type');
			
		$('.ib2-template-loader').show();
		$('.ib2-templates-content').html('');
		
		$.post(ajaxurl, {
			action: 'ib2_show_templates',
			type: type,
			subtype: subtype,
			keyword: keyword
		}, function(response){
			$('.ib2-templates-content').html(response);
			$('.ib2-template-loader').hide();
		});
	});
	
	$('body').on('mouseenter', '.ib2-tmpl-thumb', function(){
		var $this = $(this);
		
		if ( !$this.find('.ib2-select-tmpl').length && !$this.find('.ib2-choose-bg').length ) {
			
			$this.append('<div class="ib2-choose-bg"></div>');
			
			if ( changeTemplate == 1 )
				$this.append('<a href="' + $('#ib2-admin-url').val() + '?post_id=' + $('#ib2-post-id').val() + '&variant=' + $('#ib2-current-variation').val() + '&ib2action=change_template&template_id=' + $this.data('id') + '" class="btn btn-primary ib2-choose-tmpl ">Choose</a>');
			else
				$this.append('<a href="#" class="btn btn-primary ib2-choose-tmpl ib2-create-var-link" data-tid="' + $this.data('id') + '" data-status="variant" data-mode="template">Choose</a>');
			
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
				'top': top,
				'z-index': '10'
			});
		} else {
			$this.find('.ib2-choose-bg').show();
			$this.find('.ib2-choose-tmpl').show();
		}
	});
	
	$('body').on('mouseleave', '.ib2-tmpl-thumb', function(){
		var $this = $(this);
		$this.find('.ib2-choose-tmpl').remove();
		$this.find('.ib2-choose-bg').remove();
	});
	
	$('body').on('click', '.ib2-choose-tmpl', function(){
		exitingEditor = true;
		return true;	
	});
	
	// =========================== SAVE PAGE ===========================
	
	$('body').on('click', '#template-thumb', function(e){
		ib2media('template-thumb-url', 'template');
		
		e.preventDefault();
	});
	
	$('body').on('click', '#remove-template-thumb', function(e){
		$('#template-thumb-preview').html('');
		$('#template-thumb-url').val('');
		$(this).hide();
		e.preventDefault();
	});
	
	$('.save-as-tmpl').click(function(e){
		$('#ib2-template-modal').modal({
			backdrop: true,
			show: true
		});
		
		e.preventDefault();
	});
	
	$('body').on('change', '#template-type', function(){
		var $this = $("option:selected", this), type = $this.val();
		
		var newoptions = '<option value=""> -- Sub-Type -- </option>';
		newoptions += subtype_data[type];
		
		$('#template-subtype').html(newoptions);
	});
	
	$('body').on('keypress', '#template-name', function(){
		if ( $('#save-template-id').val() == 0 ) {
			$('#tmpl-duplicate-msg').hide();
			$('#save-template-id').val(0);
		}
	});
	
	$('body').on('change', '#template-type', function(){
		if ( $('#save-template-id').val() == 0 ) {
			$('#tmpl-duplicate-msg').hide();
			$('#save-template-id').val(0);
		}
	});
	
	$('body').on('click', '#template-save', function(e){
		var $this = $(this);
		if ( $('#template-name').val() == '' ) {
			$('#template-name').parent().addClass('has-error');
			$('#template-name').parent().find('.text-danger').text('ERROR: Please enter a template name.');
			return false;
		} else {
			$('#template-name').parent().removeClass('has-error');
			$('#template-name').parent().find('.text-danger').text('');
		}
			
		if( /[^a-zA-Z0-9_]/.test($('#template-name').val()) ) {
		    $('#template-name').parent().addClass('has-error');
			$('#template-name').parent().find('.text-danger').text('ERROR: Please enter only letters, numbers and underscores. No spaces.');
			return false;
		} else {
			$('#template-name').parent().removeClass('has-error');
			$('#template-name').parent().find('.text-danger').text('');
		}
			
		$this.button('loading');
		if ( $('#save-template-id').val() == 0 ) {
			// check for duplicated template...
			$.post(ajaxurl,
				{
					action: 'ib2_check_tmpl',
					name: $('#template-name').val(),
					type: $('#template-type').val(),
				},
				function( response ) {
					if ( response != 'none') {
						$('#tmpl-duplicate-msg').show();
						$('#save-template-id').val(response);
						$this.button('reset');
					} else {
						$('#ib2-template-modal').modal("hide");
						save_page($this);
					}
				}
			);
		} else {
			$('#ib2-template-modal').modal("hide");
			save_page($this);
		}
		
		e.preventDefault();
	});
	
	$('body').on('ib2autosave', function(){
		if ( autosave != 1 || $is_popup == 1 || $is_text_edit == 1 ) {
			return false;
		}
		
		if ( $('#editor-panel-inside').is(":visible") ) return false;
		
		var currentstamp = Math.floor(Date.now() / 1000),
		as_interval = currentstamp - lastautosave;
		
		if ( as_interval < autosave_interval ) {
			return false;
		}
		
		$(this).data('status', 'autosave');
		save_page($(this));
		
		lastautosave = currentstamp;
	});
	
	$('.ib2-page-save').each(function(){
		$(this).click(function(e){
			var $this = $(this);
			
			save_page($this);
			
			e.preventDefault();
		});
	});
	
	function save_loader() {
		var pos  = 'fixed';
		var top  = (($(window).height() / 2) - ($('#save-loader-ui').outerHeight() / 2));
		var left = (($(window).width() / 2) - ($('#save-loader-ui').outerWidth() / 2));

		if ( top < 0 ) top = 0;
		if ( left < 0 ) left = 0;
	
		// IE6 fix
		if( $.browser.msie && parseInt($.browser.version) <= 6 ) {
			top = top + $(window).scrollTop();
			pos = 'absolute';
		}
	
		$('#save-loader-ui').css({
	    	'position' : pos,
	    	'top' : top,
	    	'left' : left,
			'z-index': '999999'
		});
		
		$('#save-background-ui').show();
		$('#save-loader-ui').show();
	}
	
	function save_page( element ) {
		$('#autosave-status').removeClass('hidden');
		$('.ib2-page-save').parent().addClass('disabled');
		
		if ( element.data('status') != 'autosave' )
			save_loader();
		else {
			$is_autosave = 1;
		};
		
		// Fix Pop Up Display State
		if ( $is_popup == 1 ) {
			$('.ib2-popup-bg').hide();
			$('.ib2-popup').hide();
			$is_popup = 0;
			$('body').trigger('popupclose');
		}
		
		// Fix quiz position
		var quizID = $('.ib2-quiz-el').attr('id');
		$('.ib2-quiz-page').hide();
		$('#' + quizID + '-1').show();
		
		// Fix 3 Step Opt-In Pos
		$('.ib2-section-slide').hide();
		$('.ib2-section-slide-1').show();
		
		// Destroy some jquery ui instances...
		ib2_destroy();
		ib2_hotspot_destroy();
		//stack.save();
		
		$('#screen-container').css({
			'height': 'auto',
			'min-height': 'auto'
		});
		
		var allcss = {};
		$('.ib2-element-css').each(function(i){
			var c = $(this), id = c.attr('id');
			allcss[id] = Base64.encode($.trim(c.html()));
		});
		
		var origContent = $('#screen-container').html();
		$('#ib2-content-placeholder').html(origContent);
		
		// remove unnecessary element
		$('#ib2-content-placeholder').find('.gr-textarea-btn').parent().remove();
		$('#ib2-content-placeholder').find('.gr-textarea-btn').remove();
		
		var splits = {};
		$('.ib2-variant-weight').each(function(i){
			var s = $(this), variant = s.data('variant');
			splits[variant] = s.val();
		});
		
		// Process Social Share Button
		if ( $('#ib2-content-placeholder').find('.ib2-share-el').length ) {
			$('#ib2-content-placeholder').find('.ib2-share-el').each(function(i){
				var c = $(this), url = '';
				
				if ( c.attr('data-custom-url') )
					url = c.data('customUrl');
				
				c.find('.ib2-twitter-share').html('[ib2_share type="twitter" mode="' + c.data('mode') + '" url="' + url + '"]');
				c.find('.ib2-facebook-share').html('[ib2_share type="facebook" mode="' + c.data('mode') + '" url="' + url + '"]');
				c.find('.ib2-google-share').html('[ib2_share type="google" mode="' + c.data('mode') + '" url="' + url + '"]');
				c.find('.ib2-linkedin-share').html('[ib2_share type="linkedin" mode="' + c.data('mode') + '" url="' + url + '"]');
			});
		}
		
		// Process Optin Form
		if ( $('#ib2-content-placeholder').find('.ib2-share-el').length ) {
			$('#ib2-content-placeholder').find('.ib2-share-el').each(function(i){
				var c = $(this), url = '';
				
				if ( c.attr('data-custom-url') )
					url = c.data('customUrl');
				
				c.find('.ib2-twitter-share').html('[ib2_share type="twitter" mode="' + c.data('mode') + '" url="' + url + '"]');
				c.find('.ib2-facebook-share').html('[ib2_share type="facebook" mode="' + c.data('mode') + '" url="' + url + '"]');
				c.find('.ib2-google-share').html('[ib2_share type="google" mode="' + c.data('mode') + '" url="' + url + '"]');
				c.find('.ib2-linkedin-share').html('[ib2_share type="linkedin" mode="' + c.data('mode') + '" url="' + url + '"]');
			});
		}
		
		// Process Comments
		if ( $('#ib2-content-placeholder').find('.ib2-comment-el').length ) {
			$('#ib2-content-placeholder').find('.ib2-comment-el').each(function(i){
				var c = $(this), mode = c.data('comment'),
				id= c.attr('id');
				c.find('.el-content').html('[ib2_comment id="' + id + '" type="' + mode + '"]');
			});
		}
		
		// Process Menu
		if ( $('#ib2-content-placeholder').find('.ib2-menu-el').length ) {
			$('#ib2-content-placeholder').find('.ib2-menu-el').each(function(i){
				var m = $(this), menu = m.data('menu'), style = m.data('style'),
				id = m.attr('id');
				
				if ( menu != 'none' ) {
					m.find('nav').html('[ib2_menu id="' + id + '" menu="' + menu + '" style="' + style + '"]');
				}
			});
		}
		
		// Process background video 
		if ( $('#ib2-content-placeholder').find('.ib2-bgvideo-param').length ) {
			$('#ib2-content-placeholder').find('.ib2-bgvideo-param').each(function(i){
				var bv = $(this), id = bv.attr('id').replace('_bgvid', ''), mp4 = bv.attr('data-mp4') || '', 
					ogg = bv.attr('data-ogg') || '', webm = bv.attr('data-webm') || '';
					bv.parent().prepend('[ib2_bgvid id="' + id + '" mp4="' + mp4 + '" ogg="' + ogg + '" webm="' + webm + '"]');
					bv.remove();
			});
		}

		// Process Shortcode
		if ( $('#ib2-content-placeholder').find('.ib2-shortcode-el').length ) {
			$('#ib2-content-placeholder').find('.ib2-shortcode-el').each(function(i){
				var s = $(this), content = s.html();

				content = content.replace(/\[/gi, '{%sc_open%}').replace(/\]/gi, '{%sc_close%}');
				
				s.html(content);
			});
		}
		
		// Process Countdown
		if ( $('#ib2-content-placeholder').find('.ib2-countdown-el').length ) {
			$('#ib2-content-placeholder').find('.ib2-countdown-el').each(function(i){
				var c = $(this);
				c.html('');
			});
		}
		
		
		// Process Quiz
		var quiz = {};
		if ( $('#ib2-content-placeholder').find('.ib2-quiz-el').length ) {
			$('#ib2-content-placeholder').find('.ib2-quiz-page').each(function(i){
				var c = $(this), obj = {};
				if ( c.hasClass('ib2-unused-question') ) {
					c.replaceWith('[ib2_unused_quiz q="' + c.data('question') + '" tcolor="' + c.find('h3').css('color') + '" acolor="' + c.find('.ib2-answer-list').css('color') + '" id="' + c.attr('id') + '"]');
					return true;	
				}
				
				if ( !c.hasClass('ib2-section-content') ) {
					obj.question = c.find('.quiz-text-question').text();
					obj.order = c.data('question');
						
					obj.answers = {};
					c.find('.ib2-answer-list > .form-group').each(function(a){
						var ans = $(this), u = a+1;
						var abj = {};
						abj.answer = ans.find('.quiz-text-edit').text();
						abj.order = ans.find('.quiz-text-edit').data('ans');
	
						obj.answers[u] = abj;
						ans.removeClass('has-pretty-child');
						ans.replaceWith('[ib2_quiz_answer q="' + obj.order + '" a="' + u + '"]');
					});
					quiz[i] = obj;
				}
			});
		}
		
		// Process Videos
		if ( $('#ib2-content-placeholder').find('.ib2-video-container').length ) {
			$('#ib2-content-placeholder').find('.ib2-video-container').each(function(i){
				var v = $(this), id = v.attr('id'), vtype = v.data('videoType');
				
				v.find('.el-content').html('[ib2_video id="' + id + '" type="' + vtype + '"]');
			});
		}
		
		// Process Carousels
		if ( $('#ib2-content-placeholder').find('.ib2-slide-container').length ) {
			$('#ib2-content-placeholder').find('.ib2-slide-container').each(function(i){
				var v = $(this), id = v.attr('id');
				
				v.find('.el-content').html('[ib2_carousel id="' + id + '"]');
			});
		}
		
		// Powered By
		if ( $('#ib2-content-placeholder').find('#ib2-powered-by').length ) {
			$('#ib2-content-placeholder').find('#ib2-powered-by').remove();
		}
		
		// Bottom Slider
		var slider_content = Base64.encode($('#ib2-bottom-slider').html());

		$('#ib2-content-placeholder').find('#ib2-bottom-slider').remove();
		
		var cstyle = ( $('#screen-container').attr('style') ) ? $('#screen-container').attr('style') : '';
		var popup = $('#main-popup-enable').is(":checked") ? 1 : 0;
		var slider = $('#main-slider-enable').is(":checked") ? 1 : 0;
		var slider_close = $('#main-slider-close').is(":checked") ? 1 : 0;
		var attention = $('#attention-bar-enable').is(":checked") ? 1 : 0;
		var exit_splash = $('#exit-splash-enable').is(":checked") ? 1 : 0;
		var right_click = $('#right-click-enable').is(":checked") ? 1 : 0;
		var right_click_img = $('#right-click-img').is(":checked") ? 1 : 0;
		var wgate = $('#wgate-enable').is(":checked") ? 1 : 0;
		var circular = $('.ib2-circular-countdown').length ? 1 : 0;
		
		var bgvideomute = $('#background-video-mute').is(":checked") ? 1 : 0;
		var bgvideoloop = $('#background-video-loop').is(":checked") ? 1 : 0;
		var bgvideoctrl = $('#background-video-ctrl').is(":checked") ? 1 : 0;
		
		var data = {
			action: 'ib2_save_post',
			autosave: ( element.data('status') == 'autosave' ? 'yes' : 'no'),
			post_id: $('#ib2-post-id').val(),
			allcss: allcss,
			quiz: quiz,
			conversionType: $('#conversion-page-type').val(),
			conversionID: ( $('#conversion-page-type').val() == 'wp' ? $('#conversion-page').val() : '' ),
			weight: splits,
			pageWidth: $('#page-width').val(),
			fontFace: $('#body-text-font').val(),
			fontColor: $('#body-text-color').val(),
			fontSize: $('#body-text-size').val(),
			lineHeight: $('#body-line-height').val(),
			whiteSpace: $('#body-white-space').val(),
			linkColor: $('#body-link-color').val(),
			linkHoverColor: $('#body-link-hover-color').val(),
			backgroundColor: $('#background-color').val(),
			backgroundImg: Base64.encode($('#body-bg-url').val()),
			backgroundImgMode: $('#body-bg-mode').val(),
			backgroundRepeat: $('#background-repeat').val(),
			backgroundPos: $('#background-pos').val(),
			backgroundAttach: $('#background-attach').val(),
			backgroundVideo: Base64.encode($('#background-video').val()),
			backgroundVideoMute: bgvideomute,
			backgroundVideoLoop: bgvideoloop,
			backgroundVideoCtrl: bgvideoctrl,
			favicon: Base64.encode($('#favicon-url').val()),
			title: $('#page-title').val(),
			metaDesc: $('#page-desc').val(),
			metaKeys: $('#page-keywords').val(),
			noindex: $('#meta_noindex').is(":checked"),
			nofollow: $('#meta_nofollow').is(":checked"),
			noodp: $('#meta_noodp').is(":checked"),
			noydir: $('#meta_noydir').is(":checked"),
			noarchive: $('#meta_noarchive').is(":checked"),
			popup: popup,
			popupTime: $('#main-popup-time').val(),
			popupId: $('#ib2-main-popup-id').val(),
			slider: slider,
			sliderTime: $('#main-slider-time').val(),
			sliderContent: slider_content,
			sliderClose: slider_close,
			attentionBar: attention,
			attentionBarText: Base64.encode($.trim($('#attention-bar-text').val())),
			attentionBarTime: $('#attention-bar-time').val(),
			attentionBarAnchor: Base64.encode($.trim($('#attention-bar-anchor').val())),
			attentionBarUrl: Base64.encode($.trim($('#attention-bar-url').val())),
			attentionBarBackground: $('#attention-bar-background').val(),
			attentionBarBorder: $('#attention-bar-border').val(),
			attentionBarFont: $('#attention-bar-font').val(),
			attentionBarFontcolor: $.trim($('#attention-bar-fontcolor').val()),
			exitSplash: exit_splash,
			exitMsg: Base64.encode($('#exit-splash-msg').val()),
			exitUrl: Base64.encode($('#exit-splash-url').val()),
			rightClick: right_click,
			rightClickMsg: Base64.encode($('#right-click-msg').val()),
			rightClickImg: right_click_img,
			welcomeGate: wgate,
			wgateId: $('#locked-page').val(),
			gateType: $('#gate-type').val(),
			gateThanksId: $('#gate-thanks-page').val(),
			gateCode: $('#gate-code').val(),
			headScripts: Base64.encode($('#head-scripts').val()),
			bodyScripts: Base64.encode($('#body-scripts').val()),
			footerScripts: Base64.encode($('#footer-scripts').val()),
			css: Base64.encode($('#editor-body-typo').html()),
			contentStyle: Base64.encode(cstyle, 'instabuilder2'),
			content: Base64.encode($.trim($('#ib2-content-placeholder').html())),
			optincodes: Base64.encode(htmlEntity($('#optin-code-placeholder').html(),1)),
			status: element.data('status'),
			variation: $('#ib2-current-variation').val(),
			oldSlug: $('#current-slug').val(),
			newSlug: $('#ib2-new-slug').val(),
			circular: circular,
			videoData: videoData,
			carouselData: carouselData
		};

		if ( element.data('status') == 'template' ) {
			data.templateName  		= $('#template-name').val();
			data.templateType  		= $('#template-type').val();
			data.templateSubType  	= $('#template-subtype').val();
			data.templateThumb 		= $('#template-thumb-url').val();
			data.templateTags  		= $('#template-tags').val();
			data.templateID    		= $('#save-template-id').val();
		}
		
		if ( element.data('status') == 'variant' ) {
			$('#ib2-template-modal').modal('hide');
		}
		
		$.post(ajaxurl, data, function(response){
			if ( element.data('status') == 'publish' )
				$('#ib2-publish-btn').hide();
			
			if ( element.data('status') == 'template' ) {
				$('#save-template-id').val(0);
				$('#tmpl-duplicate-msg').hide();
				$('#template-save').button('reset');
			}
			
			if ( element.data('status') == 'variant' ) {
				var post_id = $('#ib2-post-id').val(),
				mode = element.data('mode'), url = $('#ib2-admin-url').val();
				
				url += '?action=ib2_new_variant&post_id=' + post_id + '&mode=' + mode;
				if ( mode == 'template' ) url += '&tid=' + element.data('tid');
				if ( mode == 'duplicate' ) url += '&oldvar=' + $('#ib2-current-variation').val();
				window.location.href = url;
			}
			
			if ( element.data('status') != 'variant' && element.data('status') != 'autosave' ) {
				$('#save-background-ui').hide();
				$('#save-loader-ui').hide();
			}
			
			$is_autosave = 0;
			$('.history_menu').show();
			$('#autosave-status').addClass('hidden');
			$('.ib2-page-save').parent().removeClass('disabled');
			
			// re-init ib2 editor
			ib2_init();
			ib2_hotspot_init();
		});
	}
	
	// =========================== ALIGNMENT =========================
	
	$('#screen-container').on('mousedown', '.ib2-align-btn', function(e){
		var $this = $(this), align = $this.data('align'),
		elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = $('#' + elID);
		
		el.css('text-align', align);
		
		if ( el.hasClass('ib2-float-left') )
			el.removeClass('ib2-float-left');
			
		if ( el.hasClass('ib2-float-right') )
			el.removeClass('ib2-float-right');
			
		e.preventDefault();
		e.stopPropagation();
	});
	
	// =========================== 3 STEPS OPTIN =========================
	$('#screen-container').on('click', '.ib2-prevopt-btn', function(e){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = $('#' + elID), currentNum = parseInt(el.data('currentSlide')), prevStep = currentNum - 1;
		
		if ( prevStep >= 1 ) {
			$('#' + elID + ' > .ib2-section-slide').hide();
			$('#' + elID + '-' + prevStep).show();
			
			el.data('currentSlide', prevStep);
			el.attr('data-current-slide', prevStep);
		}
		
		e.preventDefault();
	});
	
	$('#screen-container').on('click', '.ib2-nextopt-btn', function(e){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = $('#' + elID), currentNum = parseInt(el.data('currentSlide')), nextStep = currentNum + 1;
		
		if ( nextStep <= 3 ) {
			$('#' + elID + ' > .ib2-section-slide').hide();
			$('#' + elID + '-' + nextStep).show();
			
			el.data('currentSlide', nextStep);
			el.attr('data-current-slide', nextStep);
		}
		e.preventDefault();
	});
	
	// =========================== QUIZ =========================
	$(window).load(function(){
		$('#ib2-current-question').val(1);
	});
	
	$('#quiz-question-font').change(function(){
		var opt = $("option:selected", this).val(),
		elID = $('#ib2-current-quiz').val();
		
		$('#' + elID).find('.ib2-quiz-page > h3').css('font-family', opt);
	});
	
	$('#quiz-answer-font').change(function(){
		var opt = $("option:selected", this).val(),
		elID = $('#ib2-current-quiz').val();
		
		$('#' + elID).find('.ib2-answer-list').css('font-family', opt);
	});
	
	$('#screen-container').on('mouseenter', '.quiz-text-edit', function(){
		if ( inTextEdit == 1 ) return false;
		var $this = $(this);
		if ( !$this.find('i').length ) {
			$this.append(' <i class="fa fa-pencil"></i>');
		}
		
		$this.addClass('ib2-tab-title-hover');
	});
	
	$('#screen-container').on('mouseleave', '.quiz-text-edit', function(){
		var $this = $(this);
		if ( $this.find('i').length ) {
			$this.find('i').remove();
		}
		$this.removeClass('ib2-tab-title-hover');
	});
	
	$('#screen-container').on('click', '.quiz-text-edit', function(e){
		e.preventDefault();
		var $this = $(this);
		inTextEdit = 1;
		if ( $this.find('i').length ) {
			$this.find('i').remove();
		}
		ib2_destroy();
		
		$this.prop("contenteditable", true);
		$this.focus();
		
		e.stopPropagation();
	});
	
	$('#screen-container').on('blur', '.quiz-text-edit', function(){
		var $this = $(this);
		$this.removeClass('ib2-tab-title-hover');
		inTextEdit = 0;
		$this.prop("contenteditable", false);
		$this.removeAttr('contenteditable');
		
		ib2_init();
	});
	
	$('#screen-container').on('click', '.ib2-addque-btn', function(){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = $('#' + elID), q = $('#ib2-current-question').val(),
		count = $('#' + elID + '-' + q + '-answers > .form-group').length;
		
		if ( count >= 5 ) {
			alert('You can only add up to 5 answer choices per question.');
			return false;
		}
		
		var ans = '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="' + q + '" data-ans="' + (count+1) + '">Answer number ' + (count+1) + ' for the number ' + q + ' question</span></label></div></div>';
		$('#' + elID + '-' + q + '-answers').append(ans);
	});
	
	$('#screen-container').on('click', '.ib2-remque-btn', function(){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = $('#' + elID), q = $('#ib2-current-question').val(),
		count = $('#' + elID + '-' + q + '-answers > .form-group').length;
		
		if ( count <= 2 ) {
			alert('You need to provide at least 2 answer choices per question.');
			return false;
		}
		
		$('#' + elID + '-' + q + '-answers > .form-group:last-child').remove();
	});
	
	$('#screen-container').on('click', '.ib2-nextque-btn', function(){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = $('#' + elID), q = $('#ib2-current-question').val();
		
		if ( q == 6 ) return false;
		el.find('.ib2-quiz-page').hide();
		for ( var p = q; p <= 6; p++ ) {
			if ( p == q ) continue;
			if ( $('#' + elID + '-' + p).hasClass('ib2-unused-question') ) continue;
			if ( p == 6 )
				$('#' + elID + '-result').show();
			else
				$('#' + elID + '-' + p).show();
			$('#ib2-current-question').val(p);
			break;					
		}
	});
	
	$('#screen-container').on('click', '.ib2-prevque-btn', function(){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = $('#' + elID), q = $('#ib2-current-question').val();
		
		if ( q == 1 ) return false;
		
		el.find('.ib2-quiz-page').hide();
		
		for ( var p = q; p >= 1; p-- ) {
			if ( p == q ) continue;
			if ( $('#' + elID + '-' + p).hasClass('ib2-unused-question') ) continue;
			$('#' + elID + '-' + p).show();
			$('#ib2-current-question').val(p);
			break;					
		}
	});
	
	// =========================== TABS =========================
	
	$('body').on('keyup blur', '.tab-title-field', function(e){
		var $this = $(this), elID = $('#ib2-current-tabs').val(),
		num = $this.data('order');
		
		$('.ib2-tab-title').eq(num).text($this.val());
	});
	
	$('#screen-container').on('click', '.ib2-addtab-btn', function(e){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = $('#' + elID), eltab = $('#' + elID + '-tabs'),
		num = $('#' + elID + '-tabs > li').length+1, tabID = 'tab-' + generateID(6);
		 
		eltab.append('<li><a href="#' + tabID + '" data-toggle="tab" role="tab"><span class="ib2-tab-title">Tab #' + num + '</span></a></li>');
		
		var tabs = '<div class="tab-pane fade ib2-section-content" id="' + tabID + '">';
			tabContentID = 'ib2_el_' + generateID(8);
			tabs += '<div id="' + tabContentID + '" class="ib2-content-el ib2-text-el" data-animation="none" data-shadow="none" data-el="text"><p>This is the content of the tab number ' + num + '. This is a text element and you can drag n drop other elements into this area.</p></div>';
		tabs += '</div>';
	  					
		$('#' + elID + ' > .tab-content').append(tabs);
		
		hideSidePanel.click();
		
		e.preventDefault();
	});
	
	$('#screen-container').on('click', '.ib2-remtab-btn', function(e){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = $('#' + elID), eltab = $('#' + elID + '-tabs');
		
		var tabpaneID = $('#' + elID + '-tabs > li:last-child > a').attr('href');
		
		if ( $('#' + elID + '-tabs > li:last-child').hasClass('active') ) {
			$('#' + elID + '-tabs > li:first-child').addClass('active');
			$($('#' + elID + '-tabs > li:first-child > a').attr('href')).addClass('active in');
		}
		
		$('#' + elID + '-tabs > li:last-child').remove();
		$(tabpaneID).remove();
		
		hideSidePanel.click();
		
		e.preventDefault();
	});
	
	// =========================== COL RESIZER =========================
	
	var $pageX = 0, colWidth, nextWidth, newColWidth, newNextWidth, totalWidth = 0;
	$('#screen-container').on('mousedown', '.col-resize-grip', function(e){
		var $this = $(this), elID = $this.parent().attr('id'),
		parts = elID.split('-'), parentID = parts[0],
		nextEl = $('#' + parentID).find(' > .el-cols > .ib2-section-col').eq($('#' + elID).index() + 1);
		
		$pageX = e.pageX;
		colWidth = $('#' + elID).innerWidth();
		nextWidth = nextEl.innerWidth();
		
		if ( nextEl.hasClass('ib2-last-col') )
			nextWidth = nextEl.width();
			
		// total width
		totalWidth = $('#' + parentID).find(' > .el-cols').width();
		$(document)
		.bind('mousemove', function(e){
			var newColWidth = (e.pageX - $pageX) + colWidth,
			newNextWidth = nextWidth - (e.pageX - $pageX);
			
			if ( colWidth > newColWidth )
				newNextWidth = nextWidth + (e.pageX - $pageX);
		})
		.bind('mouseup', function(e){
			$(this)
				.unbind('mousemove')
				.unbind('mouseup');
			
			var newWidthPercent = ( newColWidth / totalWidth ) * 100,
			newNextWidthPercent = ( newNextWidth / totalWidth ) * 100;

			colSetWidth($('#' + elID), newWidthPercent);
			colSetWidth(nextEl, newNextWidthPercent);
			
			// sync grips
			sync_grips( $('#' + elID), nextEl );
			
			// reset totalWidth
			totalWidth = 0;
		});
	});
	
	var colWidths = [];
	colWidths[0] = 100;
	colWidths[1] = 49;
	colWidths[2] = 32;
	colWidths[3] = 23.5;
	
	$('#screen-container').on('click', '.ib2-remcol-btn', function(e){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = ( $('#' + elID).hasClass('ib2-wsection-el') ) ? $('#' + elID + ' .el-content-inner') : $('#' + elID),
		proceed = 1, cols = el.find(' > .el-cols > .ib2-section-col').length,
		lastcol = el.find(' > .el-cols > .ib2-last-col').html();
		
		if ( lastcol != '' ) {
			if ( !confirm("Warning: All contents inside this column will also be removed. Do you want to continue?") ) {
				proceed = 0;
			}
		}
		
		if ( proceed == 1 ) {
			// remove clearfix first
			el.find('.clearfix').remove();
			
			// Destroy col resizer
			colDestroy(el.find(' > .el-cols'));
			
			// Remove last column...
			el.find(' > .el-cols > .ib2-last-col').remove();
			
			var height = el.find(' > .el-cols > .ib2-section-col').height();
			if ( cols == 2 ) {
				// restore back to single column
				el.find(' > .el-cols > .ib2-section-col')
					.attr('style', 'width:100%; min-height:' + height + 'px;')
					.removeClass('ib2-section-col');
			} else {
				el.find(' > .el-cols > .ib2-section-col').each(function(i){
					var c = $(this), lcol = cols - 1, j = i + 1;
					
					c.css({
						'width': colWidths[(lcol-1)] + '%',
						'min-height': height + 'px'
					});
					
					if ( lcol == j ) {
						c.addClass('ib2-last-col').css('margin-right', '0');
					} else {
						c.css('margin-right', '2%');
					}
				});
				
				// Add new clearfix...
				el.find(' > .el-cols').append('<div class="clearfix"></div>');
				
				// initiate col resizer...
				colInit(el.find(' > .el-cols'));
			}
			
			// Re-init sortable...
			if ( el.hasClass("ib2-popup") ) {
				$('.ib2-pop-content')
					.unbind('sortable')
					.sortable({
						update: ib2_element_drop,
						placeholder: 'sortable-line'
					});
			} else {
				if ( $is_popup == 1 )
					ib2_reinit_sortable($('.ib2-pop-content').find('.ib2-section-content'), 'content');
				else
					ib2_reinit_sortable($('.ib2-section-content'), 'content');
			}
		}
		
		$("body").trigger("ib2CommonChange");
		e.preventDefault();
	});
	
	$('#screen-container').on('click', '.ib2-addcol-btn', function(e){
		var $this = $(this), elID = $this.parent().attr('id').replace('-edit-tools', ''),
		el = ( $('#' + elID).hasClass('ib2-wsection-el') ) ? $('#' + elID + ' .el-content-inner') : $('#' + elID);
		
		// remove clearfix first
		el.find('.clearfix').remove();
		
		if ( el.find(' > .el-cols > .ib2-section-col').length ) {
			// Destroy col resizer
			colDestroy(el.find(' > .el-cols'));
			
			var cols = el.find(' > .el-cols > .ib2-section-col').length,
			height = el.find(' > .el-cols > .ib2-section-col').height();
			
			if ( $('#' + elID).hasClass('ib2-slider-el') )
				height = 50;
			
			el.find(' > .el-cols > .ib2-section-col').each(function(i){
				var c = $(this),
				newID = ( i == 0 ) ? elID + '-box' : elID + '-box' + (i+1);
				
				if ( c.hasClass('ib2-last-col') )
					c.removeClass('ib2-last-col');
				
				c.attr('id', newID);
				c.css({
					'width': colWidths[cols] + '%',
					'margin-right': '2%',
					'min-height': height + 'px'
				});
			});
			
			var col = '<div id="' + elID + '-box' + (cols+1) + '" class="ib2-section-content ib2-section-col ib2-last-col" style="width:' + colWidths[cols] + '%; min-height:' + height + 'px;"></div>';
			el.find(' > .el-cols').append(col);
			
		} else {
			var $element = el.find(' > .el-cols > .ib2-section-content'), className = 'ib2-section-content';
			if ( el.hasClass("ib2-popup") ) {
				$element = el.find(' > .el-cols > .ib2-pop-content');
				className = 'ib2-pop-content';
			}
				
			var height = $element.height();
			if ( $('#' + elID).hasClass('ib2-slider-el') )
				height = 50;
				
			// column hasn't been initiated...
			$element
				.addClass('ib2-section-col')
				.css({
					'width': '49%',
					'margin-right': '2%',
					'min-height': height + 'px'
				});
			
			var col = '<div id="' + elID + '-box2" class="' + className + ' ib2-section-col ib2-last-col" style="width:49%; min-height:' + height + 'px;"></div>';
			el.find(' > .el-cols').append(col);
			
		}
		
		// Add new clearfix...
		el.find(' > .el-cols').append('<div class="clearfix"></div>');
		
		// initiate col resizer...
		colInit(el.find(' > .el-cols'));
			
		// Re-init sortable...
		if ( el.hasClass("ib2-popup") ) {
			$('.ib2-pop-content')
				.unbind('sortable')
				.sortable({
					update: ib2_element_drop,
					placeholder: 'sortable-line'
				});
		} else {
			if ( $is_popup == 1 )
				ib2_reinit_sortable($('.ib2-pop-content').find('.ib2-section-content'), 'content');
			else
				ib2_reinit_sortable($('.ib2-section-content'), 'content');
		}
		
		$("body").trigger("ib2CommonChange");
		e.preventDefault();
	});
	
	function colInit( element ) {
		var ec = element;
		var	id = ec.id = ec.attr('id') || colsign+colcount++;
		if ( sections[id] ) return;
		ec.addClass(colsign).attr('id', id).before('<div class="ib2-col-grips"></div>');
		ec.g = []; ec.c = []; ec.w = ec.width(); ec.gc = ec.prev();
		sections[id] = ec; 	// the table object is stored using its id as key	
		createColGrips(ec);
	};
	
	function createColGrips( ec ) {
		var eh = ec.find(" > .ib2-section-col");
		ec.ln = eh.length;
		eh.each( function (i) {
			var c = $(this);
			var g = $('<div>', { 'class': 'ib2-col-grip' });
			g.appendTo(ec.gc);
			g.ec = ec; g.i = i; g.c = c; c.w = c.width();
			ec.g.push(g); ec.c.push(c);
			if ( i < ec.ln - 1 ) g.mousedown(onGripMouseDown).append('<div class="ib2-col-resizer" style="cursor:e-resize"></div>'); // bind the mousedown event to start dragging 
			else g.addClass("ib2-last-grip").removeClass("ib2-col-grip");	// the last grip is used only to store data			
			g.data(colsign, {i:i, e:ec.attr('id')});
		});
		
		syncGrips(ec);
	};
	
	function colInstance( element ) {
		var id = element.attr('id'), ec = sections[id];
		if ( !ec || ec == null || typeof ec === "undefined" ) return false;
		if ( sections[id] )
			return true;
		return false;
	};
	
	function colDestroy( element ) {
		var id = element.attr('id'), ec = sections[id];
		if ( !ec || ec == null || typeof ec === "undefined" ) {
			return;
		}
		
		element.removeAttr('id');
		ec.removeClass(colsign);
		ec.prev('.ib2-col-grips').remove();
		ec.parent().find('.ib2-col-grips').remove();
		
		delete sections[id];
	};

	function onSectionResize( element ) {
		var id = element.attr('id'), ec = sections[id];
		if ( !ec || ec == null || typeof ec === "undefined" ) return;
		
		syncGrips(ec);
	}
	
	function syncGrips( ec ) {
		ec.gc.width(ec.w);			//the grip's container width is updated				
		for ( var i = 0; i < ec.ln; i++ ) {	//for each column
			var c = ec.c[i];		
			ec.g[i].css({
				left: ( c.offset().left - ec.offset().left + c.outerWidth() ) + 'px',
				height: ec.find(' > .ib2-section-col').innerHeight() + 'px'
			});
		}
	};
	
	function syncCols( ec, i, isOver ) {
		var inc = drag.x - drag.l, c = ec.c[i], c2 = ec.c[i+1]; 			
		var w = c.w + inc;	var w2 = c2.w - inc;	//their new width is obtained					
		//c.width( w + 'px');	c2.width(w2 + 'px');	//and set	
		colSetWidth(c, (w / ec.w) * 100);
		colSetWidth(c2, (w2 / ec.w) * 100);
		
		if ( isOver ) { c.w = w; c2.w = w2; }
	};
	
	function onGripMouseDown( event ) {
		if ( $('#screen-container').sortable("instance") )
			$('#screen-container').sortable("disable");
	
		var o = $(this).data(colsign);			//retrieve grip's data
		var ec = sections[o.e],  g = ec.g[o.i];			//shortcuts for the table and grip objects
		
		if ( coldisabled[o.e] == ec ) return false;
		
		g.ox = event.pageX;	g.l = g.position().left;	//the initial position is kept				
		$(document).bind('mousemove.'+colsign, onGripDrag).bind('mouseup.'+colsign,onGripDragOver);	//mousemove and mouseup events are bound
		//h.append("<style type='text/css'>*{cursor:"+ t.opt.dragCursor +"!important}</style>"); 	//change the mouse cursor
		g.addClass('ib2-grip-drag'); 	//add the dragging class (to allow some visual feedback)				
		drag = g;			//the current grip is stored as the current dragging object
		if ( ec.c[o.i].l ) {
			for ( var i = 0, c; i < ec.ln; i++ ) { 
				c = ec.c[i]; c.l = false; c.w= c.width(); 
			} 	//if the column is locked (after browser resize), then c.w must be updated		
		}
		return false; 	//prevent text selection
	};
	
	function onGripDrag( event ) {	
		if ( !drag ) return; var ec = drag.ec;		//table object reference 
		var x = event.pageX - drag.ox + drag.l;		//next position according to horizontal mouse position increment
		var mw = 50, i = drag.i ;	//cell's min width
		var l = mw;

		var max = i == ec.ln - 1 ? ec.w - l : ec.g[i+1].position().left - mw; //max position according to the contiguous cells
		var min = i ? ec.g[i-1].position().left + mw: l;				//min position according to the contiguous cells
		
		x = M.max(min, M.min(max, x));						// apply boundings		
		drag.x = x; drag.css("left",  x + 'px'); 			// apply position increment		

		return false; 	//prevent text selection				
	};
	
	function onGripDragOver( event ) {
		if ( $('#screen-container').sortable("instance") )
			$('#screen-container').sortable("enable");
	
		$(document).unbind('mousemove.'+colsign).unbind('mouseup.'+colsign);
		//$("head :last-child").remove(); 				//remove the dragging cursor style	
		if ( !drag ) return;
		drag.removeClass('ib2-grip-drag');		//remove the grip's dragging css-class
		var ec = drag.ec; 			// get some values	
		if ( drag.x ) { 									//only if the column width has been changed
			syncCols(ec, drag.i, true);	syncGrips(ec);	//the columns and grips are updated
		}
		
		// adjust last column width
		var cols = ec.find(' > .ib2-section-col').length, // number of cols;
		margins = (cols - 1) * 2, totalWidths = ec.width(),
		percentWidths = 100 - margins, currentWidths = 0, lastWidth = 0;
		ec.find(' > .ib2-section-col').each(function(i){
			var sc = $(this), sw = (sc.width() / totalWidths) * 100;
			sw = parseFloat(sw.toFixed(1));
			
			if ( sc.hasClass('ib2-last-col') ) lastWidth = sw;
			currentWidths = currentWidths + sw;
		});
		
		var unusedWidth = percentWidths - currentWidths;
		if ( unusedWidth > 0 ) {
			lastWidth = (lastWidth + unusedWidth) - 0.2;
			ec.find(' > .ib2-last-col').css('width', lastWidth + '%');
		}
		drag = null;									//since the grip's dragging is over	
		
		$("body").trigger("ib2CommonChange");								
	};
	
	function colSetWidth( el, width ) {
		width = width.toFixed(1);
		width -= 0.5;
		return el.css('width', width + '%');
	}

	function ib2_colresize_grips( element ) {
		element.find(' > .ib2-section-col').each(function(i){
			var t = $(this);
			if ( !t.hasClass('ib2-last-col') && !t.find('.col-resize-grip').length ) {
				t.append('<div class="col-resize-grip"></div>');
			}
			t.find(' > .col-resize-grip').css({
				'position': 'absolute',
				'top': 0,
				'left': t.width() + 'px',
				'width': '4px',
				'height': ( t.innerHeight() - 2 ) + 'px',
				'cursor': 'e-resize',
				'border-right': '1px dashed #CCC',
				'z-index': '99'
			});
		});
	}
	
	function sync_grips( el, nextEl ) {
		if ( !el.hasClass('ib2-last-col') && !el.find(' > .col-resize-grip').length ) {
			el.append('<div class="col-resize-grip"></div>');
		}
		
		el.find(' > .col-resize-grip').css({
				'position': 'absolute',
				'top': 0,
				'left': (el.width() - 2) + 'px',
				'width': '4px',
				'cursor': 'e-resize',
				'border-right': '1px dashed #CCC',
				'z-index': '99'
			});
			
		if ( !nextEl.hasClass('ib2-last-col') && !nextEl.find(' > .col-resize-grip').length ) {
			nextEl.append('<div class="col-resize-grip"></div>');
		}
		
		nextEl.find(' > .col-resize-grip').css({
				'position': 'absolute',
				'top': 0,
				'left': (nextEl.width() - 2) + 'px',
				'width': '4px',
				'cursor': 'e-resize',
				'border-right': '1px dashed #CCC',
				'z-index': '99'
			});
	}
	
	// =========================== FUNCTIONS ===========================
	
	function content_sortable_start( event, ui ) {
		var elID = $(ui.item).attr('id'),
		revID = elID + '-content';
		if ( $('#' + elID).data('el') == 'countdown' ) {
			if ( $('#' + revID).length ) {
				$('#' + revID).hide();
			}
		}
	}
	
	function content_sortable_stop( event, ui ) {
		var elID = $(ui.item).attr('id'),
		revID = elID + '-content';
		if ( $('#' + elID).data('el') == 'countdown' ) {
			if ( $('#' + revID).length ) {
				var cloned = $('#' + revID).clone();
				$('#' + revID).remove();
				cloned.insertAfter($('#' + elID));
				
				if ( $('#' + elID).data('end') == 'reveal' ) {
					$('#' + revID).show();
				}
				ib2_reinit_sortable($('.ib2-section-content'), 'content');
			}
			
			if ( $('#' + elID).hasClass('ib2-circular-countdown') ) {
				if ( layerSeconds[elID] ) {
					circular_draw(elID);
				}
				
				if ( layerBgSeconds[elID] ) {
					circular_bg_draw(elID);
				}
			}
		}
	}
	
	function ib2_reinit_sortable( element, type ) {
		if ( type == 'section' ) {
			element
				.unbind("sortable")
				.sortable({
					update: ib2_section_drop,
					placeholder: 'sortable-big-line',
					items: "> div.ib2-section-el"
				});
		} else {
			element
				.unbind("sortable")
				.sortable({
					connectWith: ".ib2-section-content",
					forcePlaceholderSize: true,
					placeholder: 'sortable-line',
					items: "> div.ib2-content-el,> div.ib2-section-el",
					start: content_sortable_start,
					update: ib2_element_drop,
					stop: content_sortable_stop
				});
		}
	}

	function ib2_hotspot_init() {
		if ( $('.ib2-hotspot-el').length ) {
			$('.ib2-hotspot-el').each(function(){
				var $this = $(this), id = $this.attr('id'),
				type = $this.data('toggle');
				if ( type == 'popover' ) {
					$('#' + id).popover();
				} else if ( type == 'tooltip' ) {
					$('#' + id).tooltip();
				}
			});
		}
	}
	
	function ib2_hotspot_destroy() {
		if ( $('.ib2-hotspot-el').length ) {
			$('.ib2-hotspot-el').each(function(){
				var $this = $(this), id = $this.attr('id'),
				type = $this.data('toggle');
				if ( type == 'popover' ) {
					$('#' + id).popover("destroy");
				} else if ( type == 'tooltip' ) {
					$('#' + id).tooltip("destroy");
				}
			});
		}
	}
	
	function ib2_init() {
		$('#screen-container')
			.sortable({
				update: ib2_section_drop,
				placeholder: 'sortable-big-line',
				items: "> div.ib2-section-el"
			});
		
		$('.ib2-section-content')
			.sortable({
				connectWith: ".ib2-section-content",
				forcePlaceholderSize: true,
				placeholder: 'sortable-line',
				items: "> div.ib2-content-el,> div.ib2-section-el",
				start: content_sortable_start,
				update: ib2_element_drop,
				stop: content_sortable_stop
			});
		
		if ( $('.ib2-section-el').length ) {
			// init col resizer...
			$('.ib2-section-el').each(function(i){
				var c = $(this), elID = c.attr('id'),
				el = $('#' + elID);
				
				if ( !el.hasClass('ib2-popup') && el.find(' > .el-cols > .ib2-section-col').length ) {
					colInit(el.find(' > .el-cols'));
				}
				
				if ( !el.hasClass('ib2-popup') && el.find('.el-content-inner > .el-cols > .ib2-section-col').length ) {
					colInit(el.find('.el-content-inner > .el-cols'));
				}
			});
		}
		
		if ( $('.ib2-wide-box').length ) {
			$('.ib2-wide-box')
				.draggable({
					containment: "parent",
				});
		}
		
		// initiate tabs
		if ( $("[id$=-tabs]").length ) {
			$("[id$=-tabs]").each(function(){
				var distab = $(this), tabid = distab.attr('id');
				if ( tabid == 'ib2-current-tab' ) return true;
				$('#screen-container').on('click', '#' + tabid + ' a', function(e){
					e.preventDefault();
					$(this).tab('show');
				});
			});
		}
		
		// initiate countdown
		if ( $('.ib2-countdown-el').length && editor_loaded == 0 ) {
			$('.ib2-countdown-el').each(function(){
				var c = $(this),
				elID = c.attr('id'),
				mode = c.data('mode'),
				target = c.data('target'),
				style = c.data('style');
				
				if ( mode != 'date' ) {
					var parts = target.split(":");
					day = parseInt(parts[0]),
					hour = parseInt(parts[1]),
					min = parseInt(parts[2]),
					newValue = day + hour + min;
					
					target = new Date().valueOf() + newValue;
				}
				
				if ( style == 'circular' ) {
					var format = circular_countdown_format(elID);
					$('#' + elID).html(format);
					$('#' + elID).countdown(target)
						.on('update.countdown', function(event) {
							// update time object
							circular_update_timer(elID, event.offset.totalDays, event.offset.hours, event.offset.minutes, event.offset.seconds);

							if ( layerMinutes[elID] ) {
						    	circular_draw(elID);
						    } else {
						    	create_circular_countdown(elID);
						    }
							
						});
				} else {
					var format = normal_countdown_format(elID, style);
					$('#' + elID).html(format);
	
					$('#' + elID).countdown(target)
						.on('update.countdown', function(event) {
							normal_countdown_update(elID, event.offset.totalDays, event.offset.hours, event.offset.minutes, event.offset.seconds);
						});
				}
				
				setTimeout(function(){
					$('#' + elID).countdown("stop");
				}, 5000);
			});
		}
		
		$('#screen-container').css('min-height', $(window).height() + 'px');
		destroyed = false;
		editor_loaded = 1;
	}
	
	function ib2_destroy() {
		// Remove any pre-existing drag references and element tools...
		$('.drag-ref').remove();
		$('.edit-tools').remove();
		$('.resize-border').removeClass('resize-border');
		$('.element-outline').removeClass('element-outline');
		$('#ib2-current-resize').val('');
		
		if ( $('#screen-container').sortable("instance"))
			$('#screen-container').sortable("destroy");
			
		$('.ib2-section-content').each(function(i){
			var sc = $(this);
			if ( sc.sortable("instance") )
				sc.sortable("destroy");
		});
		
		if ( $('.ib2-pop-content').sortable("instance") )
			$('.ib2-pop-content').sortable("destroy");
			
		$('.ib2-content-el').each(function(){
			var c = $(this);
			if ( c.resizable("instance") )
				c.resizable("destroy");
		});

		if ( $('.ib2-section-el').length ) {
			// destroy col resizer...
			$('.ib2-section-el').each(function(i){
				var c = $(this), elID = c.attr('id'),
				el = $('#' + elID);

				if ( el.find(' > .el-cols > .ib2-section-col').length ) {
					colDestroy(el.find(' > .el-cols'));
				}
				
				if ( el.find('.el-content-inner > .el-cols > .ib2-section-col').length ) {
					colDestroy(el.find('.el-content-inner > .el-cols'));
				}
				
				if ( c.resizable("instance") )
					c.resizable("destroy");
			});
		}
		
		$('.ib2-wide-box').each(function(){
			var c = $(this);
			if ( c.draggable("instance") )
				c.draggable("destroy");
		});
		
		var oldID = $('#ib2-current-resize').val();
		if ( oldID != '' ) {
			if ( $('#' + oldID).resizable("instance") )
				$('#' + oldID).resizable("destroy");
			
			if ( $('#' + oldID + ' > .el-content').resizable("instance") )
				$('#' + oldID + ' > .el-content').resizable("destroy");
				
			$('#' + oldID).removeClass('resize-border');
			$('#' + oldID + ' > .el-content').removeClass('resize-border');
			
			$('#' + oldID + '-edit-tools').remove();
			$('#ib2-current-resize').val('');
		}
		
		destroyed = true;
	}
	
	function ib2_button_setting( id, element, type, url ) {
		var color = element.css('backgroundColor'),
		text = element.text(),
		textSize = element.css('fontSize').replace(/px/gi, ''),
		textColor = element.css('color'),
		textShadow = element.css('textShadow'),
		shadow = textShadow.match(/(-?\d+px)|(rgb\(.+\))|([a-zA-Z]+)/g),
		shadowColor = shadow[0];
		radius = element.css('borderTopLeftRadius').replace(/px/gi, '');
		
		var css = $('#' + id + '-css').html(),
		obj = ib2_css2json(css);
		
		var hoverColor = obj.backgroundColor || '#3071a9',
		textHoverColor = obj.color || '#FFF',
		textHoverShadow = obj.textShadow || '1px 1px 0 #4e4e4e',
		hoverShadow = textHoverShadow.split(" "),
		hoverShadowColor = hoverShadow[(hoverShadow.length-1)];
		
		$('#button-text').val(text);
		$('#button-style').val(type);
		
		if ( element.prop("tagName") == 'A' ) {
			$('.btn-url-setting-group').show();
			$('#button-link-url').val(url);
		} else {
			$('.btn-url-setting-group').hide();
		}

		$('#button-color').iris('color', color);
		$('#button-text-color').iris('color', textColor);
		$('#button-tshadow-color').iris('color', shadowColor);
		$('#button-hover-color').iris('color', hoverColor);
		$('#button-text-hover-color').iris('color', textHoverColor);
		$('#button-tshadow-hover-color').iris('color', hoverShadowColor);
		$('#button-text-size-slider').slider("value", textSize);
		$('#button-corners-slider').slider("value", radius);
	}
	
	function ib2_css2json( css ) {
		var selX = /([^\s\;\{\}][^\;\{\}]*)\{/g,
		endX = /\}/g;
		
		css = css.replace(selX, '');
		css = css.replace(endX, '');
		css = css.replace(/ !important/g, '');
		parts = css.split(';');
		
		var obj = {};
		for ( var c = 0; c < parts.length; c++ ) {
			if ( parts[c] != '' && parts[c] != ' ' ) {
				var str = parts[c].trim(),
				par = str.split(':');
				if ( par[0] == 'color' ) {
					obj.color = par[1];
				} else if ( par[0] == 'background-color' ) {
					obj.backgroundColor = par[1];
				} else if ( par[0] == 'text-shadow' ) {
					obj.textShadow = par[1];
				}
			}
		}
		return obj;
	}
	
	function ib2_section_drop( event, ui ) { 
		var type = ui.item.data('element'),
		newID = 'ib2_el_' + generateID(8), zindex = 10;

		if ( $is_drag == 1 ) {
			// Drop Wide Box Element
			if ( type == 'wbox' ) {
				var bodyWidth = $('body').width();
				ui.item.replaceWith('');
				$('#screen-container').append('<div id="' + newID + '" class="ib2-content-el ib2-wide-box" data-el="wbox" data-animation="none" data-delay="none" data-border-type="single" data-img-mode="upload"><div class="el-content" style="background-color:#c2c2c2; width:100%; max-width:100%;" ><div id="' + newID + '-box" style="height:200px;"></div></div></div>');
			
				$('#' + newID).css('top', event.pageY + 'px');
				
				$('.ib2-wide-box')
				.unbind('draggable')
				.draggable({
					containment: "parent",
				});
			}
			
			// Drop Section
			else if ( type == 'section' ) {
				var iwidth = $('#page-width').val() + 'px',
				boxPadding = '15px 25px';
				
				var s = '<div id="' + newID + '" class="container ib2-section-el" style="width:' + iwidth + '; max-width:100%; margin-left: auto; margin-right:auto;" data-el="section" data-border-type="single" data-animation="none" data-delay="none" data-img-mode="upload">';
					s += '<div class="el-content el-cols" style="background-color:#FFFFFF;padding:' + boxPadding + '">';
						s += '<div id="' + newID + '-box" class="ib2-section-content" style="width:100%; min-height:400px;"></div>';
					s += '</div>';
				s += '</div>';
				
				ui.item.replaceWith(s);
				
			}
			
			// Drop Wide Section
			else if ( type == 'wsection' ) {
				var iwidth = $('#page-width').val() + 'px';
				var s = '<div id="' + newID + '" class="ib2-wsection-el ib2-section-el" data-el="wsection" data-animation="none" data-delay="none" data-border-type="single" data-img-mode="upload">';
					s += '<div class="el-content" style="background-color:#FFFFFF;">';
						s += '<div class="el-content-inner" style="width:' + iwidth + '; margin:0 auto;">';
							s += '<div class="el-cols" style="max-width:100%; width:100%;">';
								s += '<div id="' + newID + '-box" class="ib2-section-content" style="width:' + iwidth + '; min-height:200px; max-width:100%; margin:0 auto;"></div>';
							s += '</div>';
						s += '</div>';
					s += '</div>';
				s += '</div>';
				
				ui.item.replaceWith(s);
			}
			
			else {
				ui.item.replaceWith('');
			}
			
			if ( type == 'section' || type == 'wsection' ) {
				// Re-init sortable...
				if ( $is_popup != 1 )
					ib2_reinit_sortable($('.ib2-section-content'), 'content');
			}
			
			// Re-position Bottom Slider
			if ( $('#screen-container > div:last-child').attr('id') != 'ib2-bottom-slider' ) {
				var slider = $('#ib2-bottom-slider').html();
				$('#ib2-bottom-slider').remove();
				$('#screen-container').append('<div id="ib2-bottom-slider"></div>');
				$('#ib2-bottom-slider').html(slider);
				
				if ( $('#main-slider-enable').is(":checked") )
					$('#ib2-bottom-slider').show();
				else
					$('#ib2-bottom-slider').hide();
			}
			
			// Re-position Powered By
			if ( $('#ib2-powered-by').length ) $('#ib2-powered-by').remove();
			if ( $('#ib2-powered-enable').val() == 'yes' ) {
				if ( !$('#ib2-powered-by').length ) {
					var aff_url = $('#ib2-powered-link').val(),
					powered_img = $('#ib2-img-folder').val() + 'sprites/instabuilder2-poweredby.png';
				
					if ( aff_url == '' ) aff_url = 'http://instabuilder.com';
					
					$('#ib2-bottom-slider').before('<div id="ib2-powered-by"><a href="' + aff_url + '" target="_blank"><img src="' + powered_img + '" class="img-responsive" /></a></div>');
				}
			} else {
				if ( $('#ib2-powered-by').length ) {
					$('#ib2-powered-by').remove();
				}
			}
		
			$("body").trigger("ib2ElDrop", [type, newID]);
			$is_drag = 0;
		} else {
			$("body").trigger("ib2CommonChange");
		}
		$('body').trigger("ib2autosave");
	}
	
	function ib2_element_drop( event, ui ) { 
		var type = ui.item.data('element'),
		newID = 'ib2_el_' + generateID(8),
		zindex = 10;

		if ( $is_drag == 1 ) {
			// Drop Text Element
			if ( type == 'text' ) {
				//$('#' + dropzoneID).append('<div id="' + newID + '" class="ib2-content-el" data-el="text"><div class="el-content"><p>This is your new text content. You can modify this text, add more paragraph, change font style or add images by clicking the edit button.</p></div>');
				ui.item.replaceWith('<div id="' + newID + '" class="ib2-content-el ib2-text-el" data-animation="none" data-shadow="none" data-el="text"><p>This is your new text content. You can modify this text, add more paragraph, change font style or add images by clicking the edit button.</p></div>');
			}
			// Drop Image Element
			else if ( type == 'image' ) {
				
				ui.item.replaceWith('<div id="' + newID + '" class="ib2-content-el ib2-image-el" data-el="image" data-target="none" style="text-align:center" data-aspect-ratio="yes"><div class="el-content" style="display:inline-block; max-width:100%"><img id="' + newID + '-img" src="" alt="" class="img-responsive" /></div></div>');
				if ( ui.item.hasClass('ib2-img-item') ) {
					$('#' + newID).find('img').attr('src', ui.item.attr('src'));
				} else {
					$('#' + newID).find('img').attr('src', $('#ib2-img-src').attr('src'));
				}
				
				var imgWidth = $('#' + newID).find('img').width(),
				imgHeight = $('#' + newID).find('img').height();
				$('#' + newID + ' > .el-content')
					.css({
						'width': imgWidth + 'px',
						'height': 'auto'
					});
			}
			
			// Drop Section
			else if ( type == 'section' ) {
				var iwidth = $('#page-width').val() + 'px',
				boxPadding = '15px 25px';
				
				var s = '<div id="' + newID + '" class="ib2-section-el ib2-inner-section" style="width:100%; max-width:100%;" data-el="section" data-border-type="single" data-animation="none" data-delay="none" data-img-mode="upload">';
					s += '<div class="el-content el-cols" style="background-color:#FFFFFF; max-width:100%; padding:' + boxPadding + '">';
						s += '<div id="' + newID + '-box" class="ib2-section-content" style="width:100%; min-height:80px;"></div>';
					s += '</div>';
				s += '</div>';
				
				ui.item.replaceWith(s);
			}
			
			// Drop Columns
			else if ( type == 'columns' ) {
				var iwidth = $('#page-width').val() + 'px';
				
				var s = '<div id="' + newID + '" class="ib2-section-el ib2-inner-section ib2-columns-el" style="width:100%; max-width:100%;" data-el="section" data-border-type="single" data-animation="none" data-delay="none" data-img-mode="upload">';
					s += '<div class="el-content el-cols" style="background-color:#FFFFFF; max-width:100%;">';
						s += '<div id="' + newID + '-box" class="ib2-section-content ib2-section-col" style="width:49%; min-height:80px; margin-right: 2%"></div>';
						s += '<div id="' + newID + '-box2" class="ib2-section-content ib2-section-col ib2-last-col" style="width:49%; min-height:80px;"></div>';
						s += '<div class="clearfix"></div>';
					s += '</div>';
				s += '</div>';
				
				ui.item.replaceWith(s);
				
				colInit($('#' + newID + ' > .el-cols'));
			}
			
			// Drop Video Element
			else if ( type == 'video' ) {
				var vids = '';
				vids += '<div id="' + newID + '" class="ib2-content-el ib2-video-container" data-el="video" data-video-type="youtube" style="text-align:center">';
				vids += '<div class="el-content ib2-video-responsive-class">';
				vids += '<img src="' + $('#ib2-img-folder').val() + 'video-placeholder.png" class="img-responsive vid-placeholder" />';
				vids += '</div>';
				vids += '<div class="clearfix"></div>';
				vids += '</div>';
				
				ui.item.replaceWith(vids);
				
				$('#' + newID).css({
					'width': '640px',
					'margin': '0 auto'
				});
			}
			
			// Drop Slides Element
			else if ( type == 'slides' ) {
				var slides = '';
				slides += '<div id="' + newID + '" class="ib2-content-el ib2-slide-container" data-el="slides" style="max-width:100%">';
					slides += '<div class="el-content" style="max-width:100%">';
						slides += '<img src="' + $('#ib2-img-folder').val() + 'slides-placeholder.jpg" class="img-responsive" />';
					slides += '</div>';
					slides += '<div class="clearfix"></div>';
				slides += '</div>';
				
				if ( !carouselData[newID] || typeof carouselData[newID] === 'undefined' || carouselData[newID] == null ) {
					carouselData[newID] = {};
				}
				
				if ( !carouselData[newID]["0"] || typeof carouselData[newID]["0"] === 'undefined' || carouselData[newID]["0"] == null ) {
					carouselData[newID]["0"] = {};
				}
				
				carouselData[newID]["0"].imageurl = '';
				carouselData[newID]["0"].title = '';
				carouselData[newID]["0"].desturl = '';
		
				ui.item.replaceWith(slides);
			}
			
			// Drop Menu Element
			else if ( type == 'menu' ) {
      			var menu = '<div id="' + newID + '" class="ib2-content-el ib2-menu-el" data-el="menu" data-style="plain" data-menu="none">';
      				//menu += '<button type="button" class="ib2-navbar-toggle">';
        				//menu += '<span class="sr-only">Toggle navigation</span>';
        				//menu += '<span class="icon-bar"></span>';
        				//menu += '<span class="icon-bar"></span>';
        				//menu += '<span class="icon-bar"></span>';
      				//menu += '</button>';
      				menu += '<nav class="clearfix"></nav>';
      			menu += '</div>';
      			
				ui.item.replaceWith(menu);
				$('#' + newID).find('nav').html($('.ib2-default-nav').html());
				$('#' + newID + '> nav > ul').addClass('ib2-navi ib2-navi-plain').attr('id', newID + '-nav');
				
				ib2_menu_css(newID);
			}
			
			// Drop Title Element
			else if ( type == 'title' ) {
				ui.item.replaceWith('<div id="' + newID + '" class="ib2-content-el ib2-text-el ib2-title-el" data-animation="none" data-shadow="none" data-el="text"><h2>This Is Your New Title</h2></div>');
			}
			
			// Drop Code Element
			else if ( type == 'code' ) {
				ui.item.replaceWith('<div id="' + newID + '" class="ib2-content-el ib2-code-el" data-el="code"><div class="el-content" style="max-width:100%; width:320px; height: 240px; display: inline-block; text-align:left"><p>Edit this element to replace this text with your code.</p></div></div>');
			}
			
			// Drop ShortCode Element
			else if ( type == 'shortcode' ) {
				ui.item.replaceWith('<div id="' + newID + '" class="ib2-content-el ib2-shortcode-el" data-el="shortcode">[insert_shortcode_here]</div>');
			}
			
			// Drop Comment Element
			else if ( type == 'comment' ) {
				ui.item.replaceWith('<div id="' + newID + '" class="ib2-content-el ib2-comment-el" data-el="comment" data-comment="facebook"><div class="el-content" style="width:100%; max-width:100%; display:inline-block"><div id="' + newID + '-fbcom" class="fb-comment-left"><div class="fb-comment-right"></div></div></div></div>');
				//ui.item.replaceWith('<div id="' + newID + '" class="ib2-content-el ib2-comment-el" data-el="comment" data-comment="disqus"><div class="el-content" style="width:100%; max-width:100%; display:inline-block"><div id="' + newID + '-disqus" class="disqus-left"><div class="disqus-right"></div></div></div></div>');
			}
			
			// Drop Comment Element
			else if ( type == 'date' ) {
				var offset = $('#default-time-zone').val();
				ui.item.replaceWith('<div id="' + newID + '" class="ib2-content-el ib2-date-el" data-el="date" data-format="dddd, MMMM Do, YYYY" data-tz="' + offset + '">' + moment().tz(offset).format('dddd, MMMM Do, YYYY') + '</div>');
			}
			
			// Drop 3 Steps Opt-In Element
			else if ( type == 'optin3' ) {
				if ( opt3_dropped == 1 ) {
					ui.item.replaceWith('');
					alert('ERROR: The 3 Steps Opt-In element can ONLY be used once per landing page.');
				} else {
					
					var titleID = 'ib2_el_' + generateID(8),
					buttonID = 'ib2_el_' + generateID(8),
					videoID = 'ib2_el_' + generateID(8),
					textID = 'ib2_el_' + generateID(8),
					lastTitleID = 'ib2_el_' + generateID(8),
					optinID = 'ib2_el_' + generateID(8);
					
					opt3_dropped = 1;
					
					var vids = '';
					vids += '<div id="' + videoID + '" class="ib2-content-el ib2-video-container" data-el="video" data-video-type="youtube" style="text-align:center">';
					vids += '<div class="el-content ib2-video-responsive-class">';
					vids += '<img src="' + $('#ib2-img-folder').val() + 'video-placeholder.png" class="img-responsive vid-placeholder" />';
					vids += '</div>';
					vids += '<div class="clearfix"></div>';
					vids += '</div>';
					
					var optin = '<form role="form" method="post" method="post">';
						optin += '<div class="form-fields col-md-12">';
						optin += '<div class="ib2-field-group form-group"><input type="text" name="optin-name" class="form-control ib2-opt-field ib2-required" placeholder="First Name"></div>';
						optin += '<div class="ib2-field-group form-group"><input type="text" name="optin-email" class="form-control ib2-opt-field ib2-required ib2-validate-email" placeholder="Email Address"></div>';
						optin += '</div>';
						optin += '<div class="button-fields col-md-12">';
						optin += '<button type="submit" id="' + optinID + '-submit" class="ib2-button ib2-form-submit" data-button-type="flat" style="color: #FFFFFF; background-color: #e7bc21; border-color: #b7930e; font-size:18px; border-radius: 5px; text-shadow:1px 1px 0 #4e4e4e">Send Me The Videos!</button>';
						optin += '<input type="image" id="' + optinID + '-image" class="ib2-form-image" src="" alt="Subscribe" style="display:none" />';
						optin += '</form>';
						optin += '</div>';
						optin += '<div class="clearfix"></div>';
						optin += '<div id="' + optinID + '-fb" class="ib2-facebook-optin" style="display:none">';
						optin += '<p class="ib2-facebook-optin-txt">Have a Facebook account?</p>';
						optin += '<button class="ib2-fb-button ib2-facebook-subscribe" type="button">Subscribe with Facebook</button>';
						optin += '</div>';
						
					var q = '<div id="' + newID + '" class="ib2-content-el ib2-optslide-el" data-el="optin3" data-current-slide="1">';
							q += '<div id="' + newID + '-1" class="ib2-section-content ib2-section-slide ib2-section-slide-1" data-slide="1">';
								q += '<div id="' + titleID + '" class="ib2-content-el ib2-text-el ib2-title-el" data-el="text"><h2 style="text-align:center">Discover How To Build<br />High Converting Landing Pages<br />In 5 Minutes...<br />No HTML Skill Required!</h2></div>';
								q += '<div id="' + buttonID + '" class="ib2-content-el ib2-button-el" data-el="button" data-button-type="flat" data-target="url" style="text-align:center; max-width:100%"><div class="el-content" style="display:inline-block"><a href="#go-to-step-2" class="ib2-button" style="color: #FFFFFF; background-color: #428bca; border-color: #357ebd; border-radius: 5px; text-shadow: 1px 1px 0 #4a4a4a;  background-image: none; font-size:20px"><span class="ib2-btn-txt">Click Here To Find Out &raquo;</span></a></div></div>';
							q += '</div>';
							
							q += '<div id="' + newID + '-2" class="ib2-section-content ib2-section-slide ib2-section-slide-2" data-slide="2">';
								q += vids;
								q += '<div id="' + textID + '" class="ib2-content-el ib2-text-el" data-el="text"><p style="text-align:right"><a href="#go-to-step-3"><strong>Next Video &raquo;</strong></a></p></div>';
							q += '</div>';
							
							q += '<div id="' + newID + '-3" class="ib2-section-content ib2-section-slide ib2-section-slide-3" data-slide="3">';
								q += '<div id="' + lastTitleID + '" class="ib2-content-el ib2-text-el ib2-title-el" data-el="text"><h2 style="text-align:center">Please Register Below ... <br />And Get Access To The Complete Video Series!</h2></div>';
								q += '<div id="' + optinID + '" class="ib2-content-el ib2-optin-el" data-el="optin" data-form-mode="vertical" style="text-align:center"><div class="el-content" style="width:300px; max-width:100%; display:inline-block;">' + optin + '</div></div>';
							q += '</div>';
						q += '</div>';
					
					ui.item.replaceWith(q);
					
					$('head').append('<style id="' + optinID + '-submit-css" class="ib2-element-css" type="text/css"></style>');
					var btnCss = '#' + optinID + '-submit:hover, #' + optinID + '-submit:active { color:#FFF !important; background-color: #3071a9 !important; border-color: #285e8e !important; text-shadow: 1px 1px 0 #4a4a4a !important; }';
					$('#' + optinID + '-submit-css').html(btnCss);
				
					$('#' + videoID).css({
						'width': '640px',
						'margin': '0 auto'
					});
			
					var aWidth = $('#' + buttonID).find('a.ib2-button').outerWidth(),
					aHeight = $('#' + buttonID).find('a.ib2-button').outerHeight();
					$('#' + buttonID).find('.el-content')
						.css({
							'width': aWidth + 'px',
							'height': aHeight + 'px'
						});
					
					$('head').append('<style id="' + buttonID + '-css" class="ib2-element-css" type="text/css"></style>');
					var btnCss = '#' + buttonID + ' > .el-content > a.ib2-button:hover, #' + buttonID + ' > .el-content > a.ib2-button:active { color:#FFF !important; background-color: #3071a9 !important; border-color: #285e8e !important; text-shadow: 1px 1px 0 #4a4a4a !important; background-image: none !important; }';
					$('#' + buttonID + '-css').html(btnCss);
				}
			}
			
			// Drop Quiz Element
			else if ( type == 'quiz' ) {
				if ( quiz_dropped == 1 ) {
					ui.item.replaceWith('');
					alert('ERROR: The Questions element can ONLY be used once per landing page.');
				} else {
					quiz_dropped = 1;
					var q = '<div id="' + newID + '" class="ib2-content-el ib2-quiz-el" data-el="quiz" data-questions="1">';
						q += '<div id="' + newID + '-1" class="ib2-quiz-page" data-question="1">';
							q += '<h3 class="quiz-text-edit quiz-text-question" style="color:#333333">This is your number #1 question. Simply click this text to edit?</h3>';
							q += '<div id="' + newID + '-1-answers" class="ib2-answer-list" style="color:#333333">';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="1" data-ans="1">Answer number 1 for the number 1 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="1" data-ans="2">Answer number 2 for the number 1 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="1" data-ans="3">Answer number 3 for the number 1 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="1" data-ans="4">Answer number 4 for the number 1 question</span></label></div></div>';
							q += '</div>';
						q += '</div>';
						q += '<div id="' + newID + '-2" style="display:none" class="ib2-quiz-page ib2-unused-question" data-question="2">';
							q += '<h3 class="quiz-text-edit quiz-text-question" style="color:#333333">This is your number #2 question. Simply click this text to edit?</h3>';
							q += '<div id="' + newID + '-2-answers" class="ib2-answer-list" style="color:#333333">';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="2" data-ans="1">Answer number 1 for the number 2 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="2" data-ans="2">Answer number 2 for the number 2 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="2" data-ans="3">Answer number 3 for the number 2 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="2" data-ans="4">Answer number 4 for the number 2 question</span></label></div></div>';
							q += '</div>';
						q += '</div>';
						q += '<div id="' + newID + '-3" style="display:none" class="ib2-quiz-page ib2-unused-question" data-question="3">';
							q += '<h3 class="quiz-text-edit quiz-text-question" style="color:#333333">This is your number #3 question. Simply click this text to edit?</h3>';
							q += '<div id="' + newID + '-3-answers" class="ib2-answer-list" style="color:#333333">';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="3" data-ans="1">Answer number 1 for the number 3 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="3" data-ans="2">Answer number 2 for the number 3 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="3" data-ans="3">Answer number 3 for the number 3 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="3" data-ans="4">Answer number 4 for the number 3 question</span></label></div></div>';
							q += '</div>';
						q += '</div>';
						q += '<div id="' + newID + '-4" style="display:none" class="ib2-quiz-page ib2-unused-question" data-question="4">';
							q += '<h3 class="quiz-text-edit quiz-text-question" style="color:#333333">This is your number #4 question. Simply click this text to edit?</h3>';
							q += '<div id="' + newID + '-4-answers" class="ib2-answer-list" style="color:#333333">';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="4" data-ans="1">Answer number 1 for the number 4 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="4" data-ans="2">Answer number 2 for the number 4 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="4" data-ans="3">Answer number 3 for the number 4 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="4" data-ans="4">Answer number 4 for the number 4 question</span></label></div></div>';
							q += '</div>';
						q += '</div>';
						q += '<div id="' + newID + '-5" style="display:none" class="ib2-quiz-page ib2-unused-question" data-question="5">';
							q += '<h3 class="quiz-text-edit quiz-text-question" style="color:#333333">This is your number #5 question. Simply click this text to edit?</h3>';
							q += '<div id="' + newID + '-5-answers" class="ib2-answer-list" style="color:#333333">';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="5" data-ans="1">Answer number 1 for the number 5 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="5" data-ans="2">Answer number 2 for the number 5 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="5" data-ans="3">Answer number 3 for the number 5 question</span></label></div></div>';
								q += '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="5" data-ans="4">Answer number 4 for the number 5 question</span></label></div></div>';
							q += '</div>';
						q += '</div>';
						q += '<div id="' + newID + '-result" style="display:none" class="ib2-quiz-page ib2-section-content">';
							var resultID = 'ib2_el_' + generateID(8);
							q += '<div id="' + resultID + '" class="ib2-content-el ib2-text-el ib2-title-el" data-el="text"><h3>Your Survey Result</h3></div>';
							resultID = 'ib2_el_' + generateID(8);
							q += '<div id="' + resultID + '" class="ib2-content-el ib2-text-el" data-el="text"><p>You can place the content for the quiz result here. Feel free to put any element, such as opt-In, video, button, etc.</p></div>';
						q += '</div>';
					q += '</div>';
					ui.item.replaceWith(q);
				}
			}
			
			// Drop Countdown Element
			else if ( type == 'countdown' ) {
				var offset = $('#default-time-zone').val(),
				origTime = moment.tz("2015-08-17", offset),
				utc = origTime.clone().tz("UTC"),
				localTime = moment.utc(utc).toDate(),
				target = moment(localTime).format("YYYY/MM/DD HH:mm:ss");
				
				ui.item.replaceWith('<div id="' + newID + '" class="ib2-content-el ib2-countdown-el" style="font-size:24px; text-align:center; color:#cc0000; text-shadow:1px 1px 0 #a7a7a7;" data-el="countdown" data-style="text" data-mode="date" data-target="2015-08-17 00:00" data-tz="' + offset + '" data-end="none" data-url="" data-after="" data-before="">Countdown</div>');
				
				if ( !$('#' + newID + '-countdown-css').length )
					$('head').append('<style id="' + newID + '-countdown-css" class="ib2-element-css" type="text/css"></style>');
				
				var format = normal_countdown_format(newID, 'text');
				$('#' + newID).html(format);

				$('#' + newID).countdown(target)
					.on('update.countdown', function(event) {
						normal_countdown_update(newID, event.offset.totalDays, event.offset.hours, event.offset.minutes, event.offset.seconds);
					});
					
				setTimeout(function(){
					$('#' + newID).countdown('stop');
				}, 4000);
			}
			
			// Drop Social Share Element
			else if ( type == 'share' ) {
				var imgFolder = $('#ib2-img-folder').val();
				var s = '<div id="' + newID + '" class="ib2-content-el ib2-share-el" data-el="share" data-mode="big">';
					s += '<div class="el-content" style="max-width:100%; display:inline-block">';
						s += '<div class="ib2-facebook-share ib2-share-btn">';
							s += '<img src="' + imgFolder + 'facebook-share-big.png" border="0" />';
						s += '</div>';
						s += '<div class="ib2-twitter-share ib2-share-btn">';
							s += '<img src="' + imgFolder + 'twitter-share-big.png" border="0" />';
						s += '</div>';
						s += '<div class="ib2-linkedin-share ib2-share-btn">';
							s += '<img src="' + imgFolder + 'linkedin-share-big.png" border="0" />';
						s += '</div>';
						s += '<div class="ib2-google-share ib2-share-btn">';
							s += '<img src="' + imgFolder + 'google-share-big.png" border="0" />';
						s += '</div>';
						s += '<div class="clearfix"></div>';
				s += '</div>';
				ui.item.replaceWith(s);
				
				$('#' + newID + ' > .el-content').css('width', '275px');
			}
			
			// Drop Optin Element
			else if ( type == 'optin' ) {
				var optin = '<form role="form" method="post">';
				optin += '<div class="form-fields col-md-12">';
				optin += '<div class="ib2-field-group form-group"><input type="text" name="optin-name" class="form-control ib2-opt-field ib2-required" placeholder="First Name"></div>';
				optin += '<div class="ib2-field-group form-group"><input type="text" name="optin-email" class="form-control ib2-opt-field ib2-required ib2-validate-email" placeholder="Email Address"></div>';
				optin += '</div>';
				optin += '<div class="button-fields col-md-12">';
				optin += '<button type="submit" id="' + newID + '-submit" class="ib2-button ib2-form-submit" data-button-type="flat" style="color: #FFFFFF; background-color: #e7bc21; border-color: #b7930e; font-size:18px; border-radius: 5px; text-shadow:1px 1px 0 #4e4e4e">Subscribe</button>';
				optin += '<input type="image" id="' + newID + '-image" class="ib2-form-image" src="" alt="Subscribe" style="display:none" />';
				optin += '</form>';
				optin += '</div>';
				optin += '<div class="clearfix"></div>';
				optin += '<div id="' + newID + '-fb" class="ib2-facebook-optin" style="display:none">';
				optin += '<p class="ib2-facebook-optin-txt">Have a Facebook account?</p>';
				optin += '<button class="ib2-fb-button ib2-facebook-subscribe" type="button">Subscribe with Facebook</button>';
				optin += '</div>';
				
				ui.item.replaceWith('<div id="' + newID + '" class="ib2-content-el ib2-optin-el" data-el="optin" data-form-mode="vertical" style="text-align:center"><div class="el-content" style="width:300px; max-width:100%; display:inline-block;">' + optin + '</div></div>');
			
				$('head').append('<style id="' + newID + '-submit-css" class="ib2-element-css" type="text/css"></style>');
				var btnCss = '#' + newID + '-submit:hover, #' + newID + '-submit:active { color:#FFF !important; background-color: #3071a9 !important; border-color: #285e8e !important; text-shadow: 1px 1px 0 #4a4a4a !important; }';
				$('#' + newID + '-submit-css').html(btnCss);
			}
			// Drop Button Element
			else if ( type == 'button' ) {
				ui.item.replaceWith('<div id="' + newID + '" class="ib2-content-el ib2-button-el" data-el="button" data-button-type="flat" data-target="url" style="text-align:center; max-width:100%"><div class="el-content" style="display:inline-block"><a href="" class="ib2-button" style="color: #FFFFFF; background-color: #428bca; border-color: #357ebd; border-radius: 5px; text-shadow: 1px 1px 0 #4a4a4a;  background-image: none;"><span class="ib2-btn-txt">Click Here</span></a></div></div>');
			
				var aWidth = $('#' + newID).find('a.ib2-button').outerWidth(),
				aHeight = $('#' + newID).find('a.ib2-button').outerHeight();
				$('#' + newID).find('.el-content')
					.css({
						'width': aWidth + 'px',
						'height': aHeight + 'px'
					});
					
				$('head').append('<style id="' + newID + '-css" class="ib2-element-css" type="text/css"></style>');
				var btnCss = '#' + newID + ' > .el-content > a.ib2-button:hover, #' + newID + ' > .el-content > a.ib2-button:active { color:#FFF !important; background-color: #3071a9 !important; border-color: #285e8e !important; text-shadow: 1px 1px 0 #4a4a4a !important; background-image: none !important; }';
				$('#' + newID + '-css').html(btnCss);
			}
			
			// Drop Tabs Element
			else if ( type == 'tabs' ) {
				var tabs = '';
				tabs += '<div id="' + newID + '" class="ib2-content-el ib2-tabs-el" data-el="tabs">';
					tabs += '<ul id="' + newID + '-tabs" class="nav nav-tabs" role="tablist">';
					  	tabs += '<li class="active"><a href="#' + newID + '-0" role="tab" data-toggle="tab"><span class="ib2-tab-title">Tab #1</span></a></li>';
					  	tabs += '<li><a href="#' + newID + '-1" role="tab" data-toggle="tab"><span class="ib2-tab-title">Tab #2</span></a></li>';
					  	tabs += '<li><a href="#' + newID + '-2" role="tab" data-toggle="tab"><span class="ib2-tab-title">Tab #3</span></a></li>';
					  	tabs += '<li><a href="#' + newID + '-3" role="tab" data-toggle="tab"><span class="ib2-tab-title">Tab #4</span></a></li>';
					tabs += '</ul>';
					tabs += '<div class="tab-content">';
	  					tabs += '<div class="tab-pane fade in active ib2-section-content" id="' + newID + '-0">';
	  						var tabContentID = 'ib2_el_' + generateID(8);
	  						tabs += '<div id="' + tabContentID + '" class="ib2-content-el ib2-text-el" data-animation="none" data-shadow="none" data-el="text"><p>This is the content of the tab number 1. This is a text element and you can drag n drop other elements into this area.</p></div>';
	  					tabs += '</div>';
	  					tabs += '<div class="tab-pane fade ib2-section-content" id="' + newID + '-1">';
	  						tabContentID = 'ib2_el_' + generateID(8);
	  						tabs += '<div id="' + tabContentID + '" class="ib2-content-el ib2-text-el" data-animation="none" data-shadow="none" data-el="text"><p>This is the content of the tab number 2. This is a text element and you can drag n drop other elements into this area.</p></div>';
	  					tabs += '</div>';
	  					tabs += '<div class="tab-pane fade ib2-section-content" id="' + newID + '-2">';
	  						tabContentID = 'ib2_el_' + generateID(8);
	  						tabs += '<div id="' + tabContentID + '" class="ib2-content-el ib2-text-el" data-animation="none" data-shadow="none" data-el="text"><p>This is the content of the tab number 3. This is a text element and you can drag n drop other elements into this area.</p></div>';
	  					tabs += '</div>';
	  					tabs += '<div class="tab-pane fade ib2-section-content" id="' + newID + '-3">';
	  						tabContentID = 'ib2_el_' + generateID(8);
	  						tabs += '<div id="' + tabContentID + '" class="ib2-content-el ib2-text-el" data-animation="none" data-shadow="none" data-el="text"><p>This is the content of the tab number 4. This is a text element and you can drag n drop other elements into this area.</p></div>';
	  					tabs += '</div>';
					tabs += '</div>';
				tabs += '</div>';
				
				ui.item.replaceWith(tabs);

				$('#screen-container').on('click', '#' + newID + '-tabs a', function(e){
					e.preventDefault();
					$(this).tab('show');
				});
			}
			
			// Drop Box Element
			else if ( type == 'box' ) {
				var paddingLeft = $('#box-hp').val(),
				paddingTop = $('#box-vp').val(),
				boxPadding = paddingTop + 'px ' + paddingLeft + 'px';
				ui.item.replaceWith('<div id="' + newID + '" class="ib2-content-el ib2-box-el" data-el="box" data-animation="none" data-delay="none" data-border-type="single" data-img-mode="upload" style="max-width:100%; text-align:center; margin-top:10px; margin-bottom:20px;"><div class="el-content" style="display:inline-block;width:500px;max-width:100%"><div id="' + newID + '-box" class="ib2-section-content" style="width:500px; min-height:200px; height: auto; max-width:100%; background-color:#F5F5F5; border-width:1px; border-style:solid; border-color: #CCC; padding:' + boxPadding + '"></div></div></div>');
			}

			// Drop Horizontal Line Element
			else if ( type == 'hline' ) {
				ui.item.replaceWith('<div id="' + newID + '" class="ib2-content-el" data-el="hline"><div class="el-content" style="width:400px; margin:0 auto; max-width:100%"><div class="ib2-hline" style="width:400px; max-width:100%; height:10px; border-top:2px solid #a7a7a7"></div></div></div>');
			}
			
			// Drop Spacer Element
			else if ( type == 'spacer' ) {
				ui.item.replaceWith('<div id="' + newID + '" class="ib2-content-el" data-el="spacer"><div class="el-content" style="width:100%; margin:0; padding:0; max-width:100%; height:50px;"></div></div>');
			}
			// ==== COMBO ELEMENTS ============
			// Drop Text + Image
			else if ( type == 'text_image' ) {
				var imgID = 'ib2_el_' + generateID(8);
				var titleID = 'ib2_el_' + generateID(8);
				var textID = 'ib2_el_' + generateID(8);
				var content = '';
				content += '<div id="' + newID + '" class="ib2-section-el ib2-inner-section" data-img-mode="upload" data-delay="none" data-animation="none" data-border-type="single" data-el="section" style="width: 100%; max-width: 100%; margin-top: 0px; margin-bottom: 0px;">';
					content += '<div class="el-content el-cols" style="background-color: transparent; max-width: 100%; padding: 0px; opacity: 1; border-color: rgb(51, 51, 51); border-width: 0px; border-radius: 0px;">';
						content += '<div id="' + newID + '-box" class="ib2-section-content ib2-section-col" style="width: 21.4%; min-height: 80px; margin-right: 2%;">';
							content += '<div id="' + imgID + '" class="ib2-content-el ib2-image-el" style="text-align:center" data-target="none" data-el="image">';
								content += '<div class="el-content" style="display: inline-block; max-width: 100%; width: 190px; height: 106px;">';
									content += '<img id="' + imgID + '-img" class="img-responsive" alt="" src="' + $('#ib2-img-folder').val() + 'img-editor.png">';
								content += '</div>';
							content += '</div>';
						content += '</div>';
						content += '<div id="' + newID + '-box2" class="ib2-section-content ib2-section-col ib2-last-col" style="width: 76.4%; min-height: 80px;">';
							content += '<div id="' + titleID + '" class="ib2-content-el ib2-text-el ib2-title-el" data-el="text" data-shadow="none" data-animation="none" style="position: relative;" spellcheck="false">';
								content += '<h3>This Is Your New Title</h3>';
							content += '</div>';
							content += '<div id="' + textID + '" class="ib2-content-el ib2-text-el" data-el="text" data-shadow="none" data-animation="none" style="position: relative;" spellcheck="false">';
								content += '<p>This is your new text content. You can modify this text, add more paragraph, change font style or add images by clicking the edit button. Integer in lectus non dolor fringilla vestibulum ac vel orci. Proin sed placerat nulla.</p>';
							content += '</div>';
						content += '</div>';
						content += '<div class="clearfix"></div>';
					content += '</div>';
				content += '</div>';

				ui.item.replaceWith(content);
				
				colInit($('#' + newID + ' > .el-cols'));
			}
			
			// Drop Text + Image 2
			else if ( type == 'text_image2' ) {
				var imgID = 'ib2_el_' + generateID(8);
				var titleID = 'ib2_el_' + generateID(8);
				var textID = 'ib2_el_' + generateID(8);
				var content = '';
				content += '<div id="' + newID + '" class="ib2-section-el ib2-inner-section" data-img-mode="upload" data-delay="none" data-animation="none" data-border-type="single" data-el="section" style="width: 100%; max-width: 100%; margin-top: 0px; margin-bottom: 0px;">';
					content += '<div class="el-content el-cols" style="background-color: transparent; max-width: 100%; padding: 0px; opacity: 1; border-color: rgb(51, 51, 51); border-width: 0px; border-radius: 0px;">';
						content += '<div id="' + newID + '-box" class="ib2-section-content ib2-section-col" style="width: 76.4%; min-height: 80px; margin-right: 2%;">';
							content += '<div id="' + titleID + '" class="ib2-content-el ib2-text-el ib2-title-el" data-el="text" data-shadow="none" data-animation="none" style="position: relative;" spellcheck="false">';
								content += '<h3>This Is Your New Title</h3>';
							content += '</div>';
							content += '<div id="' + textID + '" class="ib2-content-el ib2-text-el" data-el="text" data-shadow="none" data-animation="none" style="position: relative;" spellcheck="false">';
								content += '<p>This is your new text content. You can modify this text, add more paragraph, change font style or add images by clicking the edit button. Integer in lectus non dolor fringilla vestibulum ac vel orci. Proin sed placerat nulla.</p>';
							content += '</div>';
						content += '</div>';
						content += '<div id="' + newID + '-box2" class="ib2-section-content ib2-section-col ib2-last-col" style="width: 21.4%; min-height: 80px;">';
							content += '<div id="' + imgID + '" class="ib2-content-el ib2-image-el" style="text-align:center" data-target="none" data-el="image">';
								content += '<div class="el-content" style="display: inline-block; max-width: 100%; width: 190px; height: 106px;">';
									content += '<img id="' + imgID + '-img" class="img-responsive" alt="" src="' + $('#ib2-img-folder').val() + 'img-editor.png">';
								content += '</div>';
							content += '</div>';
						content += '</div>';
						content += '<div class="clearfix"></div>';
					content += '</div>';
				content += '</div>';

				ui.item.replaceWith(content);
				
				colInit($('#' + newID + ' > .el-cols'));
			}
			
			// Fancy Opt-In #1
			else if ( type == 'fancy_optin1' ) {
				var titleID = 'ib2_el_' + generateID(8);
				var textID = 'ib2_el_' + generateID(8);
				var optinID = 'ib2_el_' + generateID(8);
				var textID2 = 'ib2_el_' + generateID(8);
				
				var content = '';
				content += '<div id="' + newID + '" class="ib2-content-el ib2-box-el " style="max-width:100%; text-align:center; margin-top:10px; margin-bottom:20px;" data-img-mode="upload" data-border-type="single" data-delay="none" data-animation="none" data-el="box" data-glossy="no">';
					content += '<div class="el-content resize-border ui-resizable" style="display: inline-block; width: 361px; max-width: 100%; border-radius: 0px; height: 482px;">';
						content += '<div id="' + newID + '-box" class="ib2-section-content " style="width: 359px; height: 482px; max-width: 100%; background-color: rgb(45, 45, 45); border-width: 0px; border-style: solid; border-color: rgb(204, 204, 204); padding: 25px 25px 20px; opacity: 1; background-image: none; border-radius: 0px;">';
							content += '<div id="' + titleID + '" class="ib2-content-el ib2-text-el ib2-title-el " data-el="text" data-shadow="none" data-animation="none" style="position: relative;" spellcheck="false">';
								content += '<h3 style="text-align: center;" data-mce-style="text-align: center;"><span style="color: rgb(255, 255, 255);" data-mce-style="color: #ffffff;"><strong>Get Sign Up Now</strong></span></h3>';
							content += '</div>';
							content += '<div id="' + textID + '" class="ib2-content-el ib2-text-el " data-el="text" data-shadow="none" data-animation="none" style="position: relative;" spellcheck="false">';
								content += '<ul class="green-check3">';
									content += '<li><span data-mce-style="color: #ffffff;" style="color: rgb(255, 255, 255);">Lorem Ipsum is simply dummy text of the printing</span></li>';
									content += '<li><span data-mce-style="color: #ffffff;" style="color: rgb(255, 255, 255);">Lorem Ipsum is simply dummy text of the printing</span></li>';
									content += '<li><span data-mce-style="color: #ffffff;" style="color: rgb(255, 255, 255);">Lorem Ipsum is simply dummy text of the printing</span></li>';
								content += '</ul>';
							content += '</div>';
							content += '<div id="' + optinID + '" class="ib2-content-el ib2-optin-el " style="text-align:center" data-form-mode="vertical" data-el="optin">';
								content += '<div class="el-content" style="width:300px; max-width:100%; display:inline-block;">';
									content += '<form role="form" method="post">';
										content += '<div class="form-fields col-md-12">';
											content += '<div class="ib2-field-group form-group">';
												content += '<input class="form-control ib2-opt-field ib2-required field-user-icon" type="text" placeholder="Your First Name" name="optin-name" style="background-color: rgb(255, 255, 255); border-color: rgb(204, 204, 204);">';
											content += '</div>';
											content += '<div class="ib2-field-group form-group">';
												content += '<input class="form-control ib2-opt-field ib2-required ib2-validate-email field-mail-icon" type="text" placeholder="Your Email Address" name="optin-email" style="background-color: rgb(255, 255, 255); border-color: rgb(204, 204, 204);">';
											content += '</div>';
										content += '</div>';
										content += '<div class="button-fields col-md-12">';
											content += '<button id="' + optinID + '-submit" class="ib2-button ib2-form-submit" style="color: rgb(255, 255, 255); background-color: rgb(230, 126, 34); border-color: rgb(202, 98, 6); font-size: 18px; border-radius: 5px; text-shadow: 1px 1px 0px rgb(117, 117, 117); background-image: -moz-linear-gradient(center bottom , rgb(230, 126, 34) 0%, rgb(255, 161, 69)); font-weight: bold;" data-button-type="glossy" type="submit">FREE INSTANT ACCESS</button>';
											content += '<input id="' + optinID + '-image" class="ib2-form-image" type="image" style="display:none" alt="Subscribe" src="">';
										content += '</div>';
									content += '</form>';
									content += '<div class="clearfix"></div>';
									content += '<div id="' + optinID + '-fb" class="ib2-facebook-optin" style="display:none">';
										content += '<p class="ib2-facebook-optin-txt" style="color: rgb(51, 51, 51); font-size: 14px;">Have a Facebook account?</p>';
										content += '<button class="ib2-fb-button ib2-facebook-subscribe" type="button">Subscribe with Facebook</button>';
									content += '</div>';
								content += '</div>';
							content += '</div>';
							content += '<div id="' + textID2 + '" class="ib2-content-el ib2-text-el " data-el="text" data-shadow="none" data-animation="none" style="position: relative;" spellcheck="false">';
								content += '<p style="text-align: center;" data-mce-style="text-align: center;"><span style="color: rgb(153, 153, 153);" data-mce-style="color: #999999;">Your Privacy is SAFE</span></p>';
							content += '</div>';
						content += '</div>';
					content += '</div>';
				content += '</div>';
				
				ui.item.replaceWith(content);
				
				var style = '#' + optinID + '-submit:hover, #' + optinID + '-submit:active {color: #ffffff !important; text-shadow: 1px 1px 0 #4a4a4a !important; background-color:#d48c11 !important; border-color:#b87000 !important; background-image: linear-gradient(center bottom , #d48c11 0%, #f7af34) !important; background-image: -moz-linear-gradient(center bottom , #d48c11 0%, #f7af34) !important; }';
				$('head').append('<style type="text/css" id="' + optinID + '-submit-css" class="ib2-element-css">' + style + '</style>');

			}
			
			// Fancy Opt-In #2
			else if ( type == 'fancy_optin2' ) {
				var sectionID = 'ib2_el_' + generateID(8);
				var sectionID2 = 'ib2_el_' + generateID(8);
				var titleID = 'ib2_el_' + generateID(8);
				var textID = 'ib2_el_' + generateID(8);
				var textID2 = 'ib2_el_' + generateID(8);
				var optinID = 'ib2_el_' + generateID(8);
				
				var content = '';
				content += '<div id="' + newID + '" class="ib2-content-el ib2-box-el " style="max-width: 100%; text-align: center; margin-top: 10px; margin-bottom: 0px;" data-img-mode="premade" data-border-type="single" data-delay="none" data-animation="none" data-el="box" data-glossy="no">';
					content += '<div class="el-content" style="display: inline-block; width: 616px; max-width: 100%; border-radius: 10px; height: 373px;">';
						content += '<div id="' + newID + '-box" class="ib2-section-content" style="width: 614px; height: 373px; max-width: 100%; background-color: rgb(38, 38, 38); border-width: 6px; border-style: solid; border-color: rgb(15, 15, 15); padding: 0px; opacity: 1; background-image: url(\'' + $('#ib2-img-folder').val() + 'backgrounds/bg2.jpg\'); border-radius: 10px;">';
							
							content += '<div id="' + sectionID + '" class="ib2-section-el ib2-inner-section" data-img-mode="premade" data-delay="none" data-animation="none" data-border-type="single" data-el="section" style="width: 100%; max-width: 100%; margin-top: 0px; margin-bottom: 0px; position: relative; z-index: 2; left: 0px; top: 0px; height: auto;" data-glossy="no">';
								content += '<div class="el-content el-cols" style="background-color: transparent; max-width: 100%; padding: 15px 45px 10px; opacity: 1; background-image: url(\'' + $('#ib2-img-folder').val() + 'backgrounds/bg1.jpg\'); border-color: rgb(51, 51, 51); border-width: 0px; border-radius: 0px;">';
									content += '<div id="' + sectionID + '-box" class="ib2-section-content" style="width: 100%; min-height: 139px;">';
										content += '<div id="' + titleID + '" class="ib2-content-el ib2-text-el ib2-title-el " data-el="text" data-shadow="yes" data-animation="none" style="position: relative; text-shadow: 1px 1px 0px rgb(2, 2, 2); left: 0px; top: 0px;" spellcheck="false">';
											content += '<p data-mce-style="text-align: center;" style="text-align: center;"><span style="color: rgb(238, 255, 255); font-size: 32px;" data-mce-style="color: #eeffff; font-size: 32px;"><strong>﻿Get Awesome Local Deals Now</strong></span></p>';
										content += '</div>';
										content += '<div id="' + textID + '" class="ib2-content-el ib2-text-el " data-el="text" data-shadow="yes" data-animation="none" style="position: relative; text-shadow: 1px 1px 0px rgb(2, 2, 2); left: 0px; top: 0px;" spellcheck="false">';
											content += '<p style="text-align: center;" data-mce-style="text-align: center;"><span style="color: rgb(128, 128, 128);" data-mce-style="color: #808080;">';
												content += '<strong>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s</strong></span>';
											content += '</p>';
										content += '</div>';
									content += '</div>';
								content += '</div>';
							content += '</div>';
						
							content += '<div id="' + sectionID2 + '" class="ib2-section-el ib2-inner-section " data-img-mode="upload" data-delay="none" data-animation="none" data-border-type="single" data-el="section" style="width: 100%; max-width: 100%; margin-top: 0px; margin-bottom: 0px; height: auto;" data-glossy="no">';
								content += '<div class="el-content el-cols" style="background-color: transparent; max-width: 100%; padding: 25px 25px 0px; opacity: 1; background-image: none; border-color: rgb(51, 51, 51); border-width: 0px; border-radius: 0px;">';
									content += '<div id="' + sectionID2 + '-box" class="ib2-section-content" style="width: 100%; min-height: 145px;">';
										content += '<div id="' + optinID + '" class="ib2-content-el ib2-optin-el " style="text-align: center;" data-form-mode="semi-horizontal" data-el="optin">';
											content += '<div class="el-content" style="width: 532px; max-width: 100%; display: inline-block;">';
												content += '<form class="form-inline" role="form" method="post">';
													content += '<div class="form-fields col-md-12">';
														content += '<div class="ib2-field-group form-group">';
															content += '<input class="form-control ib2-opt-field ib2-required input-lg field-user-icon" type="text" placeholder="First Name" name="optin-name" style="background-color: rgb(255, 255, 255); border-color: rgb(204, 204, 204);">';
														content += '</div>';
														content += '<div class="ib2-field-group form-group">';
															content += '<input class="form-control ib2-opt-field ib2-required ib2-validate-email input-lg field-mail-icon" type="text" placeholder="Email Address" name="optin-email" style="background-color: rgb(255, 255, 255); border-color: rgb(204, 204, 204);">';
														content += '</div>';
													content += '</div>';
													content += '<div class="button-fields col-md-12 button-container">';
														content += '<button id="' + optinID + '-submit" class="ib2-button ib2-form-submit submit-lg" style="color: rgb(255, 255, 255); background-color: rgb(231, 36, 33); border-color: rgb(203, 8, 5); font-size: 18px; border-radius: 5px; text-shadow: 1px 1px 0px rgb(78, 78, 78); background-image: -moz-linear-gradient(center bottom , rgb(231, 36, 33) 0%, rgb(255, 71, 68)); font-weight: bold;" data-button-type="glossy" type="submit">FREE INSTANT ACCESS</button>';
														content += '<input id="' + optinID + '-image" class="ib2-form-image" type="image" style="display:none" alt="Subscribe" src="">';
													content += '</div>';
												content += '</form>';
												content += '<div class="clearfix"></div>';
												content += '<div id="' + optinID + '-fb" class="ib2-facebook-optin" style="display:none">';
													content += '<p class="ib2-facebook-optin-txt" style="color: rgb(51, 51, 51); font-size: 16px;">Have a Facebook account?</p>';
													content += '<button class="ib2-fb-button ib2-facebook-subscribe" type="button">Subscribe with Facebook</button>';
												content += '</div>';
											content += '</div>';
										content += '</div>';
										content += '<div id="' + textID2 + '" class="ib2-content-el ib2-text-el " data-el="text" data-shadow="none" data-animation="none" style="position: relative;" spellcheck="false">';
											content += '<p style="text-align: center;" data-mce-style="text-align: center;"><span style="color: rgb(128, 128, 128); font-size: 14px;" data-mce-style="color: #808080; font-size: 14px;">Your information will never be shared</span></p>';
										content += '</div>';
									content += '</div>';
								content += '</div>';
							content += '</div>';
						content += '</div>';
					content += '</div>';
				content += '</div>';
				
				ui.item.replaceWith(content);
				
				var style = '#' + optinID + '-submit:hover, #' + optinID + '-submit:active {color: #ffffff !important; text-shadow: 1px 1px 0 #4a4a4a !important; background-color:#831c1a !important; border-color:#670000 !important; background-image: linear-gradient(center bottom , #831c1a 0%, #a63f3d) !important; background-image: -moz-linear-gradient(center bottom , #831c1a 0%, #a63f3d) !important; }';
				$('head').append('<style type="text/css" id="' + optinID + '-submit-css" class="ib2-element-css">' + style + '</style>');
				
			}
			
			// Order Scarcity
			else if ( type == 'order_scarcity' ) {
				var sectionID = 'ib2_el_' + generateID(8);
				var titleID = 'ib2_el_' + generateID(8);
				var countdownID = 'ib2_el_' + generateID(8);
				var buttonID = 'ib2_el_' + generateID(8);
				
				var content = '';
				content += '<div id="' + newID + '" class="ib2-section-el ib2-inner-section" data-img-mode="upload" data-delay="none" data-animation="none" data-border-type="single" data-el="section" style="width: 100%; max-width: 100%; margin-top: 0px; margin-bottom: 0px; height: auto;">';
					content += '<div class="el-content el-cols" style="background-color: transparent; max-width: 100%; padding: 15px 0px 0px; opacity: 1; border-color: rgb(51, 51, 51); border-width: 0px; border-radius: 0px;">';
						content += '<div id="' + newID + '-box" class="ib2-section-content ib2-section-col" style="width: 34.5%; min-height: 70px; margin-right: 2%;">';
							content += '<div id="' + sectionID + '" class="ib2-section-el ib2-inner-section" data-img-mode="upload" data-delay="none" data-animation="none" data-border-type="single" data-el="section" style="width: 100%; max-width: 100%; margin-top: 0px; margin-bottom: 0px; height: auto;">';
								content += '<div class="el-content el-cols" style="background-color: transparent; max-width: 100%; padding: 10px 0px 0px; opacity: 1; border-color: rgb(51, 51, 51); border-width: 0px; border-radius: 0px;">';
									content += '<div id="' + sectionID + '-box" class="ib2-section-content" style="width: 100%; min-height: 35px;">';
										content += '<div id="' + titleID + '" class="ib2-content-el ib2-text-el ib2-title-el" data-el="text" data-shadow="yes" data-animation="none" style="position: relative; text-shadow: 1px 1px 0px rgb(128, 128, 128);" spellcheck="false">';
											content += '<h3 data-mce-style="text-align: right;" style="text-align: right;"><span data-mce-style="color: #ffffff;" style="color: rgb(255, 255, 255);"><em><strong>Quick, Offer Expires In:</strong></em></span></h3>';
										content += '</div>';
									content += '</div>';
								content += '</div>';
							content += '</div>';
						content += '</div>';
						
						content += '<div id="' + newID + '-box2" class="ib2-section-content ib2-section-col" style="width: 37.6%; min-height: 70px; margin-right: 2%;">';
							content += '<div id="' + countdownID + '" class="ib2-content-el ib2-countdown-el ib2-countdown-style" data-before="" data-after="" data-url="" data-end="none" data-tz="Asia/Jakarta" data-target="172800000:28800000:0" data-mode="cookie" data-style="flat-box" data-el="countdown" style="font-size: 28px; text-align: center; color: rgb(226, 222, 222); text-shadow: 1px 1px 0px rgb(87, 85, 85);">';
								content += '<span class="ib2-digit"><span id="' + countdownID + '-tdays">0</span><span class="ib2-digit-txt"> day </span></span>';
								content += '<span class="ib2-digit"><span id="' + countdownID + '-thours">0</span><span class="ib2-digit-txt"> hour </span></span>';
								content += '<span class="ib2-digit"><span id="' + countdownID + '-tminutes">0</span><span class="ib2-digit-txt"> minute </span></span>';
								content += '<span class="ib2-digit"><span id="' + countdownID + '-tseconds">0</span><span class="ib2-digit-txt"> second </span></span>';
							content += '</div>';
						content += '</div>';
						
						content += '<div id="' + newID + '-box3" class="ib2-section-content ib2-section-col ib2-last-col" style="width: 23.7%; min-height: 70px;">';
							content += '<div id="' + buttonID + '" class="ib2-content-el ib2-button-el" style="text-align: center; max-width: 100%;" data-target="url" data-button-type="glossy" data-el="button">';
								content += '<div class="el-content" style="display: inline-block; width: auto; height: auto; min-width: 168px; min-height: 26px;">';
									content += '<a class="ib2-button" style="color: rgb(26, 89, 121); background-color: rgb(241, 179, 44); border-color: rgb(213, 151, 16); border-radius: 5px; text-shadow: 1px 1px 0px rgb(240, 216, 121); background-image: -moz-linear-gradient(center bottom , rgb(241, 179, 44) 0%, rgb(255, 214, 79)); font-size: 18px; font-weight: bold; min-width: 194px; min-height: 40px; width: auto; height: auto;" href=""><i class="fa fa-shopping-cart"></i>ORDER NOW</a>';
								content += '</div>';
							content += '</div>';
						content += '</div>';
						content += '<div class="clearfix"></div>';
					content += '</div>';
				content += '</div>';

				ui.item.replaceWith(content);
				
				var style = '#' + countdownID + '.ib2-countdown-style > .ib2-digit { background-color: #890f0f; background-image: none; border-width: 2px; border-style: solid; border-color: #ffffff; }';
				$('head').append('<style type="text/css" id="' + countdownID + '-countdown-css" class="ib2-element-css">' + style + '</style>');

				var newValue = 172800000 + 28800000, target = new Date().valueOf() + newValue;
				
				$('#' + countdownID).countdown(target)
					.on('update.countdown', function(event) {
						normal_countdown_update(countdownID, event.offset.totalDays, event.offset.hours, event.offset.minutes, event.offset.seconds);
					});
					
				colInit($('#' + newID + ' > .el-cols'));
				
				setTimeout(function(){
					$('#' + countdownID).countdown('stop');
				}, 4000);
				
			}
			
			// Order Scarcity #2
			else if ( type == 'order_scarcity2' ) {
				var sectionID = 'ib2_el_' + generateID(8);
				var titleID = 'ib2_el_' + generateID(8);
				var countdownID = 'ib2_el_' + generateID(8);
				
				var content = '';
				content += '<div id="' + newID + '" class="ib2-section-el ib2-inner-section" data-img-mode="upload" data-delay="none" data-animation="none" data-border-type="single" data-el="section" style="width: 100%; max-width: 100%; margin-top: 0px; margin-bottom: 0px; height: auto;">';
					content += '<div class="el-content el-cols" style="background-color: transparent; max-width: 100%; padding: 15px 0px 0px; opacity: 1; border-color: rgb(51, 51, 51); border-width: 0px; border-radius: 0px;">';
						content += '<div id="' + newID + '-box" class="ib2-section-content ib2-section-col" style="width: 61.8%; min-height: 70px; margin-right: 2%;">';
							content += '<div id="' + sectionID + '" class="ib2-section-el ib2-inner-section" data-img-mode="upload" data-delay="none" data-animation="none" data-border-type="single" data-el="section" style="width: 100%; max-width: 100%; margin-top: 0px; margin-bottom: 0px; height: auto;">';
								content += '<div class="el-content el-cols" style="background-color: transparent; max-width: 100%; padding: 10px 0px 0px; opacity: 1; border-color: rgb(51, 51, 51); border-width: 0px; border-radius: 0px;">';
									content += '<div id="' + sectionID + '-box" class="ib2-section-content" style="width: 100%; min-height: 35px;">';
										content += '<div id="' + titleID + '" class="ib2-content-el ib2-text-el ib2-title-el" data-el="text" data-shadow="yes" data-animation="none" style="position: relative; text-shadow: 1px 1px 0px rgb(128, 128, 128);" spellcheck="false">';
											content += '<h3 data-mce-style="text-align: right;" style="text-align: right;"><span data-mce-style="color: #ffffff;" style="color: rgb(255, 255, 255);"><em><strong>This Offer Will Expires In:</strong></em></span></h3>';
										content += '</div>';
									content += '</div>';
								content += '</div>';
							content += '</div>';
						content += '</div>';
						
						content += '<div id="' + newID + '-box2" class="ib2-section-content ib2-section-col ib2-last-col" style="width: 36%; min-height: 70px;">';
							content += '<div id="' + countdownID + '" class="ib2-content-el ib2-countdown-el ib2-countdown-style" data-before="" data-after="" data-url="" data-end="none" data-tz="Asia/Jakarta" data-target="172800000:28800000:0" data-mode="cookie" data-style="flat-box" data-el="countdown" style="font-size: 28px; text-align: center; color: rgb(226, 222, 222); text-shadow: 1px 1px 0px rgb(87, 85, 85);">';
								content += '<span class="ib2-digit"><span id="' + countdownID + '-tdays">0</span><span class="ib2-digit-txt"> day </span></span>';
								content += '<span class="ib2-digit"><span id="' + countdownID + '-thours">0</span><span class="ib2-digit-txt"> hour </span></span>';
								content += '<span class="ib2-digit"><span id="' + countdownID + '-tminutes">0</span><span class="ib2-digit-txt"> minute </span></span>';
								content += '<span class="ib2-digit"><span id="' + countdownID + '-tseconds">0</span><span class="ib2-digit-txt"> second </span></span>';
							content += '</div>';
						content += '</div>';

						content += '<div class="clearfix"></div>';
					content += '</div>';
				content += '</div>';

				ui.item.replaceWith(content);
				
				var style = '#' + countdownID + '.ib2-countdown-style > .ib2-digit { background-color: #3a3a3a; background-image: none; border-width: 2px; border-style: solid; border-color: #ffffff; }';
				$('head').append('<style type="text/css" id="' + countdownID + '-countdown-css" class="ib2-element-css">' + style + '</style>');

				var newValue = 172800000 + 28800000, target = new Date().valueOf() + newValue;
				
				$('#' + countdownID).countdown(target)
					.on('update.countdown', function(event) {
						normal_countdown_update(countdownID, event.offset.totalDays, event.offset.hours, event.offset.minutes, event.offset.seconds);
					});
					
				colInit($('#' + newID + ' > .el-cols'));
				
				setTimeout(function(){
					$('#' + countdownID).countdown('stop');
				}, 4000);
				
			}
			
			// Drop Box + Video
			else if ( type == 'box_video' ) {
				var videoID = 'ib2_el_' + generateID(8);
				var content = '';
				content += '<div id="' + newID + '" class="ib2-content-el ib2-box-el" style="max-width:100%; text-align:center; margin-top:10px; margin-bottom:20px;" data-img-mode="upload" data-border-type="single" data-delay="none" data-animation="none" data-el="box">';
					content += '<div class="el-content" style="display: inline-block; width: 655px; max-width: 100%; height: 371px; border-radius: 8px;">';
						content += '<div id="' + newID + '-box" class="ib2-section-content" style="width: 653px; height: 371px; max-width: 100%; background-color: #3a3a3a; border-width: 1px; border-style: solid; border-color: #1a1a1a; padding: 10px; opacity: 1; border-radius: 8px; box-shadow: 0px 0px 8px 3px rgb(145, 145, 145);">';
							content += '<div id="' + videoID + '" class="ib2-content-el ib2-video-container" style="text-align: center; width: 629px; margin: 0px auto;" data-video-type="youtube" data-el="video">';
								content += '<div class="el-content ib2-video-responsive-class" style="height: auto;">';
									content += '<img class="img-responsive vid-placeholder" src="' + $('#ib2-img-folder').val() + 'video-placeholder.png">';
								content += '</div>';
								content += '<div class="clearfix"></div>';
							content += '</div>';
						content += '</div>';
					content += '</div>';
				content += '</div>';
				
				ui.item.replaceWith(content);
				
			}
			
			// Drop Product Box #1
			else if ( type == 'product_box' ) {
				var sectionID = 'ib2_el_' + generateID(8);
				var section2ID = 'ib2_el_' + generateID(8);
				var titleID = 'ib2_el_' + generateID(8);
				var textID = 'ib2_el_' + generateID(8);
				var listID = 'ib2_el_' + generateID(8);
				var content = '';
				content += '<div id="' + newID + '" class="ib2-content-el ib2-box-el" style="max-width: 100%; text-align: center; margin-top: 10px; margin-bottom: 20px;" data-img-mode="premade" data-border-type="single" data-delay="none" data-animation="none" data-el="box">';
					content += '<div class="el-content" style="display: inline-block; width: 584px; max-width: 100%; border-radius: 10px; height: 424px;">';
						content += '<div id="' + newID + '-box" class="ib2-section-content" style="width: 582px; height: 424px; max-width: 100%; background-color: rgb(255, 255, 255); border-width: 1px; border-style: solid; border-color: rgb(44, 97, 123); padding: 0px; opacity: 1; border-radius: 10px; box-shadow: 0px 0px 8px 3px rgb(204, 204, 204); background-image: url(\'' + $('#ib2-img-folder').val() + '/backgrounds/bgnoise_lg.png\');">';
							content += '<div id="' + sectionID + '" class="ib2-section-el ib2-inner-section" data-img-mode="upload" data-delay="none" data-animation="none" data-border-type="multi" data-el="section" style="width: 100%; max-width: 100%; height: auto; margin-top: 0px; margin-bottom: 0px;">';
								content += '<div class="el-content el-cols" style="background-color: rgb(123, 154, 182); max-width: 100%; padding: 10px 15px 0px; opacity: 1; border-color: rgb(44, 97, 123); border-width: 0px 0px 1px; border-radius: 0px; border-style: solid;">';
									content += '<div id="' + sectionID + '-box" class="ib2-section-content" style="width: 100%; min-height: 46px;">';
										content += '<div id="' + titleID + '" class="ib2-content-el ib2-text-el ib2-title-el" data-el="text" data-shadow="none" data-animation="none" style="position: relative;" spellcheck="false">';
											content += '<h4 style="text-align: center;" data-mce-style="text-align: center;"><span data-mce-style="color: #ffffff;" style="color: #ffffff;">PRODUCT NAME HERE</span></h4>';
										content += '</div>';
									content += '</div>';
								content += '</div>';
							content += '</div>';
							content += '<div id="' + section2ID + '" class="ib2-section-el ib2-inner-section" data-img-mode="upload" data-delay="none" data-animation="none" data-border-type="single" data-el="section" style="width: 100%; max-width: 100%; margin-top: 0px; margin-bottom: 0px;">';
								content += '<div class="el-content el-cols" style="background-color: transparent; max-width: 100%; padding: 15px 25px; opacity: 1; border-color: rgb(51, 51, 51); border-width: 0px; border-radius: 0px;">';
									content += '<div id="' + section2ID + '-box" class="ib2-section-content" style="width:100%; min-height:80px;">';
										content += '<div id="' + textID + '" class="ib2-content-el ib2-text-el" data-el="text" data-shadow="none" data-animation="none" style="position: relative;" spellcheck="false">';
											content += '<p><span data-mce-style="color: #808080;" style="color: rgb(128, 128, 128);">You can insert a short explanation about your product/module here. Explain the core or main benefit of this product/module, and maybe insert an ecover of your product here.</span></p>';
										content += '</div>';
										content += '<div id="' + listID + '" class="ib2-content-el ib2-text-el" data-el="text" data-shadow="none" data-animation="none" style="position: relative;" spellcheck="false">';
											content += '<ul class="blue-check1">';
												content += '<li><span data-mce-style="color: #808080;" style="color: rgb(128, 128, 128);"><strong>Product Benefit 1 -</strong> Killer line that explain the product/module benefit #1 goes here...</span></li>';
												content += '<li><span data-mce-style="color: #808080;" style="color: rgb(128, 128, 128);"><strong>Product Benefit 2 -</strong> Killer line that explain the product/module benefit #2 goes here...</span></li>';
												content += '<li><span data-mce-style="color: #808080;" style="color: rgb(128, 128, 128);"><strong>Product Benefit 3 -</strong> Killer line that explain the product/module benefit #3 goes here...</span></li>';
												content += '<li><span data-mce-style="color: #808080;" style="color: rgb(128, 128, 128);"><strong>Product Benefit 4 -</strong> Killer line that explain the product/module benefit #4 goes here...</span></li>';
											content += '</ul>';
										content += '</div>';
									content += '</div>';
								content += '</div>';
							content += '</div>';
						content += '</div>';
					content += '</div>';
				content += '</div>';
				
				ui.item.replaceWith(content);
			}
			
			// Drop Product Box #2
			else if ( type == 'product_box2' ) {
				var sectionID = 'ib2_el_' + generateID(8);
				var titleID = 'ib2_el_' + generateID(8);
				var textID = 'ib2_el_' + generateID(8);
				var listID = 'ib2_el_' + generateID(8);
				var content = '';
				content += '<div id="' + newID + '" class="ib2-content-el ib2-box-el" style="max-width: 100%; text-align: center; margin-top: 10px; margin-bottom: 20px;" data-img-mode="premade" data-border-type="single" data-delay="none" data-animation="none" data-el="box">';
					content += '<div class="el-content" style="display: inline-block; width: 584px; max-width: 100%; border-radius: 10px; height: 439px;">';
						content += '<div id="' + newID + '-box" class="ib2-section-content" style="width: 582px; height: 439px; max-width: 100%; background-color: rgb(255, 255, 255); border-width: 0px; border-style: solid; border-color: rgb(44, 97, 123); padding: 5px; opacity: 1; border-radius: 10px; box-shadow: 0px 0px 8px 3px rgb(204, 204, 204);">';
							content += '<div id="' + sectionID + '" class="ib2-section-el ib2-inner-section" data-img-mode="upload" data-delay="none" data-animation="none" data-border-type="single" data-el="section" style="width: 100%; max-width: 100%; margin-top: 0px; margin-bottom: 0px;">';
								content += '<div class="el-content el-cols" style="background-color: transparent; max-width: 100%; padding: 15px 25px; opacity: 1; border-color: rgb(51, 51, 51); border-width: 0px; border-radius: 10px; box-shadow: 0px 0px 10px 4px rgb(229, 229, 229) inset;">';
									content += '<div id="' + sectionID + '-box" class="ib2-section-content" style="width:100%; min-height:397px;">';
										content += '<div id="' + titleID + '" class="ib2-content-el ib2-text-el ib2-title-el" data-el="text" data-shadow="none" data-animation="none" style="position: relative;" spellcheck="false">';
											content += '<h4 style="text-align: center;" data-mce-style="text-align: center;"><span data-mce-style="color: #808080;" style="color: #808080;">PRODUCT NAME HERE</span></h4>';
										content += '</div>';
										content += '<div id="' + textID + '" class="ib2-content-el ib2-text-el" data-el="text" data-shadow="none" data-animation="none" style="position: relative;" spellcheck="false">';
											content += '<p><span data-mce-style="color: #808080;" style="color: rgb(128, 128, 128);">You can insert a short explanation about your product/module here. Explain the core or main benefit of this product/module, and maybe insert an ecover of your product here.</span></p>';
										content += '</div>';
										content += '<div id="' + listID + '" class="ib2-content-el ib2-text-el" data-el="text" data-shadow="none" data-animation="none" style="position: relative;" spellcheck="false">';
											content += '<ul class="green-check2">';
												content += '<li><span data-mce-style="color: #808080;" style="color: rgb(128, 128, 128);"><strong>Product Benefit 1 -</strong> Killer line that explain the product/module benefit #1 goes here...</span></li>';
												content += '<li><span data-mce-style="color: #808080;" style="color: rgb(128, 128, 128);"><strong>Product Benefit 2 -</strong> Killer line that explain the product/module benefit #2 goes here...</span></li>';
												content += '<li><span data-mce-style="color: #808080;" style="color: rgb(128, 128, 128);"><strong>Product Benefit 3 -</strong> Killer line that explain the product/module benefit #3 goes here...</span></li>';
												content += '<li><span data-mce-style="color: #808080;" style="color: rgb(128, 128, 128);"><strong>Product Benefit 4 -</strong> Killer line that explain the product/module benefit #4 goes here...</span></li>';
											content += '</ul>';
										content += '</div>';
									content += '</div>';
								content += '</div>';
							content += '</div>';
						content += '</div>';
					content += '</div>';
				content += '</div>';
				
				ui.item.replaceWith(content);
			}
			
			// Other/Unknown Element Dropped
			else {
				ui.item.replaceWith('');
			}
			
			if ( type == 'box' || type == 'tabs' || type == 'quiz' || type == 'optin3' || type == 'section'
			|| type == 'text_image' || type == 'box_video' || type == 'product_box' || type == 'columns' ) {
				// Re-init sortable for box element...
				if ( $is_popup == 1 )
					ib2_reinit_sortable($('.ib2-pop-content').find('.ib2-section-content'), 'content');
				else
					ib2_reinit_sortable($('.ib2-section-content'), 'content');
			}

			$('#screen-container').find('.ib2-element').remove();
			
			$("body").trigger("ib2ElDrop", [type, newID]);
			$is_drag = 0;
		} else {
			$("body").trigger("ib2CommonChange");
		}
		$('body').trigger("ib2autosave");
	}

	function ib2CreateEditor(id) {
		var imgFolder = $('#ib2-img-folder').val();
	    txtEd = new tinymce.Editor(id, {
	        inline: true,
	        plugins: [
      			"autolink lists link image charmap code insertdatetime table",
	        	"contextmenu paste textcolor colorpicker textpattern",
	        ],
	        toolbar: "undo redo | styleselect | formatselect | pastetext | bold italic underline | alignleft aligncenter alignright | forecolor backcolor | bullist numlist | link | removeformat | fontselect fontsizeselect | code",
	        menubar: false,
	        paste_word_valid_elements: "b,strong,i,em,h1,h2",
	    	relative_urls : false,
	    	convert_urls : false,
	    	style_formats: [
			    {title: "Inline", items: [
			        {title: "Strikethrough", icon: "strikethrough", format: "strikethrough"},
			        {title: "Superscript", icon: "superscript", format: "superscript"},
			        {title: "Subscript", icon: "subscript", format: "subscript"},
			        {title: "Code", icon: "code", format: "code"}
			    ]},
			    {title: "Blocks", items: [
			        {title: "Blockquote", format: "blockquote"},
			        {title: "Div", format: "div"},
			        {title: "Pre", format: "pre"}
			    ]},
			    {title: "Text Style", items: [
			        {title: "Text Shadow (Dark)", inline: "span", classes: "d-text-shadow"},
			        {title: "Text Shadow (Grey)", inline: "span", classes: "g-text-shadow"},
			        {title: "Text Shadow (Light)", inline: "span", classes: "l-text-shadow"},
			        {title: "No Space", inline: "span", styles: {letterSpacing: '-3px'}},
			        {title: "Too Much Space", inline: "span", styles: {letterSpacing: '8px'}},
			    ]},
			    {title: "Font Weight", items: [
			        {title: "Normal", inline: "span", styles: {fontWeight: 'normal'}},
			        {title: "Bold", inline: "span", styles: {fontWeight: 'bold'}},
			        {title: "300", inline: "span", styles: {fontWeight: '300'}},
			  		{title: "400", inline: "span", styles: {fontWeight: '400'}},
			  		{title: "600", inline: "span", styles: {fontWeight: '600'}},
			  		{title: "700", inline: "span", styles: {fontWeight: '700'}},
			  		{title: "800", inline: "span", styles: {fontWeight: '800'}},
			  		{title: "900", inline: "span", styles: {fontWeight: '900'}},
			    ]},
			    {title: "Bullet", items: [
			        {title: "Check #1 (green)", selector: "ul", attributes : {'class' : 'green-check1'}},
			        {title: "Check #1 (red)", selector: "ul", attributes : {'class' : 'red-check1'}},
			        {title: "Check #1 (blue)", selector: "ul", attributes : {'class' : 'blue-check1'}},
			        {title: "Check #2 (green)", selector: "ul", attributes : {'class' : 'green-check2'}},
			        {title: "Check #2 (red)", selector: "ul", attributes : {'class' : 'red-check2'}},
			        {title: "Check #2 (blue)", selector: "ul", attributes : {'class' : 'blue-check2'}},
			        {title: "Check #3 (green)", selector: "ul", attributes : {'class' : 'green-check3'}},
			        {title: "Check #3 (red)", selector: "ul", attributes : {'class' : 'red-check3'}},
			        {title: "Check #3 (blue)", selector: "ul", attributes : {'class' : 'blue-check3'}},
			        {title: "Big Plus", selector: "ul", attributes : {'class' : 'big-plus'}},
			        {title: "Small Plus", selector: "ul", attributes : {'class' : 'small-plus'}},
			        {title: "Cross #1", selector: "ul", attributes : {'class' : 'cross-list1'}},
			        {title: "Cross #2", selector: "ul", attributes : {'class' : 'cross-list2'}},
			        {title: "Forbidden", selector: "ul", attributes : {'class' : 'forbid-list1'}},
			        {title: "Big Arrow Green", selector: "ul", attributes : {'class' : 'big-arrow-green-list'}},
			        {title: "Big Arrow Blue", selector: "ul", attributes : {'class' : 'big-arrow-blue-list'}},
			        {title: "Arrow Green", selector: "ul", attributes : {'class' : 'arrow-green-list'}},
			        {title: "Arrow Blue", selector: "ul", attributes : {'class' : 'arrow-blue-list'}},
			        {title: "Lock", selector: "ul", attributes : {'class' : 'lock-list'}},
			        {title: "Tag", selector: "ul", attributes : {'class' : 'tag-list'}},
			        {title: "Star", selector: "ul", attributes : {'class' : 'star-list'}},
			        {title: "Bulb", selector: "ul", attributes : {'class' : 'bulb-list'}},
			        {title: "Zoom", selector: "ul", attributes : {'class' : 'zoom-list'}},
			        {title: "Heart", selector: "ul", attributes : {'class' : 'heart-list'}}
			    ]},
			    {title: "Drop Caps", items: [
			    	{title: "Drop Cap Red", inline: "span", attributes : {'class' : 'ib2-dc-red'}},
			    	{title: "Drop Cap Yellow", inline: "span", attributes : {'class' : 'ib2-dc-yellow'}},
			    	{title: "Drop Cap Green", inline: "span", attributes : {'class' : 'ib2-dc-green'}},
			    	{title: "Drop Cap Blue", inline: "span", attributes : {'class' : 'ib2-dc-blue'}},
			    	{title: "Drop Cap Purple", inline: "span", attributes : {'class' : 'ib2-dc-purple'}},
			    	{title: "Drop Cap Black", inline: "span", attributes : {'class' : 'ib2-dc-black'}},
			    	{title: "Drop Cap Orange", inline: "span", attributes : {'class' : 'ib2-dc-orange'}},
			    	{title: "Drop Cap Pink", inline: "span", attributes : {'class' : 'ib2-dc-pink'}},
			    ]},
				{title: "Quick Hightlight", items: [
			   		{title: "Text Highlight Red", inline: "span", styles: {backgroundColor: '#f51f29', color: '#FFFFFF'}},
			   		{title: "Text Highlight Yellow", inline: "span", styles: {backgroundColor: '#fdf957'}},
			   		{title: "Text Highlight Green", inline: "span", styles: {backgroundColor: '#99e32a'}},
			   		{title: "Text Highlight Blue", inline: "span", styles: {backgroundColor: '#38b4fc', color: '#FFFFFF'}},
			   		{title: "Text Highlight Purple", inline: "span", styles: {backgroundColor: '#891fcc', color: '#FFFFFF'}},
			   		{title: "Text Highlight Black", inline: "span", styles: {backgroundColor: '#111111', color: '#FFFFFF'}},
			   		{title: "Text Highlight Orange", inline: "span", styles: {backgroundColor: '#fbab27'}},
			   		{title: "Text Highlight Pink", inline: "span", styles: {backgroundColor: '#da0764', color: '#FFFFFF'}},
			   	]},
	    	],
	    	fontsize_formats: "9px 10px 12px 13px 14px 16px 18px 20px 24px 28px 32px 36px 42px 48px 52px 56px 64px 68px 72px",
	        font_formats: "Andale Mono=andale mono,times;"+
		        "Arial=arial,helvetica,sans-serif;"+
		        "Arial Black=arial black,avant garde;"+
		        "Book Antiqua=book antiqua,palatino;"+
		        "Comic Sans MS=comic sans ms,sans-serif;"+
		        "Courier New=courier new,courier;"+
		        "Georgia=georgia,palatino;"+
		        "Helvetica=helvetica;"+
		        "Impact=impact,chicago;"+
		        "Symbol=symbol;"+
		        "Tahoma=tahoma,arial,helvetica,sans-serif;"+
		        "Terminal=terminal,monaco;"+
		        "Times New Roman=times new roman,times;"+
		        "Trebuchet MS=trebuchet ms,geneva;"+
		        "Verdana=verdana,geneva;"+
		        "Webdings=webdings;"+
		        "Wingdings=wingdings,zapf dingbats;"+
		        "Allura=Allura,cursive;"+
				"Architects Daughter=Architects Daughter,cursive;"+
				"Arvo=Arvo,serif;"+
				"Bevan=Bevan,cursive;"+
				"Boogaloo=Boogaloo,cursive;"+
				"Bowlby One=Bowlby One,cursive;"+
				"Cabin=Cabin,sans-serif;"+
				"Codystar=Codystar,cursive;"+
				"Covered By Your Grace=Covered By Your Grace,cursive;"+
				"Crafty Girl=Crafty Girl,cursive;"+
				"Dancing Script=Dancing Script,cursive;"+
				"Droid Sans=Droid Sans,sans-serif;"+
				"Droid Serif=Droid Serif,serif;"+
				"Exo=Exo,sans-serif;"+
				"Ewert=Ewert,cursive;"+
				"Flavors=Flavors,cursive;"+
				"Finger Paint=Finger Paint,cursive;"+
				"Gloria Hallelujah=Gloria Hallelujah,cursive;"+
				"Henny Penny=Henny Penny,cursive;"+
				"Jacques Francois Shadow=Jacques Francois Shadow,cursive;"+
				"Kaushan Script=Kaushan Script,cursive;"+
				"Lobster=Lobster,cursive;"+
				"Monofett=Monofett,cursive;"+
				"Mountains of Christmas=Mountains of Christmas,cursive;"+
				"Noto Sans=Noto Sans,sans-serif;"+
				"Nova Mono=Nova Mono,cursive;"+
				"Open Sans=Open Sans,sans-serif;"+
				"Open Sans Condensed=Open Sans Condensed,sans-serif;"+
				"Permanent Marker=Permanent Marker,cursive;"+
				"PT Sans=PT Sans,sans-serif;"+
				"PT Sans Narrow=PT Sans Narrow,sans-serif;"+
				"PT Serif=PT Serif,serif;"+
				"Rock Salt=Rock Salt,cursive;"+
				"Rokkitt=Rokkitt,serif;"+
				"Sansita One=Sansita One,cursive;"+
				"Shadows Into Light=Shadows Into Light,cursive;"+
				"Sirin Stencil=Sirin Stencil,cursive;"+
				"Special Elite=Special Elite,cursive;"+
				"Ubuntu=Ubuntu,sans-serif;"+
				"VT323=VT323,cursive;"+
				"Vollkorn=Vollkorn,serif",
				
	        setup: function( editor ) { 
	            editor.on('init', function(e) {
	                editor.focus();
	            });
	            editor.on('blur', function(e) {
	                editor.remove();
	                if ( $is_popup == 1 ) {
	                	$('.ib2-pop-content')
							.unbind('sortable')
							.sortable({
								update: ib2_element_drop,
								placeholder: 'sortable-line'
							});
	                } else {
	                	if ( destroyed ) {
	                		$is_text_edit = 0;
	                		ib2_init();
	                	}
	                }
	                
	                $('#' + id).css('padding', 0);
					$("body").trigger("ib2CommonChange");
	                return false;
	            });
	        }
	    }, tinymce.EditorManager);
	    txtEd.render();
	}

    function hoj( d, tags ) {
        /* HTML entity */

        if ( tags == 2 ) {
            if ( d == '<' )
                return '&lt;';
            else if ( d == '>' )
                return '&gt;';
        }

        if ( tags == 1 ) {
            if ( d == '<' )
                return '&lt;';
            if ( d == '>' )
                return '&gt;';
        }

        return d;
    }

    function htmlEntity( aa, tags ) {
    	if ( aa == '' ) return aa;
        var bb = '';
        for ( i = 0; i < aa.length; i++ ) 
            bb += hoj(aa.charAt(i), tags);

        return bb;
    }

	function ib2_video_data( type ) {
		var elID = $('#ib2-current-video').val(),
		mp4 = $('#video-mp4').val(), ogg = $('#video-ogg').val(), webm = $('#video-webm').val(),
		youtube = $('#video-youtube').val(), vimeo = $('#video-vimeo').val(), embed = htmlEntity($('#video-embed').val(), 1),
		splash = $('#video-splash').val(), autoplay = ( $('#video-autoplay').is(":checked") ) ? 1 : 0,
		controls = ( $('#video-no-control').is(":checked") ) ? 0 : 1,
		src = '', width = $('#' + elID).width(), height = $('#' + elID).height();
		
		if ( embed != '' )
			embed = embed.replace(/(\r\n|\n|\r)/gm,"");
		
		if ( !videoData[elID] || typeof videoData[elID] === 'undefined' || videoData[elID] == null ) {
			videoData[elID] = {};
		}
		
		videoData[elID].type = type;
		videoData[elID].embed = Base64.encode(embed);
		videoData[elID].autoplay = autoplay;
		videoData[elID].controls = controls;
		
		src += $('#ib2-player').val();
		if ( mp4 != '' ) src += '&mp4=' + encodeURIComponent(mp4); else src += '&mp4='; 
		if ( ogg != '' ) src += '&ogg=' + encodeURIComponent(ogg); else src += '&ogg=';
		if ( webm != '' ) src += '&webm=' + encodeURIComponent(webm); else src += '&webm=';
		if ( splash != '' ) src += '&splash=' + encodeURIComponent(splash); else src += '&splash=';
		src += '&autoplay=' + autoplay + '&controls=' + controls;

		// Hosted
		videoData[elID].hosted = {
			mp4: Base64.encode(mp4),
			ogg: Base64.encode(ogg),
			webm: Base64.encode(webm),
			splash: Base64.encode(splash),
			code: Base64.encode(src)
		};

		// YouTube
		var videoID = youtube.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);
		height = Math.ceil(width * 0.5625);
		var ytID = '';
		src = '';
		if ( videoID != null ) {
			ytID = videoID[1],
			params = '&rel=0&modestbranding=0&showinfo=0&ytid=' + ytID;
	   		if ( autoplay == 1 ) params += '&autoplay=1';
	   		if ( controls == 0 ) params += '&controls=0';
			src += 'http://www.youtube.com/embed/' + ytID + '?wmode=transparent' + params;
		}
		
		videoData[elID].youtube = {
			url: Base64.encode(youtube),
			id: ytID,
			code: Base64.encode(src)
		};
		
		// Vimeo
		var videoID = vimeo.match(/(videos|video|channels|\.com)\/([\d]+)/);
		height = Math.ceil(width * 0.5625);
		var vmID = '';
		src = '';
		if ( videoID != null ) {
			vmID = videoID[2],
			params = '&title=0&byline=0&portrait=0&vmid=' + vmID;
	   		if ( autoplay == 1 ) params += '&autoplay=1';
			src += 'http://player.vimeo.com/video/' + vmID + '?wmode=transparent' + params;
		}
		
		videoData[elID].vimeo = {
			url: Base64.encode(vimeo),
			id: vmID,
			code: Base64.encode(src)
		};
		
		// update data
		$('#' + elID).data('videoType', type);
		$('#' + elID).attr('data-video-type', type);
	}
	
	function ib2media( elementID, mode, num ) {
		// If the media frame already exists, reopen it.
		if ( typeof(ib2_file_frame) !== "undefined" ) {
			ib2_file_frame.close();
		}
		
		if ( typeof(mode) === "undefined" || mode == false || mode == '' ) {
			mode = 'replace';
		}

		frame_data = {
			title: 'Upload Image',
			library: {
                type: 'image'
            },
			button: {
				text: 'Use Image',
			},
			multiple: false
		};

		// Create the media frame.
		ib2_file_frame = wp.media.frames.customHeader = wp.media(frame_data);
		
		 // When an image is selected, run a callback.
		ib2_file_frame.on('select', function(){
			// We set multiple to false so only get one image from the uploader
			attachment = ib2_file_frame.state().get('selection').first().toJSON();
			
			if ( mode == 'value' ) {
				$('#' + elementID).val(attachment.url);
			} else if ( mode == 'splash' ) {
				$('#' + elementID).val(attachment.url);
				ib2_video_data('hosted');
			} else if ( mode == 'template' ) {
				ib2_template_thumb(elementID, attachment.url);
			} else if ( mode == 'background' ) {
				ib2_body_background(elementID, attachment.url);
				$('#body-bg-mode').val('upload');
			} else if ( mode == 'optin_button' ) {
				ib2_optin_image(elementID, attachment.url);
			} else if ( mode == 'box' ) {
				ib2_box_background(elementID, attachment.url);
				$('#' + elementID).data('imgMode', 'upload');
				$('#' + elementID).attr('data-img-mode', 'upload');
			} else if ( mode == 'section' ) {
				ib2_section_background(elementID, attachment.url);
				$('#' + elementID).data('imgMode', 'upload');
				$('#' + elementID).attr('data-img-mode', 'upload');
			} else if ( mode == 'image' ) {
				$('#' + elementID).find('img').attr('src', attachment.url);
				$('#' + elementID).find('.el-content').css({
					'width': attachment.width + 'px',
					'height': 'auto'
				});
				
				$('#' + elementID).find('.el-content > img').css({
					'width': attachment.width + 'px',
					'height': 'auto',
				});
				
				//$('#cur-img-width').val(attachment.width);
				//$('#cur-img-height').val(attachment.height);
				
				ib2_image(elementID, attachment.url);
			} else if ( mode == 'slider' ) {
				$('#slide-el-url-' + num).val(attachment.url);
				ib2_slider_image(elementID, attachment.url, num);
			} else if ( mode == 'favicon' ) {
				ib2_favicon(elementID, attachment.url, attachment.mime);
				console.log(attachment);
			} else {
				$('#' + elementID).find('img').attr('src', attachment.url);
				$('#' + elementID).find('.el-content').css({
					'width': attachment.width + 'px',
					'height': attachment.height + 'px',
				});
			}
		});
		
		 // Finally, open the modal
		ib2_file_frame.open();
	}
	
	function ib2_favicon( elementID, url, mime ) {
		$("#" + elementID).val(url);
		if ( mime != 'image/vnd.microsoft.icon' ) {
			$('#favicon-prev').html('<img src="' + url + '" class="img-responsive" />');
		}
		$('#favicon-el-rmv').show();
	}
	
	function ib2_slider_num_sort() {
		$('.slide-settings').each(function(i){
			var s = $(this), label = i+1;
			s.find('.slider-num').text(label);
		});
	}
	
	function ib2_slider_setting( num ) {
		var output = '';
		output += '<div class="form-group slide-settings slide-setting-' + num + '">';
	    	output += '<button type="button" class="close delete-slide-settings" data-slide-num="' + num + '"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
	  		output += '<label for="box-image">Slide Image #<span class="slider-num">X</span></label>';
	  		output += '<input type="text" class="form-control slider-image-field" id="slide-el-url-' + num + '" value="" placeholder="e.g. http://mydomain.com/image.jpg" data-slide-num="' + num + '" />';
	  		output += '<p class="help-block">Enter an image URL into the field above, or click the "Upload" button below to upload an image.</p>';
	        output += '<p style="margin-top:10px">';
	        	output += '<button class="btn btn-primary btn-sm slide-image-upload-btn" data-slide-num="' + num + '" type="button">Upload Image</button>';
	        output += '</p>';
	        
	        output += '<label for="box-image">Slide #<span class="slider-num">X</span> Title</label>';
	  		output += '<input type="text" class="form-control slider-title-field" id="slide-el-title-' + num + '" value="" placeholder="e.g. My Slide Title" data-slide-num="' + num + '" />';
	  		output += '<p class="help-block">You can add a title for this slide (optional).</p>';
	  		
	  		output += '<label for="box-image">Slide #<span class="slider-num">X</span> Destination URL</label>';
	  		output += '<input type="text" class="form-control slider-url-field" id="slide-el-desturl-' + num + '" value="" placeholder="e.g. http://mydomain.com/mypage" data-slide-num="' + num + '" />';
	  		output += '<p class="help-block">You can make the slide image clickable by entering the destination URL (optional).</p>';
	        output += '<hr>';
	    output += '</div>';
	    
	    return output;
	}
	
	function ib2_slider_image( elID, image_url, num ) {
		if ( !carouselData[elID] || typeof carouselData[elID] === 'undefined' || carouselData[elID] == null ) {
			carouselData[elID] = {};
		}
		
		if ( !carouselData[elID][num] || typeof carouselData[elID][num] === 'undefined' || carouselData[elID][num] == null ) {
			carouselData[elID][num] = {};
		}
		
		carouselData[elID][num].imageurl = image_url;
		carouselData[elID][num].title = $('#slide-el-title-' + num).val();
		carouselData[elID][num].desturl = $('#slide-el-desturl-' + num).val();
	}
	
	function ib2_optin_image( elementID, img ) {
		$('#' + elementID + '-image').attr('src', img);
		$('#image-button-url').val(img);
		$('#button-img-rmv').show();
				

		// Preview
		if ( !$('#button-image-prev').find('img').length ) {
			$('#button-image-prev').html('<img id="button-prev-img" class="img-thumbnail img-responsive" border="0" />');
		}
		
		$('#button-image-prev').find('img').attr('src', img);
	}
	
	function ib2_image( elementID, img ) {
		$('#image-el-url').val(img);
		$('#image-el-rmv').show();
				
		// Preview
		if ( !$('#image-el-prev').find('img').length ) {
			$('#image-el-prev').html('<img id="image-el-prev-img" class="img-thumbnail img-responsive" border="0" />');
		}
				
		$('#image-el-prev').find('img').attr('src', img);
	}
	
	function ib2_template_thumb( elementID, img ) {
		if ( !$('#template-thumb-preview').find('img').length ) {
			$('#template-thumb-preview').html('<img src="" border="" class="img-responsive img-thumbnail" />');
		}
		$('#template-thumb-preview').find('img').attr('src', img);
		$('#' + elementID).val(img);
		
		$('#remove-template-thumb').show();
	}
	
	function ib2_body_background( elementID, img ) {
		$('#body-bg-url').val(img);
		$('#' + elementID).css({
			'background-image': 'url("' + img + '")',
			'background-repeat': $('#background-repeat').val(),
			'background-position': $('#background-pos').val(),
			'background-attachment': $('#background-attach').val()
		});
				
		$('#background-img-rmv').show();
				
		// Preview
		if ( !$('#background-image-prev').find('img').length ) {
			$('#background-image-prev').html('<img id="background-image-prev-img" class="img-thumbnail img-responsive" border="0" />');
		}
				
		$('#background-image-prev').find('img').attr('src', img);
	}
	
	function ib2_section_background( elementID, img ) {
		$('#box-bg-url').val(img);
		$('#' + elementID + ' > .el-content').css({
			'background-image': 'url("' + img + '")',
			'background-repeat': $('#content-bgrepeat').val(),
			'background-position': $('#content-bgpos').val(),
			'background-attachment': $('#content-bgattach').val()
		});
				
		$('#box-img-rmv').show();
				
		// Preview
		if ( !$('#box-image-prev').find('img').length ) {
			$('#box-image-prev').html('<img id="box-image-prev-img" class="img-thumbnail img-responsive" border="0" />');
		}
				
		$('#box-image-prev').find('img').attr('src', img);
	}
	
	function ib2_box_background( elementID, img ) {
		$('#box-bg-url').val(img);
		var element = $('#' + elementID + '-box'); 
		element.css({
			'background-image': 'url("' + img + '")',
			'background-repeat': $('#content-bgrepeat').val(),
			'background-position': $('#content-bgpos').val(),
			'background-attachment': $('#content-bgattach').val()
		});
				
		$('#box-img-rmv').show();
				
		// Preview
		if ( !$('#box-image-prev').find('img').length ) {
			$('#box-image-prev').html('<img id="box-image-prev-img" class="img-thumbnail img-responsive" border="0" />');
		}
				
		$('#box-image-prev').find('img').attr('src', img);
	}
	
	function getUrlVars( url ) {
		var vars = {};
    	var parts = url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
        	vars[key] = value;
    	});
    	return vars;
	}
	
	function generateID( length ) {
	    var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');
	
	    if ( !length ) {
	        length = Math.floor(Math.random() * chars.length);
	    }
	
	    var str = '';
	    for ( var i = 0; i < length; i++ ) {
	        str += chars[Math.floor(Math.random() * chars.length)];
	    }
	    
	    if ( $('#' + str).length )
	    	str = generateID(length);
	    	
    	return str;
	}
	
	function getAltColor( color, type ) {
		var num = 35;
		if ( type == 'darker' ) num = -28;
		
		if ( color.substr(0, 1) === "#" ) {
			var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    		color = color.replace(shorthandRegex, function(m, r, g, b) {
        		return r + r + g + g + b + b;
    		});
    		
    		var hex = color.replace('#', ''),
		    bigint = parseInt(hex, 16),
		    r = ((bigint >> 16) & 255) + num,
		   	g = ((bigint >> 8) & 255) + num,
		    b = (bigint & 255) + num;

		} else {
			var nums = /(.*?)rgb\((\d+),\s*(\d+),\s*(\d+)\)/i.exec(color),
	        r = nums[2] + num,
	        g = nums[3] + num,
	        b = nums[4] + num;
		}
		
		if ( r > 255 ) r = 255; if ( r < 0 ) r = 0;
		if ( g > 255 ) r = 255; if ( g < 0 ) g = 0;
		if ( b > 255 ) r = 255; if ( b < 0 ) b = 0;
		
		return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
	}
	
	function componentToHex(c) {
	    var hex = c.toString(16);
	    return hex.length == 1 ? "0" + hex : hex;
	}
	
	function isNumber ( obj ) {
		return !jQuery.isArray( obj ) && (obj - parseFloat( obj ) + 1) >= 0;
	}
    
	// UNDO SCRIPT
	var undo = $("#ib2-undo"),
	redo = $("#ib2-redo");
	
	$('body').on("click", "#ib2-undo", function(e){
		if ( $is_popup == 1 ) {
			alert('Undo feature is disabled during pop-up editing.');
			return false;
		}
		
		if ( canUndo )
			ib2_stack_action('undo');
		e.preventDefault();
	});
	
	$('body').on("click", "#ib2-redo", function(e){
		if ( $is_popup == 1 ) {
			alert('Redo feature is disabled during pop-up editing.');
			return false;
		}
		
		if ( canRedo )
			ib2_stack_action('redo');
		e.preventDefault();
	});
	
	$("body").on("ib2GlobalChange", function( e, param1, param2, param3, param4 ){
		if ( $is_popup == 1 ) return false;
		ib2_save_stack('global', [param1, param2, param3, param4]);
	});
	
	$("body").on("ib2ElDrop", function( e, param1, param2 ){
		if ( $is_popup == 1 ) return false;
		ib2_save_stack('drop', [param1, param2]);
	});
	
	$("body").on("ib2CommonChange", function(){
		if ( $is_popup == 1 ) return false;
		ib2_save_stack('common');
	});

	$("body").on("popupclose", function(){
		ib2_stack_ui();
	});
	
	function ib2_undo_init() {
		var globdata = stackGlobalData();
		startStackGlobal = globdata;
		lastStackAction = 'init';
		ib2_stack_ui();
	}
	
	function ib2_stack_ui() {
		if ( stackNum >= 0 ) {
			canUndo = true;
			$("#ib2-undo").parent().removeClass('disabled');
		} else {
			canUndo = false;
			$("#ib2-undo").parent().addClass('disabled');
		}
			
		if ( stackNum < (stackData.length - 1) ) {
			canRedo = true;
			$("#ib2-redo").parent().removeClass('disabled');
		} else {
			canRedo = false;
			$("#ib2-redo").parent().addClass('disabled');
		}
	}
	
	function ib2_stack_action( mode ) {
		if ( $is_autosave == 1 || $is_text_edit == 1 ) return false;
		
		if ( mode == 'redo' )
			stackNum++;
			
		doingStack = true;
		ib2_destroy();

		var html = ( mode == 'redo' ) ? stackData[stackNum].newval : stackData[stackNum].oldval;
		var globdata = ( mode == 'redo' ) ? stackGlobal[stackNum].newval : stackGlobal[stackNum].oldval;
		var typedata = ( mode == 'redo' ) ? stackType[stackNum].newval : stackType[stackNum].oldval;
		
		$('#screen-container').html(html);
		$('#editor-body-typo').html(globdata.css);
		
		var allcss = globdata.allcss;
		if ( $('.ib2-element-css').length && allcss.length ) {
			$('.ib2-element-css').remove();
			$.each( allcss, function( key, value ){
				$('head').append('<style id="' + key + '" class="ib2-element-css">' + value + '</style>');
			});
		}
			
		if ( ("type" in typedata) && typedata.type == 'global' ) {
			if ( typedata.plugin == 'slider' ) {
				$('#' + typedata.id).slider("value", typedata.val);
			} else if ( typedata.plugin == 'picker' ) {
				$('#' + typedata.id).iris("color", typedata.val);
			} else if ( typedata.plugin == 'input' ) {
				$('#' + typedata.id).val(typedata.val);
			}
		} else {
			if ( $('#editor-panel-inside').is(":visible") ) {
				hideSidePanel.click();
			}
		}

		if ( mode == 'redo' ) {
			lastStackAction = 'redo';
		} else {
			stackNum--;
			lastStackAction = 'undo';
		}

		ib2_stack_ui();
		
		ib2_init();
		doingStack = false;
	}

	function ib2_save_stack( type, params ) {
		ib2_destroy();
		
		stackData = stackData.slice(0, stackNum + 1);
		stackGlobal = stackGlobal.slice(0, stackNum + 1);
		stackType = stackType.slice(0, stackNum + 1);

		var newContent = $('#screen-container').html(),
		newglobdata = stackGlobalData(), newtypedata = {};
		
		if ( type == 'global' ) {
			newtypedata = { type: 'global', plugin: params[0], id: params[1], val: params[2]};
			if ( lastStackAction == 'init' && (params[3] in startStackGlobal) ) {
				var oldvalue = startStackGlobal[params[3]];
				startStackType = { type: 'global', plugin: params[0], id: params[1], val: oldvalue};
			}
		}
		
		if ( startStack != newContent ) {
			stackNum++;
			
			stackData[stackNum] = { oldval: startStack, newval: newContent };
			stackGlobal[stackNum] = { oldval: startStackGlobal, newval: newglobdata };
			stackType[stackNum] = { oldval: startStackType, newval: newtypedata };
			
			startStack = newContent;
			startStackGlobal = newglobdata;
			startStackType = newtypedata;
		}
		
		ib2_stack_ui();
		lastStackAction = 'save';
		
		ib2_init();
	}
	
	function stackGlobalData() {
		var allcss = {};
		$('.ib2-element-css').each(function(i){
			var c = $(this), id = c.attr('id');
			allcss[id] = $.trim(c.html());
		});
		
		var cstyle = ( $('#screen-container').attr('style') ) ? $('#screen-container').attr('style') : '';
		var popup = $('#main-popup-enable').is(":checked") ? 1 : 0;
		var slider_content = $('#ib2-bottom-slider').html();
		var slider = $('#main-slider-enable').is(":checked") ? 1 : 0;
		var slider_close = $('#main-slider-close').is(":checked") ? 1 : 0;
		var attention = $('#attention-bar-enable').is(":checked") ? 1 : 0;
		var exit_splash = $('#exit-splash-enable').is(":checked") ? 1 : 0;
		var wgate = $('#wgate-enable').is(":checked") ? 1 : 0;
		var circular = $('.ib2-circular-countdown').length ? 1 : 0;
		
		var bgvideomute = $('#background-video-mute').is(":checked") ? 1 : 0;
		var bgvideoloop = $('#background-video-loop').is(":checked") ? 1 : 0;
		var bgvideoctrl = $('#background-video-ctrl').is(":checked") ? 1 : 0;
		
		var data = {
			allcss: allcss,
			pageWidth: $('#page-width').val(),
			fontFace: $('#body-text-font').val(),
			fontColor: $('#body-text-color').val(),
			fontSize: $('#body-text-size').val(),
			linkColor: $('#body-link-color').val(),
			linkHoverColor: $('#body-link-hover-color').val(),
			backgroundColor: $('#background-color').val(),
			backgroundImg: $('#body-bg-url').val(),
			backgroundImgMode: $('#body-bg-mode').val(),
			backgroundRepeat: $('#background-repeat').val(),
			backgroundPos: $('#background-pos').val(),
			backgroundAttach: $('#background-attach').val(),
			backgroundVideo: $('#background-video').val(),
			backgroundVideoMute: bgvideomute,
			backgroundVideoLoop: bgvideoloop,
			backgroundVideoCtrl: bgvideoctrl,
			title: $('#page-title').val(),
			metaDesc: $('#page-desc').val(),
			metaKeys: $('#page-keywords').val(),
			noindex: $('#meta_noindex').is(":checked"),
			nofollow: $('#meta_nofollow').is(":checked"),
			noodp: $('#meta_noodp').is(":checked"),
			noydir: $('#meta_noydir').is(":checked"),
			noarchive: $('#meta_noarchive').is(":checked"),
			popup: popup,
			popupTime: $('#main-popup-time').val(),
			popupId: $('#ib2-main-popup-id').val(),
			slider: slider,
			sliderTime: $('#main-slider-time').val(),
			sliderContent: slider_content,
			sliderClose: slider_close,
			attentionBar: attention,
			attentionBarText: $.trim($('#attention-bar-text').val()),
			attentionBarTime: $('#attention-bar-time').val(),
			attentionBarAnchor: $.trim($('#attention-bar-anchor').val()),
			attentionBarUrl: $.trim($('#attention-bar-url').val()),
			attentionBarBackground: $('#attention-bar-background').val(),
			attentionBarBorder: $('#attention-bar-border').val(),
			attentionBarFont: $('#attention-bar-font').val(),
			attentionBarFontcolor: $.trim($('#attention-bar-fontcolor').val()),
			exitSplash: exit_splash,
			exitMsg: $('#exit-splash-msg').val(),
			exitUrl: $('#exit-splash-url').val(),
			welcomeGate: wgate,
			wgateId: $('#locked-page').val(),
			headScripts: $('#head-scripts').val(),
			bodyScripts: $('#body-scripts').val(),
			footerScripts: $('#footer-scripts').val(),
			css: $('#editor-body-typo').html(),
			contentStyle: cstyle,
			circular: circular
		};
		
		return data;
	}
	
	
	$('#del-current-variant').click(function(){
		if ( confirm("Are you sure you want to delete this variation?") ) {
			exitingEditor = true;
			return true;
		} else {
			return false;
		}
	});
	
	// CONFIRM ON EDITOR EXIT
	$(window).bind('beforeunload', function(){
		if ( exitingEditor == false ) {
			window.scrollTo(0,0);
			
			return 'Are you sure you want to quit the editor?\nAll unsaved progress will be lost.';
		}
	});
})(jQuery);