;(function ($) {

	$.LP_Certificates = function () {

	}
	$.LP_Certificates.Model = Backbone.Model.extend({
		initialize: function () {

		}
	});
	$.LP_Certificates.View = Backbone.View.extend({
		model          : $.LP_Certificates.Model,
		el             : '#learn-press-cert-wrap',
		events         : {
			'click a.download': '_download',
			'click a.print'   : '_print'
		},
		systemFonts    : ['Arial', 'Georgia', 'Helvetica', 'Verdana'],
		viewport       : {
			width         : 0,
			height        : 0,
			templateWidth : 0,
			templateHeight: 0,
			ratio         : 1
		},
		initialize     : function (options) {
			_.bindAll(this, 'addLayer', 'createLayer', '_updateViewport', '_onLoadTemplate', '_initViewport', 'updateViewport', '_download');
			$(window).on('resize.learn-press-cert-designer', this._updateViewport);
			if (options && options.$el) {
				this.setElement(options.$el);
			}
			var elements = this.elements = {
				viewport  : $.proxy(function () {
					return this.$('#cert-design-viewport')
				}, this),
				background: $.proxy(function () {
					var $tmpl = this.$('img.cert-template');
					if ($tmpl.length == 0) {
						$tmpl = $('<img class="cert-template" />');
						this.elements.viewport().prepend($tmpl);
					}
					return $tmpl;
				}, this)
			}
			elements.background().load(this._onLoadTemplate).trigger('load');

			this.render();
		},
		_onLoadTemplate: function (e) {
			var tester = new Image(),
				$img = this.elements.background();
			tester.src = $img.attr('src');
			this.viewport = {
				width         : $img.width(),
				height        : $img.height(),
				templateWidth : tester.width,
				templateHeight: tester.height,
				ratio         : $img.width() / tester.width
			}
			this.model.set('template', tester.src);
			this._initViewport();
		},
		_initViewport  : function () {
			var that = this;

			if (!this.$canvas) {
				this.$canvas = new fabric.Canvas($('canvas', this.$el).get(0), this.model.get('layers'));
				_.each(this.model.get('layers'), function (layer) {
					if (!layer.type) return;
					var $layer = this.addLayer(layer, {setActive: false}),
						fontFamily = $layer.fontFamily;
					// only update font if it is not a system font
					if ($.inArray(fontFamily, this.systemFonts) == -1) {
						fontFamily = "::" + fontFamily;
						this.setLayerProp($layer, 'fontFamily', fontFamily)
					}
					this.$canvas.renderAll();
				}, this);
			}
			this.model.get('template') && fabric.Image.fromURL(this.model.get('template'), function (img) {
				that.$canvas.backgroundImage = img;
				$(window).trigger('resize.learn-press-cert-designer');
			});


		},
		_download      : function (e) {
			e.preventDefault();
			var $button = $(e.target),
				type = $button.data('type'),
				name = $button.data('name'),
				args = {
					format    : type == 'jpg' ? 'jpeg' : 'png',
					multiplier: 1 / this.$canvas.getZoom()
				},
				data = null;
			if (args.type == 'jpeg') {
				args.quality = 1
			}
			$('.cert-design-actions').addClass('loading');

			data = this.$canvas.toDataURL(args);
			var _t = new Date().getTime(),
				_n = 500000,
				_m = Math.ceil(data.length / _n),
				_done = 0;

			for (var i = 1; i <= _m; i++) {
				var _data = data.substr((i - 1) * _n, _n);

				$.ajax({
					url     : ajaxurl,
					dataType: 'text',
					type    : 'post',
					data    : {
						action       : 'learn-press-cert-download',
						download_cert: {
							data: _data,
							name: name,
							type: type,
							t   : _t,
							i   : i,
							m   : _m
						}
					},
					success : function (response) {
						_done++;
						if (_done == _m) {
							$.ajax({
								url     : ajaxurl,
								dataType: 'text',
								type    : 'post',
								data    : {
									action       : 'learn-press-cert-download',
									download_cert: {
										name   : name,
										type   : type,
										t      : _t,
										m      : _m,
										combine: 1
									}
								},
								success : function () {
									$('.cert-design-actions').removeClass('loading');
									if (typeof cert_url == 'undefined') {
										var cert_url = window.location.href;
									}
									window.location.href = cert_url.replace(/\?(.*)$/, '') + '?download_cert=' + name + '&type=' + type
								}
							});
						}
					}
				});
			}

			//this.download(data, name + '.' + type);
		},
		_print         : function (e) {
			e.preventDefault();
			var that = this;
			$('.cert-design-actions, .learn-press-message').addClass('hide-all-content');
			this.$('.cert-template').css('opacity', 0);
			$('body').children().not('.learn-press-cert-preview').addClass('hide-all-content');
			$('.socials-sharing').addClass('hide-all-content');
			window.print();

			setTimeout(function () {
				$('.cert-design-actions, .learn-press-message').removeClass('hide-all-content');
				that.$('.cert-template').css('opacity', '');
				$('body').children().not('.learn-press-cert-preview').removeClass('hide-all-content');
				$('.socials-sharing').removeClass('hide-all-content');


			}, 450);
		},
		download       : function (url, name) {

			var $form = $('#learn-press-form-download-cert');
			$form.find('#download_cert_data').remove();
			$form.append($('<input type="hidden" id="download_cert_data" name="download_cert[data]" value="' + url + '" />'))
			$form.submit();
		},
		createLayer    : function (args) {
			var defaults = $.extend({
					fontSize  : 24,
					left      : 0,
					top       : 0,
					lineHeight: 1,
					originX   : 'left',
					fontFamily: 'Arial',
					name      : '',
					fieldType : 'custom'
				}, args),
				text = args.text || '',
				$object = new fabric.Text(text, defaults);
			$object.set({
				hasControls: false
			});
			_.each(defaults, function (v, k) {
				$object.set(k, v);
			});
			$object.selectable = false;
			var $_object = $(document).triggerHandler('learn_press_certificate_layer_obj', [$object, args]);
			if (typeof $_object == 'object') {
				$object = $_object;
			}
			return $object;
		},
		addLayer       : function ($layer, args) {
			args = $.extend({
				setActive: true
			}, args || {});
			if ($.isPlainObject($layer)) {
				$layer = this.createLayer($layer);
			}
			this.$canvas.add($layer);
			if (args.setActive) {
				this.$canvas.setActiveObject($layer);
			}
			this.$canvas.renderAll();
			return $layer;
		},
		setLayerProp   : function ($layer, prop, value) {
			var options = {};
			switch (prop) {
				case 'textAlign':
					$layer.originX = value;
					break;
				case 'color':
					$layer.set('fill', value);
					break;
				case 'scaleX':
				case 'scaleY':
					if (value < 0) {
						if (prop == 'scaleX') {
							$layer.flipX = true;
						} else {
							$layer.flipY = true;
						}
					} else {
						if (prop == 'scaleX') {
							$layer.flipX = false;
						} else {
							$layer.flipY = false;
						}
					}
					options[prop] = this.toFixed(Math.abs(value));
					break;
				case 'fontFamily':
					if (value.match(/^::/)) {
						this.loadGoogleFont(value.replace(/^::/, ''), $layer, function (font, $object) {
							if (!$object) $object = this.$canvas.getActiveObject();
							$object.set('fontFamily', font);
							setTimeout($.proxy(function () {
								this.$canvas.renderAll();
							}, this), 450)
						})
						break;
					}
				default:
					options[prop] = value;
			}
			_.each(options, function (v, k) {
				$layer.set(k, v)
			})
			$layer.setCoords();
		},
		updateViewport : function () {
			var $img = this.elements.background();
			this.viewport = $.extend(this.viewport, {
				width : $img.width(),
				height: $img.height(),
				ratio : $img.width() / this.viewport.templateWidth
			});
			this.$canvas.setHeight(this.viewport.height);
			this.$canvas.setWidth(this.viewport.width);

			this.$canvas.setZoom(this.viewport.ratio);
			this.$canvas.calcOffset();
			this.$canvas.renderAll();

			this.updateRulers();
			this.$('.canvas-container').css('position', 'absolute');

		},
		_updateViewport: function () {
			this._updateViewportTimeout && clearTimeout(this._updateViewportTimeout);
			this._updateViewportTimeout = setTimeout(this.updateViewport, 300);
		},
		updateRulers   : function () {

		},
		loadGoogleFont : function (font, $object, callback) {
			var that = this,
				id = 'google-font-' + font.replace(/\s+/, '-').toLowerCase(),
				$link = $('link#' + id);
			if ($link.length) {
				$.isFunction(callback) && callback.call(that, font, $object);
			} else {
				$link = $('<link id="' + id + '" href="http://fonts.googleapis.com/css?family=' + font.replace(/\s+/, '+') + '" rel="stylesheet" type="text/css" />')
					.appendTo($('head'))
					.load(function () {
						$.isFunction(callback) && callback.call(that, font, $object);
					});
			}
		}
	});

	var LP_Model_Certificates = LP_View_Certificates = null;

	$(document).ready(function () {
		if (typeof cert_data == 'undefined' || LP_Model_Certificates) {
			return;
		}
		LP_Model_Certificates = window.LP_Model_Certificates = new $.LP_Certificates.Model(cert_data);
		LP_View_Certificates = window.LP_View_Certificates = new $.LP_Certificates.View({model: LP_Model_Certificates});
	});

})(jQuery);