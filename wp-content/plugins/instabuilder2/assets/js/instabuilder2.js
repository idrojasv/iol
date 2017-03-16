;(function($) {
	var $is_popup = 0, current_popup = '', $is_slider = 0, leavepop = 0;
	
	var pgIframes = {};
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
    
	// Powered By
	if ( powered_by == 'yes' ) {
		if ( !$('#ib2-powered-by').length ) {
			var aff_url = powered_by_link;
		
			if ( aff_url == '' ) aff_url = 'http://instabuilder.com';
			$('#entire_wrapper').append('<div id="ib2-powered-by"><a href="' + aff_url + '" target="_blank"><img src="' + powered_img + '" class="img-responsive" /></a></div>');
		}
	} else {
		if ( $('#ib2-powered-by').length ) {
			$('#ib2-powered-by').remove();
		}
	}
		
	// Adjust font size for mobile
	$('p').find('span').each(function(){
		var ps = $(this), origFont = ps.css('fontSize').replace(/px/gi, '');
		if ( origFont > 18 && $(window).width() <= 480 ) {
			var newSize = 0.7 * origFont;
			ps.css('font-size', newSize + 'px');
		}
	});
	
	$(window).load(function(){
		// Fix TwentyFifteen CSS conflict
		$('#twentyfifteen-style-css').remove();
		$('#twentyfifteen-fonts-css').remove();
		$('#twentyfifteen-ie-css').remove();
		$('#twentyfifteen-ie7-css').remove();
		
		// Date Elements
		if ( $('.ib2-date-el').length ) {
			$('.ib2-date-el').each(function(){
				var d = $(this), format = d.data('format'), offset = d.data('tz');
				
				d.text(moment().tz(offset).format(format));
			});
		}
		
		// Try to fix sub-menu display
		if ( $('.ib2-section-el').length ) {
			var zindex = 999;
			$('.ib2-section-el').each(function(i){
				var sec = $(this);
				
				if ( !sec.find('.ib2-navi').length ) return true;
				
				sec.css('z-index', zindex);
				
				zindex = zindex - 1;
				
			});
		}
		// Fix Webinar form URL
		if ( $('form').length ) {
			$('form').each(function(i){
				var f = $(this);
				if ( f.find('input[name=_webinar_key]').length ) {
					f.attr('action', webinar_url);
				}
			});
		}
		
		// Fix Video
		if ( $('.ib2-video-container').length ) {
			$('.ib2-video-container').each(function(i){
				var v = $(this);
				if ( v.find('script').length ) {
					v.find('.embed-responsive').removeClass('embed-responsive embed-responsive-16by9');
				}
			});
		}
		
		// remove unnecessary elements
		$('.gr-textarea-btn').parent().remove();
		$('.gr-textarea-btn').remove();
		
		// unused menu button...
		$('button.ib2-navbar-toggle').remove();
		
		$('[id]').each(function (i) {
    		var ids = $('[id="' + this.id + '"]');
    		if ( ids.length > 1 ) {
    			if ( ids.hasClass('ib2-content-el') || ids.hasClass('ib2-section-el') )
    				$('[id="' + this.id + '"]:gt(0)').remove();
    		}
		});
		
		if ( $('.ib2-menu-el').length ) {
			$('.ib2-menu-el').each(function(i){
				var m = $(this), menu = m.data('menu'), style = m.data('style'),
				id = m.attr('id');
				
				if ( menu == 'none' || m.hasClass('ib2-new-navi') || m.hasClass('ib2-updated-navi') ) return true;
				
				$.post(ib2ajaxurl, {
						action: 'update_menu',
						menu: menu,
						style: style,
						menu_id: id
					}, function(response){
						if ( response.success ) {
							m.find('nav').html(response.output);
						}
					});
			});
		}
	});
	
	// ================== VIDEO BACKGROUND CONTROL ======================
	$('body').on('click', '.bgvid-play', function(e) { // play button
            $('.ib2-ytube-play').hide();
			$('.ib2-ytube-pause').show();
            player.playVideo();

            e.preventDefault();
        });

    $('body').on('click', '.bgvid-pause', function(e) { // pause button
            $('.ib2-ytube-play').show();
			$('.ib2-ytube-pause').hide();
            player.pauseVideo();

            e.preventDefault();
        });

    $('body').on('click', '.bgvid-mute', function(e) { // mute button
            e.preventDefault();
            var isMuted = player.isMuted();
            (isMuted) ? player.unMute() : player.mute();

            var $this = $(this);
			if ( isMuted ) {
				$('li.ib2-ytube-mute a').css('background-position', 'left top');
				$this.data('mute', 'no');
				$this.attr('data-mute', 'no');
			} else {
				$('li.ib2-ytube-mute a').css('background-position', 'left bottom');
				$this.data('mute', 'yes');
				$this.attr('data-mute', 'yes');
			}
        });

    $('body').on('click', '.bgvid-volume-down', function(e) { // volume down button
            e.preventDefault();
            var currentVolume = player.getVolume();
            if (currentVolume < 10) currentVolume = 10;
            player.setVolume(currentVolume - 10);

            if ( currentVolume <= 0 ) {
				$('li.ib2-ytube-mute a').css('background-position', 'left bottom');
				$('.tubular-mute').data('mute', 'yes');
				$('.tubular-mute').attr('data-mute', 'yes');
			}
        });

    $('body').on('click', '.bgvid-volume-up', function(e) { // volume up button
            e.preventDefault();
            if (player.isMuted()) player.unMute(); // if mute is on, unmute
            var currentVolume = player.getVolume();
            if (currentVolume > 100 - 10) currentVolume = 100 - 10;
            player.setVolume(currentVolume + 10);

            if ( currentVolume >= 1 ) {
				$('li.ib2-ytube-mute a').css('background-position', 'left top');
				$('.tubular-mute').data('mute', 'no');
				$('.tubular-mute').attr('data-mute', 'no');
			}
        });
	
	// ================== ATTENTION BAR ======================
	
	if ( ib2_attbar == 0 ) {
		$('body').css('paddingTop', '0px');
	} else {
		var attHeight = $('.ib2-notification-text').outerHeight() - 2;
		$('body').css('paddingTop', attHeight + 'px');
	}
	
	$('label[for*="nb-hide"]').click(function(e){
		$('body').css('paddingTop', '0px');
	});
	
	$('label[for*="nb-show"]').click(function(e){
		var attHeight = $('.ib2-notification-text').outerHeight() - 2;
		$('body').css('paddingTop', attHeight + 'px');
	});
	
	// ================== 3 STEPS OPTIN ======================
	$('a[href*="#go-to-step-2"]').click(function(e){
		var hw = $('.ib2-section-slide-1').outerWidth();
		$('.ib2-section-slide-1').hide({
			direction: 'left', distance: hw
		});
		$('.ib2-section-slide-2').show("bounce", {}, 500);
		e.preventDefault();	
	});
	
	$('a[href*="#go-to-step-3"]').click(function(e){
		var hw = $('.ib2-section-slide-2').outerWidth();
		$('.ib2-section-slide-2').hide({
			direction: 'left', distance: hw
		});
		$('.ib2-section-slide-3').show("bounce", {}, 500);
		e.preventDefault();
	});
	
	// ================== DELAY & ANIMATION ======================
	if ( $('.ib2-box-delay').length ) {
		$('.ib2-box-delay').each(function(){
			var b = $(this), id = b.attr('id'),
			delay = b.data('delay').split(":"),
			hour = parseInt(delay[0]), min = parseInt(delay[1]),
			secs = parseInt(delay[2]),
			anim = b.data('animation'),
			options = {};
			
			hour = hour * 60 * 60;
			min = min * 60;
			
			var hold = (hour + min + secs) * 1000;
			
			eldelay = setTimeout(function(){
				if ( anim == 'none' ) {
					$('#' + id).removeClass('ib2-box-delay');
				} else {
					effectClass = animation_map(anim);
					$('#' + id).removeClass('ib2-box-delay');
					
					$('#' + id).removeClass('ib2-box-animation')
							.addClass('animated ' + effectClass);	
				}
			}, hold);
		});
	}
	
	var animWinHeight = $(window).height();
	if ( $('.ib2-text-animation, .ib2-box-animation').length ) {
		var currentTop = $(window).scrollTop();
		$('.ib2-text-animation, .ib2-box-animation').each(function(){
			var te = $(this), id = te.attr('id'), anim = $('#' + id).data('animation');
			
			if ( currentTop < 10 && ( $('#' + id).offset().top < (animWinHeight*0.75) ) ) {
				effectClass = animation_map(anim);
				te.removeClass('ib2-text-animation ib2-box-animation')
					.addClass('animated ' + effectClass);
			}
		});
	}

	$(window).scroll(function () {
   		var winPos = $(window).scrollTop();
		if ( $('.ib2-text-animation, .ib2-box-animation').length ) {
			$('.ib2-text-animation, .ib2-box-animation').each(function(){
				var te = $(this), offset = te.offset(),
				id = te.attr('id'), anim = te.data('animation');
				if ( te.hasClass('animated') ) return true;
				if ( te.hasClass('ib2-box-delay') ) return true;
				if ( te.hasClass('ib2-popup') ) return true;
				if ( te.hasClass('ib2-slider-el') ) return true;
			
				if ( te.offset().top < winPos + (animWinHeight*0.75) ) {
					effectClass = animation_map(anim);
					te.removeClass('ib2-text-animation ib2-box-animation')
						.addClass('animated ' + effectClass);
			   	}
			});
		}
	});
	
	// ================== QUIZ ======================
	
	if ( $('.answers-radio').length ) {
		$('.answers-radio').each(function(){
			var id = $(this).attr('id');
			$(this).prettyCheckable();
			
			$('body').on('change', '#' + id, function(){
				var $this = $(this), answer = $this.val(),
				parts = $this.attr('id').split('_'),
				q = parts[1];
				
				if ( $this.is(":checked") ) {
					$('.quiz-loader').show();
					$('.ib2-quiz-el').css('opacity', '0.6');
					
					var prnt = $this.parent().parent().parent(),
					prnt_id = prnt.attr('id'),
					elID = prnt_id.replace('-' + q + '-answers', '');
					
					$.post(ib2ajaxurl, {
						action: 'quiz_answer',
						question: q,
						answer: answer
					}, function(response){
						if ( response.success ) {
							$('.ib2-quiz-page').hide();
							$('#' + prnt_id).parent().next('.ib2-quiz-page').show();
						}
						
						$('.quiz-loader').hide();
						$('.ib2-quiz-el').css('opacity', '1.0');
					});
				}
			});
		});
	}
	
	if ( $('.ib2-quiz-el').length ) {
		$('.ib2-quiz-el').append('<div class="quiz-loader" style="display:none"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
		
		var qtop  = (($('.ib2-quiz-el').height() / 2) - ($('.quiz-loader > i').height() / 2));
		var qleft = (($('.ib2-quiz-el').width() / 2) - ($('.quiz-loader > i').width() / 2));

		if ( qtop < 0 ) qtop = 0;
		if ( qleft < 0 ) qleft = 0;
		
		$('.quiz-loader').css({
			'position': 'absolute',
			'top': qtop,
			'left': qleft,
			'opacity': '1.0'
		});
	}
	
	// FIX VIDEO PROBLEM ON CHROME
	$(window).load(function(){
		if ( $("iframe").length ) {
			$("iframe").each(function(i){
				var iv = $(this);
				if ( iv.is(":visible") && iv.data('autoplay') == 'yes' ) {
					var src = iv.attr('src');
					if ( src != '' && src != 'http://instabuilder.com' ) {
						src += '&autoplay=1';
						iv.attr('src', src);
					}
				}
			});
		}
	});
	
	// FIX HEIGHT FOX CODE ELEMENT
	if ( $('.ib2-code-el').length ) {
		$('.ib2-code-el').each(function(){
			var tc = $(this), h = tc.css('height').replace(/px/gi, '');
			
			tc.css({
				'height': 'auto',
				'min-height': h + 'px'
			});
			
			tc.find('.el-content').css({
				'height': 'auto',
				'min-height': h + 'px'
			});
		});
	}
	
	// ================== POP UP ======================
	if ( ib2_popup == 1 ) {
		if ( ib2_poptime != 'unfocus' ) {
			$(window).load(function(){
				var options = {}, anim = '', animate = false;
				if ( $is_popup ==  1 ) return false;
				
				if ( $('#' + ib2_popid + '-popup').hasClass('ib2-box-animation') && $('#' + ib2_popid + '-popup').attr('data-animation') ) {
					anim = $('#' + ib2_popid + '-popup').data('animation');
					animate = true;
				}
			
				ib2_popup_center( $('#' + ib2_popid + '-popup') );
				
				if ( anim === "scale" ) {
					options = { percent: 100 };
				}
				
				$('.ib2-popup-bg').fadeIn("medium");
				
				if ( $("iframe").length ) {
					$("iframe").each(function(i){
						var iv = $(this);
						if ( iv.is(":visible") && iv.data('autoplay') == 'yes' ) {
							var src = iv.attr('src');
							src.replace('&autoplay=1', '');
							iv.attr('src', src);
						}
					});
				}
		
				if ( animate == true ) {
					window.setTimeout(function(){
						$('.ib2-popup-bg').fadeIn("medium");
						$('#' + ib2_popid + '-popup').show(anim, options, 500);
					}, 1000);
				} else {
					$('#' + ib2_popid + '-popup').fadeIn("slow");
				}
				
				if ( $('#' + ib2_popid + '-popup').find("iframe").length ) {
					$('#' + ib2_popid + '-popup').find("iframe").each(function(i){
						var iv = $(this);
						if ( iv.is(":visible") && iv.data('autoplay') == 'yes' ) {
							var src = iv.attr('src') + '&autoplay=1';
							iv.attr('src', src);
						}
					});
				}
		
				$is_popup = 1;
				current_popup = ib2_popid;
			});
		} else {
			$('body').mouseleave(function(){
				var options = {}, anim = '', animate = false;
				if ( $is_popup == 1 || leavepop == 1 ) return false;
				
				if ( $('#' + ib2_popid + '-popup').hasClass('ib2-box-animation') && $('#' + ib2_popid + '-popup').attr('data-animation') ) {
					anim = $('#' + ib2_popid + '-popup').data('animation');
					animate = true;
				}
				
				if ( $("iframe").length ) {
					$("iframe").each(function(i){
						var iv = $(this);
						if ( iv.is(":visible") && iv.data('autoplay') == 'yes' ) {
							var src = iv.attr('src');
							src.replace('&autoplay=1', '');
							iv.attr('src', src);
						}
					});
				}
				
				ib2_popup_center( $('#' + ib2_popid + '-popup') );
				
				if ( anim === "scale" ) {
					options = { percent: 100 };
				}
			
				$('.ib2-popup-bg').fadeIn("medium");
				
				if ( animate == true ) {
					window.setTimeout(function(){
						$('#' + ib2_popid + '-popup').show(anim, options, 500);
					}, 500);
				} else {
					$('#' + ib2_popid + '-popup').fadeIn("slow");
				}
				
				if ( $('#' + ib2_popid + '-popup').find("iframe").length ) {
					$('#' + ib2_popid + '-popup').find("iframe").each(function(i){
						var iv = $(this);
						if ( iv.is(":visible") && iv.data('autoplay') == 'yes' ) {
							var src = iv.attr('src') + '&autoplay=1';
							iv.attr('src', src);
						}
					});
				}
				
				leavepop = 1;
				$is_popup = 1;
				current_popup = ib2_popid;
			});
		}
	}
	
	$('.open-popup, .ib2-open-popup').each(function(){
		$(this).click(function(e){
			e.preventDefault();
			
			var $this = $(this), options = {},
			anim = '', animate = false,
			elID = $this.parent().parent().attr('id');
			
			var popId = '#' + elID + '-popup';
			if ( $(popId).hasClass('ib2-box-animation') && $(popId).attr('data-animation') ) {
				anim = $(popId).data('animation');
				animate = true;
			}
			
			if ( $is_popup == 1 ) return false;
			
			if ( $("iframe").length ) {
				$("iframe").each(function(i){
					var iv = $(this);
					if ( iv.is(":visible") && iv.data('autoplay') == 'yes' ) {
						var src = iv.attr('src');
						src = src.replace('&autoplay=1', '');
						iv.attr('src', src);
					}
				});
			}
				
			ib2_popup_center( $(popId) );
			
			if ( anim === "scale" ) {
				options = { percent: 100 };
			}

			$('.ib2-popup-bg').fadeIn("medium");
			
			if ( animate == true ) {
				window.setTimeout(function(){
					$(popId).show(anim, options, 500);
				}, 500);
			} else {
				$(popId).fadeIn("slow");
			}

			if ( $(popId).find("iframe").length ) {
				$(popId).find("iframe").each(function(i){
					var iv = $(this);
					if ( iv.is(":visible") && iv.data('autoplay') == 'yes' ) {
						var src = iv.attr('src') + '&autoplay=1';
						iv.attr('src', src);
					}
				});
			}
				
			$is_popup = 1;
			current_popup = elID;

		});
	});
	
	$('body').on('click', '.ib2-close-pop', function(e){
		$('body').trigger('popupclose');
		e.preventDefault();
	});
	
	$(document).keyup(function(e) {
		if ( e.keyCode == 27 && $is_popup == 1 ) {
			$('body').trigger('popupclose');
		}
	});
	
	$('body').on('click', '.ib2-popup-bg', function(e){
		$('body').trigger('popupclose');
	});
	
	$('body').on('popupclose', function(){
		var elID = current_popup;
		
		$('.ib2-popup-bg').fadeOut("medium");
		$('#' + elID + '-popup, .ib2-popup').fadeOut("medium");
		
		if ( $('#' + elID + '-popup').find("iframe").length ) {
			$('#' + elID + '-popup').find("iframe").each(function(i){
				var iv = $(this);
				var src = iv.attr('src');
				src = src.replace('&autoplay=1', '');
				iv.attr('src', src);
			});
		}
				
		$is_popup = 0;
		current_popup = '';
	});
	
	// ================== HOTSPOT ======================
	$('[data-toggle*="tooltip"]').tooltip();
	$('[data-toggle*="popover"]').popover();
	
	if ( $('[data-toggle*="popup"]').length ) {
		$('[data-toggle*="popup"]').each(function(){
			var $this = $(this), id = $this.attr('id');
			if ( $this.data('trigger') == 'hover' ) {
				$('#' + id).bind('mouseover', function(e){
					hotspot_popup(id);
				});
			} else if ( $this.data('trigger') == 'click' ) {
				$('#' + id).bind('click', function(e){
					hotspot_popup(id);
					
					e.preventDefault();
				});
			}
		});	
	}
	
	function hotspot_popup( elID ) {
		var $this = $(this), options = {},
		anim = '', animate = false;
		
		var popId = '#' + elID + '-popup';
		if ( $(popId).hasClass('ib2-box-animation') && $(popId).attr('data-animation') ) {
			anim = $(popId).data('animation');
			animate = true;
		}
		
		if ( $is_popup == 1 ) return false;
		
		if ( $("iframe").length ) {
			$("iframe").each(function(i){
				var iv = $(this);
				if ( iv.is(":visible") && iv.data('autoplay') == 'yes' ) {
					var src = iv.attr('src');
					src = src.replace('&autoplay=1', '');
					iv.attr('src', src);
				}
			});
		}
			
		ib2_popup_center( $(popId) );
		
		if ( anim === "scale" ) {
			options = { percent: 100 };
		}

		$('.ib2-popup-bg').fadeIn("medium");
		
		if ( animate == true ) {
			window.setTimeout(function(){
				$(popId).show(anim, options, 500);
			}, 500);
		} else {
			$(popId).fadeIn("slow");
		}

		if ( $(popId).find("iframe").length ) {
			$(popId).find("iframe").each(function(i){
				var iv = $(this);
				if ( iv.is(":visible") && iv.data('autoplay') == 'yes' ) {
					var src = iv.attr('src') + '&autoplay=1';
					iv.attr('src', src);
				}
			});
		}
			
		$is_popup = 1;
		current_popup = elID;
	}
	
	// ================== SLIDER ======================
	
	if ( ib2_slider == 1 ) {
		$(window).load(function(){
			var options = {}, anim = '', animate = false;
			if ( $is_slider == 1 ) return false;
			
			if ( $('#ib2-bottom-slider > .ib2-slider-el').hasClass('ib2-box-animation') && $('#ib2-bottom-slider > .ib2-slider-el').attr('data-animation') ) {
				anim = $('#ib2-bottom-slider > .ib2-slider-el').data('animation');
				animate = true;
			}
		
			if ( anim === "scale" ) {
				options = { percent: 100 };
			}
			
			if ( animate == true ) {
				$('#ib2-bottom-slider').show();
				//$('#ib2-bottom-slider > .ib2-slider-el').show(anim, options, 500);

				var effectClass = animation_map(anim);
				$('#ib2-bottom-slider > .ib2-slider-el').removeClass('ib2-text-animation ib2-box-animation')
					.addClass('animated ' + effectClass);
			} else {
				$('#ib2-bottom-slider').effect('blind', { direction: 'up', mode: 'show' }, 1000);
			}
			
			if ( ib2_slider_close == 0 )  {
				var sliderCloseHtml = '<button type="button" class="btn btn-danger btn-sm close-bottom-slider"><i class="fa fa-times"></i></button>';
				$('.ib2-slider-el > .el-content').prepend(sliderCloseHtml);
			}
			
			var marginBottom = parseInt($('#ib2-bottom-slider').outerHeight()),
			currentMargin = parseInt($('body').css('marginBottom').replace('px', ''));
			marginBottom = marginBottom + currentMargin + 20;
			$('body').css('margin-bottom', marginBottom + 'px');
		
			$is_slider = 1;
		});
	}
	
	$('body').on('click', '.close-bottom-slider', function(e){
		var $this = $(this), sliderHeight = parseInt($('#ib2-bottom-slider').outerHeight()),
		marginBottom = parseInt($('body').css('marginBottom').replace('px', ''));
		
		var currentMargin = (marginBottom - sliderHeight) - 20;
		if ( currentMargin < 0 )
			currentMargin = 0;
			
		$('#ib2-bottom-slider').effect('slide', { direction: 'down', mode: 'hide' }, 500);
		$('body').css('margin-bottom', currentMargin + 'px');
		
		e.preventDefault();
	});
	
	// ================== COUNTDOWN ======================
	
	$(window).load(function(){
		if ( $('.ib2-countdown-el').length ) {
			$('.ib2-countdown-el').each(function(){
				var $this = $(this), mode = $this.data('mode'),
				target = $this.data('target'), cID = $this.attr('id'),
				end = $this.data('end'), style = $this.data('style');
				
				if ( mode == 'evergreen' || mode == 'cookie' ) {
					var newDate = new Date().valueOf(),
					parts = target.split(":"),
					day = parseInt(parts[0]),
					hour = parseInt(parts[1]),
					min = parseInt(parts[2]);
					
					if ( mode == 'cookie' ) {
						if ( $.cookie('ib2c-' + post_id) ) {
							newDate = parseInt($.cookie('ib2c-' + post_id));	
						} else {
							var expire = day + 30;
							$.cookie('ib2c-' + post_id, newDate, { expires: expire, path: '/' });
						}
					}
					
					var newValue = day + hour + min, target = newDate + newValue;
				} else {
					 var offset = $this.data('tz');
					 var origTime = moment.tz(target, offset);
					 var utc = origTime.clone().tz("UTC");
					 var localTime = moment.utc(utc).toDate();
					 target = moment(localTime).format('YYYY/MM/DD HH:mm:ss');
				}
				
				var beforeText = '', afterText = '';
				if ( style == 'text' ) {
					beforeText = $('#' + cID).data('before') != '' ? decodeURIComponent($('#' + cID).data('before')) + ' ' : '';
					afterText = $('#' + cID).data('after') != '' ? ' ' + decodeURIComponent($('#' + cID).data('after')) : '';
				}
				
				if ( style == 'circular' ) {
					var format = circular_countdown_format(cID);
					$('#' + cID).html(format);
				} else {
					var format = normal_countdown_format(cID, style);
					$('#' + cID).html(format);
				}
						
				$('#' + cID).countdown(target)
					.on('update.countdown', function(event) {
						if ( style == 'circular' ) {
							// update time object
								circular_update_timer(cID, event.offset.totalDays, event.offset.hours, event.offset.minutes, event.offset.seconds);
	
								if ( layerMinutes[cID] ) {
							    	circular_draw(cID);
							    } else {
							    	create_circular_countdown(cID);
							    }
						} else {
							normal_countdown_update(cID, event.offset.totalDays, event.offset.hours, event.offset.minutes, event.offset.seconds);
						}
					})
					.on('finish.countdown', function(event) {
						if ( mode == 'cookie' || mode == 'date' ) {
							if ( end == 'redirect' ) {
								var url = decodeURIComponent($this.data('url'));
								if ( url != '' ) {
									window.location.href = url;
								}
							} else if ( end == 'hide' ) {
								$this.hide();
							} else if ( end == 'reveal' ) {
								$this.hide();
								$('#' + cID + '-content').show();
							}
						}
					});
	
			});
		}
	});
	// ================== EMAIL VALIDATION ======================
	$('.ib2-validate-email').each(function(){
		$(this).keyup(function(){
			ib2_email_validation($(this));
		});
		
		$(this).blur(function(){
			ib2_email_validation($(this));
		});
	});
	
	$('.ib2-required').each(function(){
		$(this).keyup(function(){
			ib2_required_validation($(this));
		});
		
		$(this).blur(function(){
			ib2_required_validation($(this));
		});
	});
	
	$('form').each(function(){
		$(this).submit(function(e){
			var $this = $(this), reqpass = 0;
			$this.find('.ib2-required').each(function(){
				if ( $(this).is(":visible") ) {
					if ( !ib2_required_validation($(this)) ) reqpass += 1;
				}
			});
			
			if ( $this.find('.ib2-validate-email').length && $this.find('.ib2-validate-email').is(":visible") )
				if ( !ib2_email_validation($this.find('.ib2-validate-email')) ) reqpass += 1;
	
			if ( reqpass > 0 ) return false;
			else return true;
		});
	});
	
	function ib2_email_validation( element ) {
		if ( !ib2_validate_email(element.val()) ) {
			element.addClass('ib2-field-error');
			if ( !element.parent().find('.ib2-field-error-txt').length ) {
				element.parent().append('<span class="ib2-field-error-txt"></span>');
			}
			element.parent().find('.ib2-field-error-txt').text('Please enter a valid email address.');
			return false;
		} else {
			element.removeClass('ib2-field-error');
			element.parent().find('.ib2-field-error-txt').remove();
		}
		return true;
	}

	function ib2_required_validation( element ) {
		if ( element.val() == '' ) {
			element.addClass('ib2-field-error');
			if ( !element.parent().find('.ib2-field-error-txt').length ) {
				element.parent().append('<span class="ib2-field-error-txt"></span>');
			}
			element.parent().find('.ib2-field-error-txt').text('This field is required.');
			return false;
		} else {
			element.removeClass('ib2-field-error');
			element.parent().find('.ib2-field-error-txt').remove();
		}
		return true;
	}

	function ib2_validate_email( value ) {
		return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(value);
	}

	function ib2_popup_center( element ) {
		var pos  = 'fixed';
		var top  = (($(window).height() / 2) - (element.outerHeight() / 2));
		var left = (($(window).width() / 2) - (element.outerWidth() / 2));

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
    
    function circular_update_timer( elID, days, hours, mins, secs ) {
    	if ( !circularTime[elID] || circularTime[elID] == null || typeof circularTime[elID] === "undefined" )
			circularTime[elID] = {};
    							
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
    
    function animation_map( effect ) {
    	switch ( effect ) {
    		case 'blind':
    			effect = 'fadeInDown';
    			break;
    		case 'clip':
    			effect = 'fadeInUp';
    			break;
    		case 'pulsate':
    			effect = 'flash';
    			break;
    		case 'drop':
    			effect = 'bounceInDown';
    			break;
    		case 'slide':
    			effect = 'fadeInLeft';
    			break;
    		case 'scale':
    			effect = 'zoomIn';
    			break;
    		case 'explode':
    			effect = 'rubberBand';
    			break;
    		case 'highlight':
    			effect = 'lightSpeedIn';
    			break;
    		case 'puff':
    			effect = 'tada';
    			break;
    		case 'fold':
    			effect = 'flip';
    			break;
    	}
    	
    	return effect;
    }
})(jQuery);